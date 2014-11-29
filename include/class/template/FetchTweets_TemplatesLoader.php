<?php
/**
    Loads template components.
    
    @package        Fetch Tweets
    @copyright      Copyright (c) 2013, Michael Uno
    @authorurl      http://michaeluno.jp
    @license        http://opensource.org/licenses/gpl-2.0.php GNU Public License
    @since          2.3.9
*/

/**
 * Loads active template components.
 * 
 * @since       2.3.9
 */
class FetchTweets_TemplatesLoader {

    /**
     * Indicates whether the class is already loaded or not.
     * 
     */
    static private $_bLoaded = false;
    /**
     * Loads active template components.
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
     * Includes activated templates' functions.php files.
     * 
     * @remark      This is called from the initial loader class.
     */     
    public function loadFunctionsOfActiveTemplates() {
        
        $this->_loadFileOfActiveTemplatesByFileName( 'functions.php' );
        
    }
    
    /**
     * Includes activated templates' settings.php files.
     * 
     * @remark      This is called from the initial loader class.
     */ 
    public function loadSettingsOfActiveTemplates() {
        
        if ( ! is_admin() ) { return; }                        
                                
        if ( ! FetchTweets_PluginUtility::isInPluginAdminPage() ) { return; }
        
        $this->_loadFileOfActiveTemplatesByFileName( 'settings.php' );

    }   
        
        static private $_aLoaded = array() ;
        /**
         * Loads the file of active template of the given file name.
         * 
         * @since       2.3.9
         * @param       string      $sFileName      The file base name with file extension to load.
         * @param       string      $sMethod        The method to load. Either 'include' or 'enqueue_style' is accepted. Use 'enqueue_style' for styles.
         */
        private function _loadFileOfActiveTemplatesByFileName( $sFileName='functions.php', $sMethod='include' ) {
            
            $_oOption = FetchTweets_Option::getInstance();
            foreach( $_oOption->getActiveTemplates() as $_aTemplate ) {
                            
                $_oTemplate = new FetchTweets_Template( $_aTemplate['sSlug'] );
                $_sFilePath = $_oTemplate->getPathByFileName( $sFileName );
                if ( ! $_sFilePath ) {
                    continue;
                }
                if ( in_array( $_sFilePath, self::$_aLoaded ) ) {
                    continue;
                }
                self::$_aLoaded[ $_sFilePath ] = $_sFilePath;
                
                switch( $sMethod ) {
                    default:
                    case 'include': 
                        include( $_sFilePath );
                        break;
                    case 'enqueue_style':
                        wp_register_style( 
                            "fetch-tweets-" . md5( $_aTemplate['sDirPath'] ),
                            FetchTweets_WPUtilities::getSRCFromPath( $_sFilePath ) 
                        );
                        wp_enqueue_style( "fetch-tweets-" . md5( $_aTemplate['sDirPath'] ) );
                        break;
                }
                            
            }
        }
    /**
     * 
     * @since       2.3.6
     */
    public function loadStylesOfActiveTemplates() {
        add_action( 'wp_enqueue_scripts', array( $this, '_replyToEnqueueActiveTemplateStyles' ) );
    }    
        /**
         * Enqueues active template CSS files.
         * 
         * @remark      This must be called after the option object has been established.
         * @since       2.3.6       
         * @since       2.3.9       Changed the name from `enqueueActiveTemplateStyle`.
         */
        public function _replyToEnqueueActiveTemplateStyles() {
            
            $this->_loadFileOfActiveTemplatesByFileName( 'style.css', 'enqueue_style' );
            return;

        }    
    
}