<?php
/**
	Handles templates that display fetched tweets.
	
	@package		Fetch Tweets
	@copyright		Copyright (c) 2013, Michael Uno
	@authorurl		http://michaeluno.jp
	@license     	http://opensource.org/licenses/gpl-2.0.php GNU Public License
	@since			1.1.0
*/

abstract class FetchTweets_Templates_Base extends FetchTweets_Templates_Utility {
	
    /**
     * Stores the self-instance.
     * 
     * @since       2.3.6
     */
    static public $oInstance = null;	    
    
	/**
	 * Represents the template array structure stored in the option array.
	 * 
	 */
	public static $aStructure_Template = array(				
	
		// these are absolute paths
		'strDirPath'			=> null,
		'strCSSPath'			=> null,
		'strFunctionPath'		=> null,
		'strTemplatePath'		=> null,
		'strSettingsPath'		=> null,
		'strThumbnailPath'		=> null,
		
		// these are relative paths to ABSPATH.	2.3.5+
		'strDirRelativePath'	=> null,
		
		// template info
		'strSlug'				=> null,
		'strName'				=> null,
		'strDescription'		=> null,
		'strTextDomain'			=> null,
		'strDomainPath'			=> null,
		'strVersion'			=> null,
		'strAuthor'				=> null,
		'strAuthorURI'			=> null,
		
		// flags
		'fIsActive'				=> null,
		'fIsDefault'			=> null,
		'intIndex'				=> null,
		
	);
    
    /**
     * Returns the instance of the class.
     * 
     * This is to ensure only one instance exists.
     * 
     * @since       2.3.6
     */
    static public function getInstance() {
            
        if ( isset( $GLOBALS['oFetchTweets_Templates'] ) && is_object( $GLOBALS['oFetchTweets_Templates'] ) ) {
            return $GLOBALS['oFetchTweets_Templates'];
        }
        
        $GLOBALS['oFetchTweets_Templates'] = new FetchTweets_Templates( FetchTweets_Commons::$sAdminKey );
        return $GLOBALS['oFetchTweets_Templates'];
        
    }     
    
    /**
     * Stores the flag indicating whether it has been loaded or not to prevent multiple instances.
     * 
     * @since   2.3.8
     */
    static public $_bLoaded = false;
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct() {

        if ( self::$_bLoaded ) {
            return;
        }
        self::$_bLoaded = true;
        
        $this->loadFunctionsOfActiveTemplates();
        $this->loadStylesOfActiveTemplates();		
        $this->loadSettingsOfActiveTemplates();

    }    
        
	/**
	 * Returns an array that holds arrays of activated template information.
	 * 
	 * The active templates are stored in the plugin's option array, which means stored in the database.
	 * 
	 */
	public function getActiveTemplates() {
		
        $_oOption = $GLOBALS['oFetchTweets_Option'];    // FetchTweets_Option::getInstance(); causes infinite function calls.
        
		// The default template (saved or dynamically generated)
		$_aDefaultTemplate = empty( $_oOption->aOptions['arrDefaultTemplate'] ) || ! @is_file( $_oOption->aOptions['arrDefaultTemplate']['strCSSPath'] )
			? $this->findDefaultTemplateDetails()
			: $_oOption->aOptions['arrDefaultTemplate'] + self::$aStructure_Template;
		
		// The saved active templates.
		$_aActiveTemplates = isset( $_oOption->aOptions['arrTemplates'] )
			? $_oOption->aOptions['arrTemplates']
			: array();
				
		// Add the default template to the activated template.
// TODO: change the key to use the md5 of relative path.		
		$_aActiveTemplates[ $_aDefaultTemplate['strSlug'] ] = $_aDefaultTemplate;
		
		// Format the template array.
		foreach( $_aActiveTemplates as $__sDirSlug => &$__aActiveTemplate ) {		
					
			$__aActiveTemplate = $this->_formatTemplateArray( $__aActiveTemplate );
			if ( ! $__aActiveTemplate ) {
				unset( $_aActiveTemplates[ $__sDirSlug ] );
			}
			$__aActiveTemplate['fIsActive'] = true;
			
		}
		
		return $_aActiveTemplates;
		
	}
		/**
		 * Formats the template array.
		 * 
		 * @since			2.3.5
		 * @return			array|false			The formatted template array or false if the necessary file paths do not exist.
		 */
		private function _formatTemplateArray( $aTemplate ) {
			
			if ( ! is_array( $aTemplate ) ) {
				return false;
			}
			
			$aTemplate = $aTemplate + self::$aStructure_Template;
			
			$aTemplate['strDirPath'] = $aTemplate['strDirPath']	// check if it's not missing
				? $aTemplate['strDirPath']
				: dirname( $aTemplate['strCSSPath'] );
						
			$aTemplate['strTemplatePath'] = $aTemplate['strTemplatePath']	// check if it's not missing
				? $aTemplate['strTemplatePath']
				: dirname( $aTemplate['strCSSPath'] ) . DIRECTORY_SEPARATOR . 'template.php';
				
			$aTemplate['strDirRelativePath'] = $aTemplate['strDirRelativePath'] 
				? $aTemplate['strDirRelativePath']
				: str_replace( '\\', '/', untrailingslashit( FetchTweets_Utilities::getRelativePath( ABSPATH, $aTemplate['strDirPath'] ) ) );                
				
			// Check mandatory files. Consider the possibility that the user may directly delete the template files/folders.
			if ( ! FetchTweets_WPUtilities::getReadableFilePath( $aTemplate['strDirPath'] . DIRECTORY_SEPARATOR . 'style.css', $aTemplate['strDirRelativePath'] . DIRECTORY_SEPARATOR . 'style.css' ) ) {
				return false;
			}
			if ( ! FetchTweets_WPUtilities::getReadableFilePath( $aTemplate['strDirPath'] . DIRECTORY_SEPARATOR . 'template.php', $aTemplate['strDirRelativePath'] . DIRECTORY_SEPARATOR . 'template.php' ) ) {
				return false;
			}
	
			return $aTemplate;
			
		}
		
	/**
	 * Retrieve templates and returns the template information as array.
	 * 
	 * This method is called for the template listing table to list available templates. So this method generates the template information dynamically.
	 * This method does not deal with saved options.
	 * 
	 */
	public function getUploadedTemplates() {
			
		// Construct a template array.
		$_aTemplates = array();
		$_iIndex = 0;		
		foreach ( $this->_getTemplateDirs() as $__sDirPath ) {
			
			// Check mandatory files.
			if ( ! @is_file( $__sDirPath . DIRECTORY_SEPARATOR . 'style.css' ) ) continue;
			if ( ! @is_file( $__sDirPath . DIRECTORY_SEPARATOR . 'template.php' ) ) continue;

// TODO: change the key and 'strSlug' to md5 of relative dir path.
			$_aTemplates[ md5( $__sDirPath ) ] = array(
					'strCSSPath' => $__sDirPath . DIRECTORY_SEPARATOR . 'style.css',
					'strDirPath' => $__sDirPath,
					'strFunctionPath' => @is_file( $__sDirPath . DIRECTORY_SEPARATOR . 'functions.php' ) ? $__sDirPath . DIRECTORY_SEPARATOR . 'functions.php' : null,			
					'strTemplatePath' => @is_file( $__sDirPath . DIRECTORY_SEPARATOR . 'template.php' ) ? $__sDirPath . DIRECTORY_SEPARATOR . 'template.php' : null,					
					'strSettingsPath' => @is_file( $__sDirPath . DIRECTORY_SEPARATOR . 'settings.php' ) ? $__sDirPath . DIRECTORY_SEPARATOR . 'settings.php' : null,	// this is optional.
					'strThumbnailPath' => $this->_getScreenshotPath( $__sDirPath ),	// it's not a url.
					'strSlug' => md5( $__sDirPath ),			
					'intIndex' => $_iIndex++,
				) 
				+ $this->_getTemplateData( $__sDirPath . DIRECTORY_SEPARATOR . 'style.css' ) 
				+ self::$aStructure_Template;
					
		}
		
		return $_aTemplates;
		
	}

	
	/**
	 * Finds the default template and retrieves the detail information of the template.
	 * 
	 * This is used when no default template is set.
	 * 
	 * @return			array				The default template array
	 */
	public function findDefaultTemplateDetails( $sDirPath=null ) {	
		        
		$sDirPath = isset( $sDirPath ) && $sDirPath
			? $sDirPath
			: FetchTweets_Commons::getPluginDirPath() . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'plain';

		return array(
				'fIsActive'				=> true,	// a default template must be active.
				'fIsDefault'			=> true,
				'strCSSPath'			=> $sDirPath . DIRECTORY_SEPARATOR . 'style.css',
				'strDirPath'			=> $sDirPath,
				'strDirRelativePath'	=> str_replace( '\\', '/', untrailingslashit( FetchTweets_Utilities::getRelativePath( ABSPATH, $sDirPath ) ) ),	// 2.3.5+
				'strFunctionPath'		=> @is_file( $sDirPath . DIRECTORY_SEPARATOR . 'functions.php' ) ? $sDirPath . DIRECTORY_SEPARATOR . 'functions.php' : null,					
				'strTemplatePath'		=> @is_file( $sDirPath . DIRECTORY_SEPARATOR . 'template.php' ) ? $sDirPath . DIRECTORY_SEPARATOR . 'template.php' : null,					
				'strSettingsPath'		=> @is_file( $sDirPath . DIRECTORY_SEPARATOR . 'settings.php' ) ? $sDirPath . DIRECTORY_SEPARATOR . 'settings.php' : null,	// this is optional.
				'strThumbnailPath'		=> $this->_getScreenshotPath( $sDirPath ),	// it's not a url.
// TODO: change the key and 'strSlug' to md5 of relative dir path.				
				'strSlug' 				=> md5( $sDirPath ),			
			) 
			+ $this->_getTemplateData( $sDirPath . DIRECTORY_SEPARATOR . 'style.css' )
			+ self::$aStructure_Template;		

	}
	
	/**
	 * 
	 * @since			2.3.0
	 */
	public function getDefaultTemplateName() {
		
		$_aDefaultTemplate = empty( $GLOBALS['oFetchTweets_Option']->aOptions['arrDefaultTemplate'] ) || ! @is_file( $GLOBALS['oFetchTweets_Option']->aOptions['arrDefaultTemplate']['strCSSPath'] )
			? $this->findDefaultTemplateDetails()
			: $GLOBALS['oFetchTweets_Option']->aOptions['arrDefaultTemplate'] + self::$aStructure_Template;
		return $_aDefaultTemplate['strName'];
		
	}
	
	public function getDefaultTemplateSlug() {
		
		$_aDefaultTemplate = empty( $GLOBALS['oFetchTweets_Option']->aOptions['arrDefaultTemplate'] ) || ! @is_file( $GLOBALS['oFetchTweets_Option']->aOptions['arrDefaultTemplate']['strCSSPath'] )
			? $this->findDefaultTemplateDetails()
			: $GLOBALS['oFetchTweets_Option']->aOptions['arrDefaultTemplate'] + self::$aStructure_Template;
// TODO: returns the md5 of relative path
		return $_aDefaultTemplate['strSlug'];		
		
	}
	
	public function getDefaultTemplatePath() {
			
		$_aDefaultTemplate = empty( $GLOBALS['oFetchTweets_Option']->aOptions['arrDefaultTemplate'] ) || ! @is_file( $GLOBALS['oFetchTweets_Option']->aOptions['arrDefaultTemplate']['strCSSPath'] )
			? $this->findDefaultTemplateDetails()
			: $GLOBALS['oFetchTweets_Option']->aOptions['arrDefaultTemplate'] + self::$aStructure_Template;
// TODO: return the template path after checking the file exists and if not, calculate with the relative dir path.		
		return $_aDefaultTemplate['strTemplatePath'];		
			
	}	
		
	/*
	 * Event methods 
	 * */
	/**
	 * Includes activated templates' functions.php files.
	 * 
	 * @remark			This is called from the initial loader class.
	 * 
	 */ 	
	public function loadFunctionsOfActiveTemplates() {
		
        $_aLoaded = array();
		foreach( $this->getActiveTemplates() as $__aTemplate ) {
						
			$_sFunctionsPath = FetchTweets_WPUtilities::getReadableFilePath( $__aTemplate['strFunctionPath'], $__aTemplate['strDirRelativePath'] . DIRECTORY_SEPARATOR . 'functions.php' );				
			if ( ! $_sFunctionsPath ) {
				continue;
			}
            if ( in_array( $_sFunctionsPath, $_aLoaded ) ) {
                continue;
            }
            $_aLoaded[] = $_sFunctionsPath;
			include( $_sFunctionsPath );
						
		}
		
	}
	
	/**
	 * Includes activated templates' settings.php files.
	 * 
	 * @remark			This is called from the initial loader class.
	 * 
	 */ 
	public function loadSettingsOfActiveTemplates() {
		
		if ( ! is_admin() ) { return; }
		                        
        if ( isset( $GLOBALS['pagenow'] ) && 'plugins.php' === $GLOBALS['pagenow'] ) {
            return;
        }                                
                                
        if ( ! FetchTweets_PluginUtility::isInPluginAdminPage() ) { return; }
        
        $_aLoaded = array();
		foreach( $this->getActiveTemplates() as $__aTemplate ) {
			
			$_sSettingsPath = FetchTweets_WPUtilities::getReadableFilePath( $__aTemplate['strSettingsPath'], $__aTemplate['strDirRelativePath'] . DIRECTORY_SEPARATOR . 'settings.php' );
			if ( ! $_sSettingsPath ) {
				continue;
			}
            if ( in_array( $_sSettingsPath, $_aLoaded ) ) {
                continue;
            }
            $_aLoaded[] = $_sSettingsPath;
			include( $_sSettingsPath );
						
		}
	}
	
	/**
	 * Enqueues active template CSS files.
	 * 
	 * @remark			This must be called after the option object has been established.
	 */
	public function enqueueActiveTemplateStyles() {

		foreach( $this->getActiveTemplates() as $__aTemplate ) {
			
			// Get the absolute path of the CSS file.
			$_sCSSPath = FetchTweets_WPUtilities::getReadableFilePath( $__aTemplate['strCSSPath'], $__aTemplate['strDirRelativePath'] . DIRECTORY_SEPARATOR . 'style.css' );
			if ( ! $_sCSSPath ) {
				continue;
			}
			
			wp_register_style( "fetch-tweets-{$__aTemplate['strSlug']}", FetchTweets_WPUtilities::getSRCFromPath( $_sCSSPath ) );		// relative path to the WordPress installed path.
			wp_enqueue_style( "fetch-tweets-{$__aTemplate['strSlug']}" );		
			
		}
		
	}
	
	/*
	 * MISC methods.
	 */
	/**
	 * Returns an array holding the labels(names) of activated templates.
	 * 
	 * This is used for the widget form or the template meta box to let the user select a template.
	 * 
	 */
	public function getTemplateArrayForSelectLabel( $aTemplates=null ) {
		
		if ( ! $aTemplates ) {
			$aTemplates = $this->getActiveTemplates();
		}
			
		$_aLabels = array();
		foreach ( $aTemplates as $__sDirSlug => $__aTemplate ) {
			if ( ! isset( $__aTemplate['strName'] ) ) continue;	// it may be broken.
			$_aLabels[ $__sDirSlug ] = $__aTemplate['strName'];
		}
		
		return $_aLabels;		
		
	}	

    /**
     * 
     * @since       2.3.6
     */
    public function loadStylesOfActiveTemplates() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueueActiveTemplateStyles' ) );
    }
   
    
}