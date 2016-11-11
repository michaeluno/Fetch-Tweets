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

        new FetchTweets__AdminInPageTab__APIAuthenticationKey( $oFactory, $this->_sPageSlug );
        new FetchTweets__AdminInPageTab__APIAuthentication( $oFactory, $this->_sPageSlug );      
        new FetchTweets__AdminInPageTab__APIAuthRedirect( $oFactory, $this->_sPageSlug );
        new FetchTweets__AdminInPageTab__APIAuthCallback( $oFactory, $this->_sPageSlug );
        new FetchTweets__AdminInPageTab__General( $oFactory, $this->_sPageSlug );
        new FetchTweets__AdminInPageTab__Misc( $oFactory, $this->_sPageSlug );
        new FetchTweets__AdminInPageTab__Cache( $oFactory, $this->_sPageSlug );
        new FetchTweets__AdminInPageTab__ManageOption( $oFactory, $this->_sPageSlug );
        
        $oFactory->setPageTitleVisibility( false );
        
    }
  
}
