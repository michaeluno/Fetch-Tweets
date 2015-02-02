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
 * Defines an in-page tab.
 * 
 * @since       2.4.5
 */
class FetchTweets_AdminPage_Setting_Redirect extends FetchTweets_AdminPage_Tab_Base {

    
    /**
     * Called when the tab loads.
     * 
     * @remark      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {
        
        $this->_redirect();
        
    }
    
    
    /**
     * Redirects to the twitter to get authenticated.
     * 
     * @since       1.3.0
     * @since       2.4.5       Moved from the admin page class.
     * @remark      This is redirected from the "Connect to Twitter" button.
     */
    private function _redirect() {  
    
        /* Build TwitterOAuth object with client credentials. */
        $_oConnect = new FetchTweets_TwitterOAuth( 
            FetchTweets_Commons::ConsumerKey, 
            FetchTweets_Commons::ConsumerSecret 
        );
         
        /* Get temporary credentials - Requesting authentication tokens, the parameter is the URL we will be redirected to. */
        $_aRequestToken = $_oConnect->getRequestToken( 
            add_query_arg( 
                array(
                    'post_type' => 'fetch_tweets',
                    'page' => 'fetch_tweets_settings',
                    'tab' => 'twitter_callback'
                ),
                admin_url( $GLOBALS['pagenow'] ) 
            )
        );
        
        /* Save temporary credentials to transient. */
        $_aTemporaryTokens = array();
        $_aTemporaryTokens['oauth_token'] = $_aRequestToken['oauth_token'];
        $_aTemporaryTokens['oauth_token_secret'] = $_aRequestToken['oauth_token_secret'];
        FetchTweets_WPUtilities::setTransient( FetchTweets_Commons::TransientPrefix . '_oauth', $_aTemporaryTokens, 60*10 );    // 10 minutes
        
        /* If last connection failed don't display authorization link. */
        switch ( $_oConnect->http_code ) {
            case 200:    /* Build authorize URL and redirect user to Twitter. */
                wp_redirect( $_oConnect->getAuthorizeURL( $_aTemporaryTokens['oauth_token'] ) );    // goes to twitter.com
            break;
            default:    /* Show notification if something went wrong. */
                die( __( 'Could not connect to Twitter. Refresh the page or try again later.', 'fetch-tweets' ) );
        }
        exit;
    
    }       
    
}