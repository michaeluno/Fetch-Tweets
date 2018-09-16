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
        $_oConnect->setCacheDuration( 0 );
        
        // Get temporary credentials - Requesting authentication tokens, the parameter is the URL we will be redirected back to.
        // As of May 2018, Twitter API requires the callback URL to be whitelisted in the registered App setting.
        // @see https://twittercommunity.com/t/action-required-sign-in-with-twitter-users-must-whitelist-callback-urls/105342
        $_sCallbackURL  = add_query_arg(
            array(
                'post_type' => 'fetch_tweets',
                'page'      => 'fetch_tweets_settings',
                'tab'       => 'twitter_callback'
            ),
            admin_url( $GLOBALS[ 'pagenow' ] )
        );
        $_sCallbackURL  = 'http://michaeluno.sakura.ne.jp/apps/callback/callback.php?callback=' . base64_encode( $_sCallbackURL );
        $_aRequestToken = $_oConnect->getRequestToken( $_sCallbackURL );

        // For errors,
        $_sSettingPage = add_query_arg(
            array(
                'post_type' => 'fetch_tweets',
                'page'      => 'fetch_tweets_settings',
                'tab'       => 'twitter_connect'
            ),
            admin_url( $GLOBALS[ 'pagenow' ] )
        );
        if ( isset( $_aRequestToken[ '<?xml version' ] ) ) {
            // The TwitterOAuth library does not sanitize the error string.
            $_sMessage = preg_replace( '/.+\?>/', '', $_aRequestToken[ '<?xml version' ] );
            $oFactory->setSettingNotice( '<strong>' . FetchTweets_Commons::NAME . '</strong> ' . $_sMessage, 'error' );
            $this->goToURL( $_sSettingPage ); // will exit
        }

        // Save temporary credentials to transient.
        $_aTemporaryTokens = array();
        $_aTemporaryTokens['oauth_token'] = $_aRequestToken['oauth_token'];
        $_aTemporaryTokens['oauth_token_secret'] = $_aRequestToken['oauth_token_secret'];
        $this->setTransient( FetchTweets_Commons::TransientPrefix . '_oauth', $_aTemporaryTokens, 60*10 );    // 10 minutes
        
        // If last connection failed don't display authorization link.
        switch ( $_oConnect->http_code ) {
            case 200:    // Build authorize URL and redirect user to Twitter.
                $this->goToURL( $_oConnect->getAuthorizeURL( $_aTemporaryTokens['oauth_token'] ) );    // goes to twitter.com
            break;
            default:    // Show notification if something went wrong.
                $oFactory->setSettingNotice(
                    '<strong>' . FetchTweets_Commons::NAME . '</strong> '
                        . __( 'Could not connect to Twitter. Refresh the page or try again later.', 'fetch-tweets' )
                    , 'error' );
                $this->goToURL( $_sSettingPage );
        }
        exit;
        
    }
    

}
