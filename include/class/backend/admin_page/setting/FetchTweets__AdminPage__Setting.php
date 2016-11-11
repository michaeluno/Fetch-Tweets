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
 * Defines an admin page.
 * 
 * @since       2.5.0
 */
class FetchTweets__AdminPage__Setting extends FetchTweets__AdminPage__Base {

    protected function _getArguments( $oFactory ) {
        return array(
            'page_slug'     => 'fetch_tweets_settings',
            'title'         => __( 'Settings', 'fetch-tweets' ),
            'screen_icon'   => FetchTweets_Commons::getPluginURL( "/asset/image/screen_icon_32x32.png" ),
            'order'         => 80,
        );
    }

    /**
     * Called when the page loads.
     */
    protected function _load( $oFactory ) {
                
        // Add in-page-tabs.
        new FetchTweets_AdminPage_Setting_Authentication(
            $oFactory,
            $this->_sPageSlug,
            array(
                'tab_slug'          => 'authentication',    // the manual auth keys input page
                'title'             => __( 'Authentication', 'fetch-tweets' ),
                'parent_tab_slug'   => 'twitter_connect',
                'show_in_page_tab'  => false,    
            )            
        );
        new FetchTweets_AdminPage_Setting_TwitterConnect(
            $oFactory,
            $this->_sPageSlug,        
            array(
                'tab_slug'     => 'twitter_connect',    // the oAuth connection page
                'title'        => __( 'Authentication', 'fetch-tweets' ),
                'order'        => 1,                
            )
        );
        new FetchTweets_AdminPage_Setting_Redirect(
            $oFactory,
            $this->_sPageSlug,          
            array(
                'tab_slug'     => 'twitter_redirect',
                'title'        => __( 'Redirect', 'fetch-tweets' ),
                'show_in_page_tab' => false,
            )            
        );
        new FetchTweets_AdminPage_Setting_Callback(
            $oFactory,
            $this->_sPageSlug,                  
            array(
                'tab_slug'     => 'twitter_callback',
                'title'        => __( 'Callback', 'fetch-tweets' ),
                'show_in_page_tab'  => false,
            )        
        );
      
        new FetchTweets__AdminInPageTab__General( $oFactory, $this->_sPageSlug );
        new FetchTweets__AdminInPageTab__Misc( $oFactory, $this->_sPageSlug );
        new FetchTweets__AdminInPageTab__Cache( $oFactory, $this->_sPageSlug );
        new FetchTweets__AdminInPageTab__ManageOption( $oFactory, $this->_sPageSlug );
        
        $oFactory->setPageTitleVisibility( false );
        
    }
  
}
