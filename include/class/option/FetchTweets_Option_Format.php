<?php
/**
 * Fetch Tweets
 * 
 * Fetches and displays tweets from twitter.com.
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 */

/**
 * Provides option formatting methods.
 * 
 * @since       2.3.9       Moved some properties and methods from the base class.
 */
abstract class FetchTweets_Option_Format extends FetchTweets_Option_Templates {
    
    protected static $aStructure_Options = array(        
        'authentication_keys'       => array(
            'consumer_key'      => '',
            'consumer_secret'   => '',
            'access_token'      => '',
            'access_secret'     => '',
            'screen_name'       => '',
            'user_id'           => '',        
            'is_connected'      => null,    // do not set a default value here as it will be checked if the value is set or not later
            'connect_method'    => 'manual',
        ),
        'twitter_connect'           => array(
            // do not set 'consumer_key' and the 'consumer_secret' key so that third-party scripts can determine the connection method by checking the existence of the keys.
            'access_token'      => '',
            'access_secret'     => '',
            'screen_name'       => '',
            'user_id'           => '',
            'is_connected'      => null,    // do not set a default value here as it will be checked if the value is set or not later
            'connect_method'    => 'oauth',
        ),
        'default_values'            => array(),
        'capabilities'              => array(),
        'cache_settings'            => array(
            'cache_for_errors'  => false,
            'caching_mode'      => 'normal',    // 2.1+
            'clearing_interval' => array(   // 2.5.0+
                'size'  => 7,
                'unit'  => 86400,
            ),
        ),
        'search'                    => array(
            'is_searchable'    => false,
        ),
        'arrTemplates'              => array(),    // stores template info arrays.
        'arrDefaultTemplate'        => array(),    // stores the default template info.
        
        // 2.4.5+
        'content_security_policy'   => array(
            'disable_warnings' => false,
        ),
        
        // 2.4.8+
        'sensitive_material'        => array(
            'possibly_sensitive'            => 'do_nothing', // 'remove', 'replace_media_with_message'          
        ),
        
        // 2.5.0+
        'delete'                    => array(
            'delete_upon_uninstall'    => false,
        ),
        
        // 2.5.1+
        'oembed'                    => array(
            'discover'          => false,
            'cache_discover'    => true,
            'cache_duration'    => array(
                'size'  => 1,
                'unit'  => 86400,
            ),
        ),
        
    );
    
    public $aStructure_DefaultParams = array(
        'tweet_type'            => null,    // this will be set in the add/edit rule page
        
        'id'                    => null,
        'ids'                   => null,    // deprecated as of 1.0.0.4 - but extension plugins may use it
        'tag'                   => null,
        'tags'                  => null,    // deprecated as of 1.0.0.4 - but extension plugins may use it
        'count'                 => 20,
        // 'avatar_size'        => 48,
        'operator'              => 'IN',    // 2.5.0 Changed the default value from `AND`
        'tag_field_type'        => null,    // 2.5.0 Changed the default value from `slug`. used internally. slug or id.
        'sort'                  => 'descending',        //  ascending, descending, or random 
        // 'template'           => null,    // the template slug
        
        // for custom function calls
        'q'                     => null,    
        'screen_name'           => null,    
        'include_rts'           => 0,       
        'exclude_replies'       => 0,       
        'cache'                 => 1200,     // Cache lifespan in seconds.
        'lang'                  => null,    
        'result_type'           => 'mixed',  
        'until'                 => '',       // since 1.3.3
        'geocode'               => '',       // since 1.3.3 - this is for shortcode parametrs while geocentric_coordinate and geocentric_radius are for the meta box options.
        'geocentric_coordinate' => array(    // since 1.3.3
            'latitude'  => '',
            'longitude' => '',
        ),
        'geocentric_radius'     => array(    // since 1.3.3
            'size' => '',
            'unit' => 'mi',
        ),
        'custom_query'          => null,    // 2.5.0+
        
        // 1.2.0+
        'list_id'               => null,    
        'twitter_media'         => true,
        'external_media'        => true,
        
        // 2+
        'account_id'            => null,    // do not set the default ID of 0 here. The fetching method will check if the value is set and if so, it considers as the home timeline tweet type.
        
        // 2.4.7+
        'show_error_on_no_result'       => true,
        
        // 2.4.8
        'apply_template_on_no_result'   => true,
        
        // 2.5.0+
        'force_caching'          => false,   // (boolean) whether to force updating the cache. Used in the background cache renewal processes.
        
        
    );
    public $aStructure_DefaultTemplateOptions = array(
        // leave them null and let each template define default values.
        'template'              => null,    // the template slug
        'avatar_size'           => null,    // 48, 
        'width'                 => null,    // 100,    
        'width_unit'            => null,    // '%',    
        'height'                => null,    // 800,
        'height_unit'           => null,    // 'px',
    );          
    
   /**
     * Represents the template array structure stored in the option array.
     * 
     * @since       2.3.9       Dropped the function, thumbnail, CSS, template, settings path as they can be calculated from the relative path.
     */
    public static $aStructure_Template = array(
        'sDirPath'              => null,
        'sRelativeDirPath'      => null,        // the relative path to ABSPATH
        'sSlug'                 => null,
        'sOldSlug'              => null,
        'sName'                 => null,
        'sDescription'          => null,
        'sVersion'              => null,
        'sAuthor'               => null,
        'sAuthorURI'            => null,
        'bIsActive'             => null,
        'bIsDefault'            => null,
        'iIndex'                => null,
    );
    
    /**
     * Represents the template array structure stored in the option array.
     * 
     */
    public static $aStructure_Template_Legacy = array(                
    
        // these are absolute paths
        'strDirPath'            => null,
        'strCSSPath'            => null,
        'strFunctionPath'       => null,
        'strTemplatePath'       => null,
        'strSettingsPath'       => null,
        'strThumbnailPath'      => null,
        
        // these are relative paths to ABSPATH.    2.3.5+
        'strDirRelativePath'    => null,        // the relative directory path to the WordPress installed directory.
        
        // template info
        'strSlug'               => null,        // the md5 hash of the absolute path of the directory.
        
        'strName'               => null,
        'strDescription'        => null,
        'strTextDomain'         => null,
        'strDomainPath'         => null,
        'strVersion'            => null,
        'strAuthor'             => null,
        'strAuthorURI'          => null,
        
        // flags
        'fIsActive'             => null,
        'fIsDefault'            => null,
        'intIndex'              => null,
        
    );
        
    
    
    
    /*
     * Sets the options array.
     * 
     * Back end methods
     * */
    protected function setOption( $sOptionKey ) {
        
        // Flags
        $_bOptionsModified = false;
        
        // Set up the options array.
        $_aOptions   = $this->getAsArray( get_option( $sOptionKey, array() ) );
        $aOptions    = $this->uniteArrays( $_aOptions, self::$aStructure_Options );     
        
        // If the v1 option array structure is present, format the options for backward compatibility
        if ( isset( $aOptions['fetch_tweets_settings'] ) || isset( $aOptions['fetch_tweets_templates'] ) ) {
            $aOptions = $this->_convertV1OptionsToV2( $aOptions );
            $_bOptionsModified = true;
        }

        // If the template option array is empty, retrieve the active template arrays.
        if ( empty( $aOptions[ 'arrTemplates' ] ) ) {
            $_aDefaultTemplate = $this->findDefaultTemplateDetails();
            $aOptions['arrTemplates'][ $_aDefaultTemplate['sSlug'] ] = $_aDefaultTemplate;
            $aOptions['arrDefaultTemplate'] = $_aDefaultTemplate;
            $_bOptionsModified = true;
        } 
        
        // 2.3.9+ If the activated templates arrays are old, update them.
        foreach( $aOptions['arrTemplates'] as $_sOldSlug => &$aTemplate ) {
            
            // the 'sSlug' key is added since v2.3.9 so if it is set, the structure is up to date.
            if ( isset( $aTemplate['sSlug'] ) ) { 
                continue; 
            }
            
            unset( $aOptions['arrTemplates'][ $_sOldSlug ] );
            $_aTemplate = $this->_formatTemplateArray( $aTemplate );
            $aOptions['arrTemplates'][ $_aTemplate['sSlug'] ] = $_aTemplate;
            $_bOptionsModified = true;
            
        }
        if ( ! isset( $aOptions['arrDefaultTemplate']['sSlug'] ) ) {
            $_aDefaultTemplate = $this->_formatTemplateArray( $aOptions['arrDefaultTemplate'] );
            $aOptions['arrDefaultTemplate'] = $_aDefaultTemplate
                ? $_aDefaultTemplate
                : $this->findDefaultTemplateDetails();
            $_bOptionsModified = true;
        }
        
        
        if ( $_bOptionsModified ) {
            $this->saveOptions( $aOptions );
        }
        return $aOptions;
                
    }
    
    /**
     * Finds the default template and retrieves the detail information of the template.
     * 
     * This is used when no default template is set.
     * 
     * @return       array      The default template array
     */
    public function findDefaultTemplateDetails( $sDirPath='' ) {    
        
        static $_aDefault = array();
        if ( isset( $_aDefault[ $sDirPath ] ) ) {
            return $_aDefault[ $sDirPath ];
        }
        $sDirPath = $sDirPath
            ? $sDirPath
            : FetchTweets_Commons::getPluginDirPath() . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'plain';
        $_aDefaultTemplate = $this->getTemplateArrayByDirPath( $sDirPath );
        $_aDefaultTemplate[ 'bIsActive' ]  = true;
        $_aDefaultTemplate[ 'bIsDefault' ] = true;    
        $_aDefaultTemplate[ 'fIsActive' ]  = true;     // backward compatibility
        $_aDefaultTemplate[ 'fIsDefault' ] = true;     // backward compatibility
        $_aDefault[ $sDirPath ] = $_aDefaultTemplate;
        return $_aDefault[ $sDirPath ];
        
    }
        
    /**
     * Returns the template array by the given directory path.
     * @since       2.3.9
     * @scope       public       The template (singular) class also accesses this.
     */
    public function getTemplateArrayByDirPath( $sDirPath ) {
                    
        $_sRelativePath = str_replace( '\\', '/', untrailingslashit( FetchTweets_Utilities::getRelativePath( ABSPATH, $sDirPath ) ) );
        $_aData = array(
            'sDirPath'              => $sDirPath,
            'sRelativeDirPath'      => $_sRelativePath,
            'sSlug'                 => $_sRelativePath,
            'sOldSlug'              => md5( $sDirPath ),            
                
            // Backward compatibility
            'strDirPath'            => $sDirPath,
            'strDirRelativePath'    => $_sRelativePath,    // 2.3.5+
            'strSlug'               => md5( $sDirPath ),
        );
        return $this->_formatTemplateArray( $_aData );
        
    }        
        /**
         * Formats the template array.
         * 
         * Takes care of formatting change through version updates.
         * 
         * @since       2.3.9               
         * @return      array|boolean       Formatted template array. If the passed value is not an array 
         * or something wrong with the template array, false will be returned.
         */
        protected function _formatTemplateArray( $aTemplate ) {
         
            if ( ! is_array( $aTemplate ) ) { return false; }
            
            $aTemplate = $aTemplate + self::$aStructure_Template;
            
            // for backward compatibility
            $aTemplate = $this->_formatTemplateArrayLegacy( $aTemplate );
                       
            // format
            $aTemplate['sDirPath']          = isset( $aTemplate['sDirPath'] ) ? $aTemplate['sDirPath'] : $aTemplate['strDirPath'];
            $aTemplate['sRelativeDirPath']  = isset( $aTemplate['sRelativeDirPath'] ) ? $aTemplate['sRelativeDirPath'] : $aTemplate['strDirRelativePath'];
            $aTemplate['sSlug']             = isset( $aTemplate['sSlug'] ) ? $aTemplate['sSlug'] : $aTemplate['sRelativeDirPath'];
            $aTemplate['sOldSlug']          = isset( $aTemplate['sOldSlug'] ) ? $aTemplate['sOldSlug'] : $aTemplate['strSlug'];
            $aTemplate['sName']             = isset( $aTemplate['sName'] ) ? $aTemplate['sName'] : $aTemplate['strName'];
            $aTemplate['sDescription']      = isset( $aTemplate['sDescription'] ) ? $aTemplate['sDescription'] : $aTemplate['strDescription'];
            $aTemplate['sVersion']          = isset( $aTemplate['sVersion'] ) ? $aTemplate['sVersion'] : $aTemplate['strVersion'];
            $aTemplate['sAuthor']           = isset( $aTemplate['sAuthor'] ) ? $aTemplate['sAuthor'] : $aTemplate['strAuthor'];
            $aTemplate['sAuthorURI']        = isset( $aTemplate['sAuthorURI'] ) ? $aTemplate['sAuthorURI'] : $aTemplate['strAuthorURI'];        
            $aTemplate['bIsActive']         = isset( $aTemplate['bIsActive'] ) ? $aTemplate['bIsActive'] : $aTemplate['fIsActive'];
            $aTemplate['bIsDefault']        = isset( $aTemplate['bIsDefault'] ) ? $aTemplate['bIsDefault'] : $aTemplate['fIsDefault'];
            $aTemplate['iIndex']            = isset( $aTemplate['iIndex'] ) ? $aTemplate['iIndex'] : $aTemplate['intIndex'];
            
            // Check mandatory files. Consider the possibility that the user may directly delete the template files/folders.
            if ( ! FetchTweets_WPUtility::getReadableFilePath( $aTemplate['sDirPath'] . DIRECTORY_SEPARATOR . 'style.css', $aTemplate['sRelativeDirPath'] . DIRECTORY_SEPARATOR . 'style.css' ) ) {
                return false;
            }
            if ( ! FetchTweets_WPUtility::getReadableFilePath( $aTemplate['sDirPath'] . DIRECTORY_SEPARATOR . 'template.php', $aTemplate['sRelativeDirPath'] . DIRECTORY_SEPARATOR . 'template.php' ) ) {
                return false;
            }          
            return $aTemplate;
            
        }    
    
    
            /**
             * Make the passed template array compatible with the format of v2.3.8 or below.
             *
             * @return			array|false			The formatted template array or false if the necessary file paths do not exist.
             */
            private function _formatTemplateArrayLegacy( array $aTemplate ) {
                                
                $aTemplate = $aTemplate + self::$aStructure_Template_Legacy;
                
                $aTemplate['strDirPath'] = $aTemplate['strDirPath']	// check if it's not missing
                    ? $aTemplate['strDirPath']
                    : dirname( $aTemplate['strCSSPath'] );
                            
                $aTemplate['strTemplatePath'] = $aTemplate['strTemplatePath']	// check if it's not missing
                    ? $aTemplate['strTemplatePath']
                    : dirname( $aTemplate['strCSSPath'] ) . DIRECTORY_SEPARATOR . 'template.php';
                    
                $aTemplate['strDirRelativePath'] = $aTemplate['strDirRelativePath'] 
                    ? $aTemplate['strDirRelativePath']
                    : str_replace( '\\', '/', untrailingslashit( FetchTweets_Utilities::getRelativePath( ABSPATH, $aTemplate['strDirPath'] ) ) );                
        
                return $aTemplate;
                
            }    
    
        /**
         * Updates the format of options of v1 to v2.
         */
        private function _convertV1OptionsToV2( $aOptions ) {

            // Drop the page slug dimension.
            $_aOptions = FetchTweets_Utilities::uniteArrays(
                isset( $aOptions['fetch_tweets_settings'] ) ? $aOptions['fetch_tweets_settings'] : array(),
                isset( $aOptions['fetch_tweets_templates'] ) ? $aOptions['fetch_tweets_templates'] : array()
            );
            unset( $aOptions['fetch_tweets_settings'], $aOptions['fetch_tweets_templates'] );

            // For template options
            if ( isset( $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['top'] ) ) {
                $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings'][ 0 ] = $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['top'];
                unset( $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['top'] );
            }
            if ( isset( $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['right'] ) ) {
                $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings'][ 1 ] = $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['right'];
                unset( $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['right'] );
            }
            if ( isset( $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['bottom'] ) ) {
                $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings'][ 2 ] = $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['bottom'];
                unset( $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['bottom'] );
            }
            if ( isset( $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['left'] ) ) {
                $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings'][ 3 ] = $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['left'];
                unset( $_aOptions['fetch_tweets_template_plain']['fetch_tweets_template_plain_paddings']['left'] );
            }
            if ( isset( $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['top'] ) ) {
                $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings'][ 0 ] = $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['top'];
                unset( $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['top'] );
            }
            if ( isset( $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['right'] ) ) {
                $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings'][ 1 ] = $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['right'];
                unset( $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['right'] );
            }
            if ( isset( $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['bottom'] ) ) {
                $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings'][ 2 ] = $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['bottom'];
                unset( $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['bottom'] );
            }
            if ( isset( $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['left'] ) ) {
                $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings'][ 3 ] = $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['left'];
                unset( $_aOptions['fetch_tweets_template_single']['fetch_tweets_template_single_paddings']['left'] );
            }        

            // Credentials
            if ( isset( $_aOptions['authentication_keys'] ) ) {                
                $_aOptions['authentication_keys']['is_connected'] = ( $_aOptions['authentication_keys']['consumer_key'] && $_aOptions['authentication_keys']['consumer_secret'] && $_aOptions['authentication_keys']['access_token'] && $_aOptions['authentication_keys']['access_secret'] );
                $_aOptions['authentication_keys']['connect_method'] = 'manual';
            }
            if ( isset( $_aOptions['twitter_connect'] ) ) {
                $_aOptions['twitter_connect']['is_connected'] = ( $_aOptions['twitter_connect']['access_token'] && $_aOptions['twitter_connect']['access_secret'] );
                $_aOptions['twitter_connect']['connect_method'] = 'oauth';
            }
            
            return $_aOptions + $aOptions;
            
        }    
    
}