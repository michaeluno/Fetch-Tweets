<?php
/**
 * Fetch Tweets
 * 
 * Fetches and displays tweets from twitter.com.
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

class FetchTweets_AdminPage extends FetchTweets_AdminPageFramework {

    /**
     * A public user construct.
     */
    public function start() {
                
        // Set the option property.
        $this->oOption = $GLOBALS['oFetchTweets_Option'];
        
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
            && in_array( $_GET['post_type'], array( FetchTweets_Commons::PostTypeSlug, FetchTweets_Commons::PostTypeSlugAccounts ) )
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
        
        
    /*
     * Customize the Menu
     */
    public function _replyToBuildMenu() {
    
        parent::_replyToBuildMenu();
        
        // Somehow the settings link in the plugin listing page points to the Create Rule by List page. So fix it to the Settings page.
        $this->oProp->sDefaultPageSlug = FetchTweets_Commons::PageSettingsSlug;

        // Remove the default post type menu item.
        $_sPageSlug = $this->oProp->aRootMenu['sPageSlug'];
        if ( ! isset( $GLOBALS['submenu'][ $_sPageSlug ] ) ) { 
            // logged-in users of an insufficient access level don't have the menu to be registered.
            return; 
        } 
        foreach ( $GLOBALS['submenu'][ $_sPageSlug ] as $intIndex => $arrSubMenu ) {
                        
            if ( ! isset( $arrSubMenu[ 2 ] ) ) { continue; }
            
            // Remove the default Add New entry.
            if ( $arrSubMenu[ 2 ] == 'post-new.php?post_type=' . FetchTweets_Commons::PostTypeSlug ) {
                unset( $GLOBALS['submenu'][ $_sPageSlug ][ $intIndex ] );
                continue;
            }
            
            // Copy and remove the Tag menu element to change the position. 
            if ( $arrSubMenu[ 2 ] == 'edit-tags.php?taxonomy=' . FetchTweets_Commons::TagSlug . '&amp;post_type=' . FetchTweets_Commons::PostTypeSlug ) {
                $_aMenuEntry_Tag = array( $GLOBALS['submenu'][ $_sPageSlug ][ $intIndex ] );
                unset( $GLOBALS['submenu'][ $_sPageSlug ][ $intIndex ] );
                continue;                
            }

        }
        
        // Second iteration.
        $intMenuPos_Setting = -1;
        foreach ( $GLOBALS['submenu'][ $_sPageSlug ] as $intIndex => $arrSubMenu ) {
            
            $intMenuPos_Setting++;    
            if (  isset( $arrSubMenu[ 2 ] ) && $arrSubMenu[ 2 ] == 'fetch_tweets_settings' ) 
                break;    // the position variable will now contain the position of the Setting menu item.
    
        }
    
        // Insert the Tag menu item before the Setting menu item.
        if ( isset( $_aMenuEntry_Tag ) ) {
            array_splice( 
                $GLOBALS['submenu'][ $_sPageSlug ], // original array
                $intMenuPos_Setting,     // position
                0,     // offset - should be 0
                $_aMenuEntry_Tag     // replacement array
            );        
        }

        // Unfortunately array_splice() will loose all the associated keys(index).
        
    }


    public function setUp() {
            
        $_sCapability = FetchTweets_Option::get( array( 'capabilities', 'setting_page_capability' ) );
        if ( $_sCapability ) {
            $this->setCapability( $_sCapability );
        }

        $this->_setUpPages();
        
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
                    'order'         => 1,
                ),
                array(
                    'title'         => __( 'Add Rule by Timeline', 'fetch-tweets' ),
                    'href'          => admin_url( "post-new.php?post_type={$_sPostTypeSlug}&tweet_type=home_timeline" ),
                ),            
                array(
                    'title'         => __( 'Add Rule by Search', 'fetch-tweets' ),
                    'href'          => admin_url( "post-new.php?post_type={$_sPostTypeSlug}&tweet_type=search" ),
                )
            );                
        
            new FetchTweets_AdminPage_AddRuleByList(
                $this,
                'fetch_tweets_add_rule_by_list',    // page slug
                __( 'Add Rule by List', 'fetch-tweets' ),   // page title
                FetchTweets_Commons::getPluginURL( "/asset/image/screen_icon_32x32.png" )
            );  
            
            $this->addSubMenuItems(
                array(
                    'title'         => __( 'Add Rule by Feed', 'fetch-tweets' ),
                    'href'          => admin_url( "post-new.php?post_type={$_sPostTypeSlug}&tweet_type=feed" ),
                ),                
                array(
                    'title'         => __( 'Add Rule by Tweet ID', 'fetch-tweets' ),
                    'href'          => admin_url( "post-new.php?post_type={$_sPostTypeSlug}&tweet_type=tweet_id" ),
                ),            
                array(
                    'title'         => __( 'Add Rule by Custom Query', 'fetch-tweets' ),
                    'href'          => admin_url( "post-new.php?post_type={$_sPostTypeSlug}&tweet_type=custom_query" ),
                )
            );
            new FetchTweets_AdminPage_Setting(
                $this,
                'fetch_tweets_settings',
                __( 'Settings', 'fetch-tweets' ),
                FetchTweets_Commons::getPluginURL( "/asset/image/screen_icon_32x32.png" )
            );
            new FetchTweets_AdminPage_Extension(
                $this,
                'fetch_tweets_extensions',
                __( 'Extensions', 'fetch-tweets' ),
                FetchTweets_Commons::getPluginURL( "/asset/image/screen_icon_32x32.png" )
            );
            new FetchTweets_AdminPage_Template(
                $this,
                'fetch_tweets_templates',
                __( 'Templates', 'fetch-tweets' ),
                FetchTweets_Commons::getPluginURL( "/asset/image/screen_icon_32x32.png" )
            );

        }            
    
    /**
     * Gets loaded one of the plugin admin page started loading.
     */
    public function load_FetchTweets_AdminPage( $oAdminPage ) {
        
        $this->_setUpStyles();
         
        if ( ! $this->oOption->isAuthKeysManuallySet() && ! $this->oOption->isAuthKeysAutomaticallySet() ) { 
            $_sSettingPageURL = add_query_arg( array( 'post_type' => 'fetch_tweets', 'page' => 'fetch_tweets_settings', 'tab' => 'twitter_redirect' ), admin_url( $this->oProp->sPageNow ) ); 
            $this->setAdminNotice(
                "<strong>" . FetchTweets_Commons::PluginName . "</strong>: "
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