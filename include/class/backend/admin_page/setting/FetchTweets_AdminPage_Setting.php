<?php
/**
 * Fetch Tweets
 * 
 * Fetches and displays tweets from twitter.com.
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Defines an admin page.
 * 
 * @since       2.4.5
 */
class FetchTweets_AdminPage_Setting extends FetchTweets_AdminPage_Page_Base {

    /**
     * Called when the page loads.
     * 
     */
    public function replyToLoadPage( $oFactory ) {
                
        // Add in-page-tabs.
        new FetchTweets_AdminPage_Setting_Authentication(
            $oFactory,
            $this->sPageSlug,
            array(
                'tab_slug'          => 'authentication',    // the manual auth keys input page
                'title'             => __( 'Authentication', 'fetch-tweets' ),
                'parent_tab_slug'   => 'twitter_connect',
                'show_in_page_tab'  => false,    
            )            
        );
        new FetchTweets_AdminPage_Setting_TwitterConnect(
            $oFactory,
            $this->sPageSlug,        
            array(
                'tab_slug'     => 'twitter_connect',    // the oAuth connection page
                'title'        => __( 'Authentication', 'fetch-tweets' ),
                'order'        => 1,                
            )
        );
        new FetchTweets_AdminPage_Setting_Redirect(
            $oFactory,
            $this->sPageSlug,          
            array(
                'tab_slug'     => 'twitter_redirect',
                'title'        => __( 'Redirect', 'fetch-tweets' ),
                'show_in_page_tab' => false,
            )            
        );
        new FetchTweets_AdminPage_Setting_Callback(
            $oFactory,
            $this->sPageSlug,                  
            array(
                'tab_slug'     => 'twitter_callback',
                'title'        => __( 'Callback', 'fetch-tweets' ),
                'show_in_page_tab'  => false,
            )        
        );
        new FetchTweets_AdminPage_Setting_General(
            $oFactory,
            $this->sPageSlug,     
            array(
                'tab_slug'     => 'general',
                'title'        => __( 'General', 'fetch-tweets' ),
                'order'        => 2,                
            )            
        );
        new FetchTweets_AdminPage_Setting_MISC(
            $oFactory,
            $this->sPageSlug,    
            array(
                'tab_slug'     => 'misc',
                'title'        => __( 'Misc', 'fetch-tweets' ),
                'order'        => 3,                
            )
        );
        new FetchTweets_AdminPage_Setting_Cache(
            $oFactory,
            $this->sPageSlug,    
            array(
                'tab_slug'     => 'cache',
                'title'        => __( 'Cache', 'fetch-tweets' ),
                'order'        => 4,
            )
        );        
   
        new FetchTweets__AdminInPageTab__ManageOption(
            $oFactory,
            $this->sPageSlug
        );
 
        add_action( "do_before_{$this->sPageSlug}", array( $this, 'replyToDoBeforePage' ) );
        
    }
    
    /**
     * Called before the page gets rendered.
     * 
     * @remark      do_before_ + page slug
     */
    public function replyToDoBeforePage( $oFactory ) {
        $oFactory->setPageTitleVisibility( false );
    }
 
}