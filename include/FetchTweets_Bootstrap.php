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
 * Handles the initial set-up for the plugin.
 *    
 * @since       1.0.0
 * @since       1.3.4            Renamed to `FetchTweets_Bootstrap`s from `FetchTweets_InitialLoader`. 
 * @action      do      fetch_tweets_action_before_loading_plugin       [2.4.2+] Triggered before loading plugin components. Modules (extensions) should use this hook.
 * @action      do      fetch_tweets_action_after_loading_plugin        [2.4.2+] Triggered after loading plugin components. Modules (extensions) should use this hook.
 */
final class FetchTweets_Bootstrap extends FetchTweets_AdminPageFramework_PluginBootstrap {
    
    protected $_oUtil;
    
    /**
     * User constructor.
     * @since       2.5.0
     */
    public function start() {
        $this->_oUtil = new FetchTweets_AdminPageFramework_FrameworkUtility;
    }
        
    /**
     * @since       2.5.0
     */
    public function setConstants() {
        
        if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
            define( 'MINUTE_IN_SECONDS', 60 );
        }
        if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
            define( 'HOUR_IN_SECONDS',   60 * MINUTE_IN_SECONDS );
        }
        if ( ! defined( 'DAY_IN_SECONDS' ) ) {
            define( 'DAY_IN_SECONDS',    24 * HOUR_IN_SECONDS   );
        }
        if ( ! defined( 'WEEK_IN_SECONDS' ) ) {
            define( 'WEEK_IN_SECONDS',    7 * DAY_IN_SECONDS    );
        }
        if ( ! defined( 'YEAR_IN_SECONDS' ) ) {
            define( 'YEAR_IN_SECONDS',  365 * DAY_IN_SECONDS    );    
        }        
        
    }
    
    public function setGlobals() {
        
        $GLOBALS[ 'oFetchTweets_Option' ]         = null;    // stores the option object
        
        // Stores custom registering class paths
        $GLOBALS[ 'arrFetchTweets_FinalClasses' ] = $this->_oUtil->getElementAsArray(
            $GLOBALS,
            'arrFetchTweets_FinalClasses'
        );
        $GLOBALS[ 'arrFetchTweets_FinalClasses' ] = $this->_oUtil->getElementAsArray(
            $GLOBALS,
            'arrFetchTweets_Classes'
        );            
        
    }
    
    /**
     * The plugin activation callback method.
     * 
     * @since       2.5.0
     */    
    public function replyToPluginActivation() {
        
        $this->_checkRequirements();
                
    }
        /**
         * Performs plugin requirement checks.
         * @since       2.5.0
         */
        private function _checkRequirements() {
                
            $_oRequirementCheck = new FetchTweets_AdminPageFramework_Requirement(
                FetchTweets_Commons::$aRequirements,
                FetchTweets_Commons::NAME
            );        
            if ( $_oRequirementCheck->check() ) {            
                $_oRequirementCheck->deactivatePlugin( 
                    $this->sFilePath, 
                    __( 'Deactivating the plugin', 'fetch-tweets' ),  // additional message
                    true    // is in the activation hook. This will exit the script.
                );
            }   
        }
    
    /**
     * The plugin deactivation callback method.
     * 
     * @since       2.5.0
     * @return      void
     */
    public function replyToPluginDeactivation() {
        FetchTweets_WPUtility::clearTransients();
    }    
      
    
    
    /**
     * Load localization files.
     *
     * @since       2.5.0
     * @return      void
     * @callback    action      init
     */
    public function setLocalization() {    
        load_plugin_textdomain( 
            FetchTweets_Commons::TEXT_DOMAIN, 
            false, 
            dirname( plugin_basename( $this->sFilePath ) ) . '/language/'
        );       
    }        
    
        
    /**
     * Register classes to be auto-loaded.
     * 
     * @since       2.5.0
     */
    public function getClasses() {        
        $_aClassFiles   = array();
        include( dirname( $this->sFilePath ) . '/include/include-list.php' );
        return $_aClassFiles;
    }    

    /**
     * Loads all necessary plugin components.
     * 
     * @callback    action      plugins_loaded
     */
    public function setUp() {
                
        // 1. Libraries and required files.
        $this->___include();
        
        // 2. Custom database tables.
        $this->___handleCustomDatabaseTables();
        
        // 3. Option Object - the instantiation will handle the initial set-up
        FetchTweets_Option::getInstance();

        // 4. Load active templates - this must be done after loading the option class as it stores active templates.
        new FetchTweets_TemplatesLoader;
        
        // 5. Admin pages
        if ( $this->bIsAdmin ) {
            new FetchTweets_AdminPage( FetchTweets_Commons::$sAdminKey, $this->sFilePath );
            new FetchTweets_AdminPage_Contact( '', $this->sFilePath );
        }
        
        // 6. Post Type - no need to check is_admin() because posts of custom post type can be accessed from the front-end.
        new FetchTweets_PostType( FetchTweets_Commons::PostTypeSlug, null, $this->sFilePath );     // post type slug
        
        // 7. Meta-boxes
        if ( $this->bIsAdmin ) {
            $this->_registerMetaBoxes();
        }
                        
        // 8. Shortcodes e.g. [fetch_tweets id="143"]
        $this->___loadShortcodes();
                    
        // 9. Widgets
        add_action( 'widgets_init', 'FetchTweets_WidgetByID::registerWidget' );
        add_action( 'widgets_init', 'FetchTweets_WidgetByTag::registerWidget' );
                
        // 10. Events - handles background processes.
        new FetchTweets_Event;        

    }
        /**
         * Installs/upgrades database tables.
         * Since the activation hook is not triggered when the plugin updates,
         * The table versions must be checked in every page load.
         */
        private function ___handleCustomDatabaseTables() {
            $_oTable  = new FetchTweets_DatabaseTable_ft_http_requests;
            $_oTable->upgrade();  
            
            // Not implemented yet
            // $_oTable  = new FetchTweets_DatabaseTable_ft_tweets;
            // $_oTable->upgrade();                          
        }
    
        /**
         * Include files.
         * @return      void
         */
        private function ___include() {
            
            $_sPluginDir = dirname( $this->sFilePath );
            include( $_sPluginDir . '/include/function/functions.php' );            
            require( $_sPluginDir . '/include/library/TwitterOAuth/twitteroauth.php' );
                                    
        }    
        
        /**
         * @since       2.5.0
         */
        private function ___loadShortcodes() {            
            new FetchTweets_Shortcode_Main;    
            if ( $this->_oUtil->isDebugMode() ) {            
                new FetchTweets_Shortcode_Debug;    
            }
        }

    /**
     * Registers plugin specific meta boxes.
     * 
     * @since            2.0.0
     */
    protected function _registerMetaBoxes() {
        
        if ( ! isset( $GLOBALS['pagenow'] ) ) { 
            return; 
        }
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
        
    }
        /**
         * Retrieves the tweet type.
         * 
         * @since            2.0.0
         */
        private function _getTweetType() {

            // If the GET 'tweet_type' query value is set, use it.
            if ( isset( $_GET['tweet_type'] ) && $_GET['tweet_type'] ) { 
                return $_GET['tweet_type']; 
            }
        
            // If the 'action' query value is edit, search for the meta field value which previously set when it is saved.
            if ( isset( $_GET['action'], $_GET['post'] ) && $_GET['action'] == 'edit' ) {
                return get_post_meta( $_GET['post'], 'tweet_type', true );
            }
            
            // return the default type
            return 'screen_name';
            
        }    
        
        

}