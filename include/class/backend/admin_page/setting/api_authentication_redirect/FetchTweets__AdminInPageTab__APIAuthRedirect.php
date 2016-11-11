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
 * Defines a tab.
 * 
 * @since       2.5.0
 */
class FetchTweets__AdminInPageTab__APIAuthRedirect extends FetchTweets__AdminInPageTab__Base {
    
    /**
     * @return      array
     * @since       2.5.0
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'tab_slug'     => 'twitter_redirect',
            'title'        => __( 'Redirect', 'fetch-tweets' ),
            'show_in_page_tab' => false,   
        );
    }

    protected function _load( $oFactory ) {
        
        // Build TwitterOAuth object with client credentials.
        $_oConnect = new FetchTweets_TwitterOAuth( 
            FetchTweets_Commons::ConsumerKey, 
            FetchTweets_Commons::ConsumerSecret 
        );
         
        // Get temporary credentials - Requesting authentication tokens, the parameter is the URL we will be redirected to.
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
        
        // Save temporary credentials to transient.
        $_aTemporaryTokens = array();
        $_aTemporaryTokens['oauth_token'] = $_aRequestToken['oauth_token'];
        $_aTemporaryTokens['oauth_token_secret'] = $_aRequestToken['oauth_token_secret'];
        $this->setTransient( FetchTweets_Commons::TransientPrefix . '_oauth', $_aTemporaryTokens, 60*10 );    // 10 minutes
        
        // If last connection failed don't display authorization link.
        switch ( $_oConnect->http_code ) {
            case 200:    // Build authorize URL and redirect user to Twitter.
                wp_redirect( $_oConnect->getAuthorizeURL( $_aTemporaryTokens['oauth_token'] ) );    // goes to twitter.com
            break;
            default:    // Show notification if something went wrong.
                die( __( 'Could not connect to Twitter. Refresh the page or try again later.', 'fetch-tweets' ) );
        }
        exit;
        
    }
    

}
