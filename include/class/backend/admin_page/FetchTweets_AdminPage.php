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
 * 
 */
class FetchTweets_AdminPage extends FetchTweets_AdminPageFramework {

    /**
     * A public user construct.
     */
    public function start() {
                
        // Set the option property.
        $this->oOption = FetchTweets_Option::getInstance();
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }
        
        // Add custom links to the description cell of the plugin listing table.
        if ( 'plugins.php' === $this->oProp->sPageNow ) {            
            $this->addLinkToPluginDescription(  
                '<a href="http://en.michaeluno.jp/donate">' . __( 'Donate', 'fetch-tweets' ) . '</a>',
                '<a href="http://en.michaeluno.jp/contact/custom-order/?lang=' . ( defined( 'WPLANG' ) && WPLANG ? WPLANG : 'en' ) . '">' . __( 'Order custom plugin', 'fetch-tweets' ) . '</a>'
            );
        }        
        
        // Disable object caching in the plugin pages to help some caching plugins not to prevent the settings from being saved.
        if (
            isset( $_GET['post_type'] )
            && in_array( $_GET['post_type'], array( FetchTweets_Commons::PostTypeSlug, FetchTweets_Commons::$aPostTypes['account'] ) )
        ) {
            $GLOBALS['_wp_using_ext_object_cache'] = false;    
        }        
        
        // For the list table bulk actions. The WP_List_Table class does not set the post type query string in the redirected page.
        // http://.../wp-admin/edit.php?page=fetch_tweets_templates&tab=&_wpnonce=ebed1d5343&_wp_http_referer=%2Fwp360%2Fwp-admin%2Fedit.php%3Fpost_type%3Dfetch_tweets%26page%3Dfetch_tweets_templates&action=activate&paged=1&action2=-1
        if ( 
            ( isset( $_POST['post_type'] ) && $_POST['post_type'] == FetchTweets_Commons::PostTypeSlug )    // the form is submitted 
            && ( ! isset( $_GET['post_type'] ) )    // and post_type query string is not in the url
            && ( isset( $_GET['page'] ) && $_GET['page'] == 'fetch_tweets_templates' ) // and the page is the template listing table page,
        ) {
            exit( wp_redirect( add_query_arg( array( 'post_type' => FetchTweets_Commons::PostTypeSlug ) + $_GET, admin_url() . '' . $GLOBALS['pagenow'] ) ) );
        }
    
        // Prepare the template array for the template listing table
        if ( isset( $_GET['page'] ) && 'fetch_tweets_templates' === $_GET['page'] ) {
            add_action( 'admin_menu', array( $this, '_replyToProcessBulkActionForTemplateListTable' ) );            
        }
                
    }
    
        public function _replyToProcessBulkActionForTemplateListTable() {
            
            $this->oTemplateListTable = new FetchTweets_ListTable(
                $this->oOption->getActiveTemplates() + $this->oOption->getUploadedTemplates()
            );

            $this->oTemplateListTable->process_bulk_action();
            
        }


    public function setUp() {
            
        $_sCapability = FetchTweets_Option::get( array( 'capabilities', 'setting_page_capability' ) );
        if ( $_sCapability ) {
            $this->setCapability( $_sCapability );
        }

        $this->_setUpPages();
        
    }
    
    /**
     * Set the default options.
     */
    public function options_FetchTweets_AdminPage( $aOptions ) {
        return $this->oUtil->uniteArrays(
            $aOptions,
            $this->oOption->get()
        );
    }
    
        /**
         * Defines the plugin pages.
         */
        private function _setUpPages() {
            
            $_sPostTypeSlug = FetchTweets_Commons::PostTypeSlug;
            $this->setRootMenuPageBySlug( "edit.php?post_type={$_sPostTypeSlug}"  );
            $this->addSubMenuItems(
                array(
                    'title'         => __( 'Add Rule by Screen Name', 'fetch-tweets' ),
                    'href'          => admin_url( "post-new.php?post_type={$_sPostTypeSlug}&tweet_type=screen_name" ),
                    'order'         => 10,
                ),
                array(
                    'title'         => __( 'Add Rule by Timeline', 'fetch-tweets' ),
                    'href'          => admin_url( "post-new.php?post_type={$_sPostTypeSlug}&tweet_type=home_timeline" ),
                    'order'         => 20,
                ),            
                array(
                    'title'         => __( 'Add Rule by Search', 'fetch-tweets' ),
                    'href'          => admin_url( "post-new.php?post_type={$_sPostTypeSlug}&tweet_type=search" ),
                    'order'         => 30,
                )
            );                
        
            new FetchTweets__AdminPage__AddRuleByList( $this );  
            
            $this->addSubMenuItems(
                array(
                    'title'         => __( 'Add Rule by Feed', 'fetch-tweets' ),
                    'href'          => admin_url( "post-new.php?post_type={$_sPostTypeSlug}&tweet_type=feed" ),
                    'order'         => 50,
                ),                
                array(
                    'title'         => __( 'Add Rule by Tweet ID', 'fetch-tweets' ),
                    'href'          => admin_url( "post-new.php?post_type={$_sPostTypeSlug}&tweet_type=tweet_id" ),
                    'order'         => 60,
                ),            
                array(
                    'title'         => __( 'Add Rule by Custom Query', 'fetch-tweets' ),
                    'href'          => admin_url( "post-new.php?post_type={$_sPostTypeSlug}&tweet_type=custom_query" ),
                    'order'         => 70,
                )
            );
            new FetchTweets__AdminPage__Setting( $this );
            new FetchTweets_AdminPage_Template(
                $this,
                'fetch_tweets_templates',
                __( 'Templates', 'fetch-tweets' ),
                FetchTweets_Commons::getPluginURL( "/asset/image/screen_icon_32x32.png" )
            );
            new FetchTweets_AdminPage_Extension(
                $this,
                'fetch_tweets_extensions',
                __( 'Extensions', 'fetch-tweets' ),
                FetchTweets_Commons::getPluginURL( "/asset/image/screen_icon_32x32.png" )
            );
            
        }            
    
    /**
     * Gets loaded one of the plugin admin page started loading.
     */
    public function load() {
        
        $this->_setUpStyles();
         
        if ( ! $this->oOption->isAuthKeysManuallySet() && ! $this->oOption->isAuthKeysAutomaticallySet() ) { 
            $_sSettingPageURL = add_query_arg( array( 'post_type' => 'fetch_tweets', 'page' => 'fetch_tweets_settings', 'tab' => 'twitter_redirect' ), admin_url( $this->oProp->sPageNow ) ); 
            $this->setAdminNotice(
                "<strong>" . FetchTweets_Commons::NAME . "</strong>: "
                . sprintf( __( '<a href="%1$s">The API authentication keys need to be set</a> in order to use this plugin.', 'fetch-tweets' ), $_sSettingPageURL )
            );
        } 
        
    }
        /**
         * Defines the plugin admin page styles.
         */
        private function _setUpStyles() {
            
            $this->setPageHeadingTabsVisibility( false );        // disables the page heading tabs by passing false.
            $this->setInPageTabTag( 'h2' );                
            $this->enqueueStyle( FetchTweets_Commons::getPluginURL( '/asset/css/admin.css' ) );
            $this->enqueueStyle( FetchTweets_Commons::getPluginURL( '/asset/css/fetch_tweets_templates.css' ), 'fetch_tweets_templates' );
            $this->enqueueStyle( FetchTweets_Commons::getPluginURL( '/asset/css/fetch_tweets_settings.css' ), 'fetch_tweets_settings' );
            $this->enqueueStyle( FetchTweets_Commons::getPluginURL( '/asset/css/fetch_tweets_add_rule_by_list.css' ), 'fetch_tweets_add_rule_by_list' );
            
        }        
        
}