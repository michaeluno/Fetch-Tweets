<?php
/**
 *    Handles the initial set-up for the plugin.
 *    
 * @package     Fetch Tweets
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        1.0.0
 * @since        1.3.4            Renamed to FetchTweets_Bootstrap from FetchTweets_InitialLoader
 * 
 * 
*/

final class FetchTweets_Bootstrap {
    
    /**
     * Stores the flag indicating whether it has been loaded or not to prevent multiple instances.
     */
    static public $_bLoaded = false;
    
    /**
     * Loads the plugin.
     */
    function __construct( $sPluginFilePath ) {
        
        if ( self::$_bLoaded ) {
            return;
        }
        self::$_bLoaded = true;
        
        
        // 0. Properties
        $this->_sFilePath   = $sPluginFilePath;    
        $this->_bIsAdmin    = is_admin();
        
        // 1. Define constants.
        // $this->_defineConstants();
        
        // 2. Set global variables.
        $this->_setGlobalVariables();
        
        // 3. Set up auto-load classes.
        $this->_include( $this->_sFilePath );
        
        // 4. Set up activation hook.
        register_activation_hook( $this->_sFilePath, array( $this, '_replyToDoWhenPluginActivates' ) );
        
        // 5. Set up deactivation hook.
        register_deactivation_hook( $this->_sFilePath, array( $this, '_replyToDoWhenPluginDeactivates' ) );
        
        // 6. Set up localization.
        $this->_localize();
        
        // 7. Check requirements.
        if ( $this->_bIsAdmin ) {
            add_action( 'admin_init', array( $this, '_replyToCheckRequirements' ) );
        }
        
        // 8. Schedule to call start up functions when all the plugins get loaded.
        add_action( 'plugins_loaded', array( $this, '_replyToLoadPluginComponents' ), 999, 1 );
            
    }    
    
    // private function _defineConstants() {}
    
    private function _setGlobalVariables() {
        
        $GLOBALS['oFetchTweets_Option']         = null;    // stores the option object
        
        // Stores custom registering class paths
        $GLOBALS['arrFetchTweets_FinalClasses'] = isset( $GLOBALS['arrFetchTweets_FinalClasses'] ) && is_array( $GLOBALS['arrFetchTweets_FinalClasses'] ) 
            ? $GLOBALS['arrFetchTweets_FinalClasses'] 
            : array();
        $GLOBALS['arrFetchTweets_Classes']      = isset( $GLOBALS['arrFetchTweets_Classes'] ) && is_array( $GLOBALS['arrFetchTweets_Classes'] ) 
            ? $GLOBALS['arrFetchTweets_Classes'] 
            : array();
                
        $GLOBALS['arrFetchTweets_oEmbed']       = array();        
                
    }
    
    private function _include( $sFilePath ) {
        
        $_sPluginDir =  dirname( $sFilePath );
        
        // Include the include list files.
        $_aBootClassFiles = array();
        include( $_sPluginDir . '/include/fetch-tweets-include-class-file-list-boot.php' );
        
        new FetchTweets_RegisterClasses( '', array(), $_aBootClassFiles );
        
        // Schedule to register regular classes when all the plugins are loaded. This allows other scripts to modify the loading class files.
        add_action( 'plugins_loaded', array( $this, '_replyToIncludeOtherFiles' ) );        
        
        FetchTweets_Commons::setUp( $sFilePath );
        
    }
        /**
         * Registers regular classes to be auto loaded.
         * 
         */
        public function _replyToIncludeOtherFiles() {
            
            $_sPluginDir = dirname( $this->_sFilePath );
            
            include( $_sPluginDir . '/include/library/admin-page-framework/fetch-tweets-admin-page-framework.min.php' );
            require( $_sPluginDir . '/include/library/TwitterOAuth/twitteroauth.php' );
            
            $_aClassFiles = array();
            $_aAdminClassFiles = array();
            include( $_sPluginDir . '/include/fetch-tweets-include-class-file-list.php' );
            if ( $this->_bIsAdmin ) {
                include( $_sPluginDir . '/include/fetch-tweets-include-class-file-list-admin.php' );
            }
            new FetchTweets_RegisterClasses( '', array(), $GLOBALS['arrFetchTweets_Classes'] + $_aClassFiles + $_aAdminClassFiles );
                        
        }
    /**
     * Checks if the server suffices for the plugin requirements.
     * 
     * @since            2.1
     */
    public function _replyToCheckRequirements() {

        new FetchTweets_Requirements( 
            $this->_sFilePath,
            array(
                'php' => array(
                    'version' => '5.2.4',
                    'error' => 'The plugin requires the PHP version %1$s or higher.',
                ),
                'wordpress' => array(
                    'version' => '3.3',
                    'error' => 'The plugin requires the WordPress version %1$s or higher.',
                ),
                'functions' => array(
                    'curl_version' => sprintf( __( 'The plugin requires the %1$s to be installed.', 'fetch-tweets' ), 'the cURL library' ),
                ),
                // 'classes' => array(
                    // 'DOMDocument' => sprintf( __( 'The plugin requires the <a href="%1$s">libxml</a> extension to be activated.', 'pseudo-image' ), 'http://www.php.net/manual/en/book.libxml.php' ),
                // ),
                'constants'    => array(),
            )
        );    
        
    }
        
    public function _replyToDoWhenPluginActivates() {

        // Schedule transient set-ups
        wp_schedule_single_event( time(), 'fetch_tweets_action_setup_transients' );        
  
    }
    
    public function _replyToDoWhenPluginDeactivates() {
        
        FetchTweets_WPUtilities::clearTransients();
        
    }    
    
    private function _localize() {
        
        load_plugin_textdomain( 
            FetchTweets_Commons::TextDomain, 
            false, 
            dirname( plugin_basename( $this->_sFilePath ) ) . '/language/'
        );
        
        if ( $this->_bIsAdmin ) {
            load_plugin_textdomain( 
                'admin-page-framework', 
                false, 
                dirname( plugin_basename( $this->_sFilePath ) ) . '/language/'
            );        
        }
        
    }        
    
    public function _replyToLoadPluginComponents() {
        
        // All the necessary classes have been already loaded.
                
        // 2. Option Object - the instantiation will handle the initial set-up
        FetchTweets_Option::getInstance();

        // 3. Load active templates - this must be done after loading the option class as it stores active templates.
        new FetchTweets_TemplatesLoader;
        
        // 4. Admin pages
        if ( $this->_bIsAdmin ) {
            new FetchTweets_AdminPage( FetchTweets_Commons::$sAdminKey, $this->_sFilePath );        
        }
        
        // 5. Post Type - no need to check is_admin() because posts of custom post type can be accessed from the front-end.
        new FetchTweets_PostType( FetchTweets_Commons::PostTypeSlug, null, $this->_sFilePath );     // post type slug
        
        // 6. Meta-boxes
        if ( $this->_bIsAdmin ) {
            $this->_registerMetaBoxes();
        }
                        
        // 7. Shortcode - enables the shortcode. e.g. [fetch_tweets id="143"]
        new FetchTweets_Shortcode( 'fetch_tweets' );    
            
        // 8. Widgets
        add_action( 'widgets_init', 'FetchTweets_WidgetByID::registerWidget' );
        add_action( 'widgets_init', 'FetchTweets_WidgetByTag::registerWidget' );
                
        // 9. Events - handles background processes.
        new FetchTweets_Event;    
        
        // 10. MISC
        if ( FetchTweets_PluginUtility::isInPluginAdminPage() ) {
            $GLOBALS['oFetchTweetsUserAds'] = isset( $GLOBALS['oFetchTweetsUserAds'] ) ? $GLOBALS['oFetchTweetsUserAds'] : new FetchTweets_UserAds;
        }
        
        // 11. WordPress version backward compatibility.
        $this->_defineConstantesForBackwardCompatibility();
        
    }

    /**
     * Registers plugin specific meta boxes.
     * 
     * @since            2.0.0
     */
    protected function _registerMetaBoxes() {
        
        if ( ! isset( $GLOBALS['pagenow'] ) ) { return; }
        if ( ! in_array( $GLOBALS['pagenow'], array( 'post-new.php', 'post.php' ) ) ) { return; }
        
        $_bIsUpdatePost = ( empty( $_GET ) && 'post.php' === $GLOBALS['pagenow'] );    // when saving the meta data, the GET array is empty
        $_sTweetType    = $this->_getTweetType();
        if ( 'search' == $_sTweetType || $_bIsUpdatePost ) {
            new FetchTweets_MetaBox_Search(
                'fetch_tweets_meta_box_search',    // meta box ID
                __( 'Tweets by Search', 'fetch-tweets' ),    // meta box title
                array( FetchTweets_Commons::PostTypeSlug ),    // post, page, etc.
                'normal',    // context
                'default'    // priority
            );
            new FetchTweets_MetaBox_Search_Advanced(    // [2.2+]
                'fetch_tweets_meta_box_search_advanced',    // meta box ID
                __( 'Tweets by Search Advanced Options', 'fetch-tweets' ),    // meta box title
                array( FetchTweets_Commons::PostTypeSlug ),    // post, page, etc.
                'normal',    // context
                'low'    // priority    // ('high', 'core', 'default' or 'low') 
            );
        }
        if ( 'home_timeline' == $_sTweetType || $_bIsUpdatePost ) {
            new FetchTweets_MetaBox_Timeline(
                'fetch_tweets_meta_box_timeline',    // meta box ID
                __( 'Tweets by Timeline', 'fetch-tweets' ),    // meta box title
                array( FetchTweets_Commons::PostTypeSlug ),    // post, page, etc.
                'normal',    // context
                'default'    // priority
            );
        }        
        if ( 'screen_name' == $_sTweetType || $_bIsUpdatePost ) {
            new FetchTweets_MetaBox_ScreenName(
                'fetch_tweets_meta_box_screen_name',    // meta box ID
                __( 'Tweets by Screen Name', 'fetch-tweets' ),    // meta box title
                array( FetchTweets_Commons::PostTypeSlug ),    // post, page, etc.
                'normal',    // context
                'default'    // priority
            );
        }
        if ( 'list' == $_sTweetType || $_bIsUpdatePost ) {            
            new FetchTweets_MetaBox_List(
                'fetch_tweets_meta_box_list',    // meta box ID
                __( 'Tweets by List', 'fetch-tweets' ),    // meta box title
                array( FetchTweets_Commons::PostTypeSlug ),    // post, page, etc.
                'normal',    // context
                'default'    // priority
            );
        }    
        if ( 'feed' == $_sTweetType || $_bIsUpdatePost ) {
            new FetchTweets_MetaBox_Feed(
                'fetch_tweets_meta_box_feed',    // meta box ID
                __( 'Tweets by Feed', 'fetch-tweets' ),    // meta box title
                array( FetchTweets_Commons::PostTypeSlug ),    // post, page, etc.
                'normal',    // context
                'default'    // priority
            );        
        }
        if ( 'tweet_id' == $_sTweetType || $_bIsUpdatePost ) {
            new FetchTweets_MetaBox_TweetID(
                'fetch_tweets_meta_box_custom_query',    // meta box ID
                __( 'Tweets by ID', 'fetch-tweets' ),    // meta box title
                array( FetchTweets_Commons::PostTypeSlug ),    // post, page, etc.
                'normal',    // context
                'default'    // priority
            );        
        }        
        if ( 'custom_query' == $_sTweetType || $_bIsUpdatePost ) {
            new FetchTweets_MetaBox_CustomQuery(
                'fetch_tweets_meta_box_custom_query',    // meta box ID
                __( 'Request with Custom Query', 'fetch-tweets' ),    // meta box title
                array( FetchTweets_Commons::PostTypeSlug ),    // post, page, etc.
                'normal',    // context
                'default'    // priority
            );        
        }
        
        new FetchTweets_MetaBox_Cache(
            'fetch_tweets_meta_box_cache',    // meta box ID
            __( 'Cache', 'fetch-tweets' ),        // meta box title
            array( FetchTweets_Commons::PostTypeSlug ),    // post, page, etc.
            'advanced',    // context
            'low'    // priority            
        );
        new FetchTweets_MetaBox_Template(
            'fetch_tweets_template_meta_box_v2',    // meta box ID
            __( 'Template', 'fetch-tweets' ),        // meta box title
            array( FetchTweets_Commons::PostTypeSlug ),    // post, page, etc.
            'side',        // context
            'low'    // priority
        );
        new FetchTweets_MetaBox_Tag;
        // new FetchTweets_MetaBox_Misc;        
        
    }
        /**
         * Retrieves the tweet type.
         * 
         * @since            2.0.0
         */
        private function _getTweetType() {

            // If the GET 'tweet_type' query value is set, use it.
            if ( isset( $_GET['tweet_type'] ) && $_GET['tweet_type'] ) return $_GET['tweet_type'];
        
            // If the 'action' query value is edit, search for the meta field value which previously set when it is saved.
            if ( isset( $_GET['action'], $_GET['post'] ) && $_GET['action'] == 'edit' ) {
                return get_post_meta( $_GET['post'], 'tweet_type', true );
            }
            
            // return the default type
            return 'screen_name';
            
        }    
        
        /**
         * Defines constants that are not defined in WordPress v3.4.x or below.
         * 
         * @since            1.3.0
         */
        protected function _defineConstantesForBackwardCompatibility() {
            
            if ( ! defined( 'MINUTE_IN_SECONDS' ) ) define( 'MINUTE_IN_SECONDS', 60 );
            if ( ! defined( 'HOUR_IN_SECONDS' ) ) define( 'HOUR_IN_SECONDS',   60 * MINUTE_IN_SECONDS );
            if ( ! defined( 'DAY_IN_SECONDS' ) ) define( 'DAY_IN_SECONDS',    24 * HOUR_IN_SECONDS   );
            if ( ! defined( 'WEEK_IN_SECONDS' ) ) define( 'WEEK_IN_SECONDS',    7 * DAY_IN_SECONDS    );
            if ( ! defined( 'YEAR_IN_SECONDS' ) ) define( 'YEAR_IN_SECONDS',  365 * DAY_IN_SECONDS    );    

        }

}