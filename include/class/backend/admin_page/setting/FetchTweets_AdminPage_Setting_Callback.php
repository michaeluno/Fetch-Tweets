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
class FetchTweets_AdminPage_Setting_Callback extends FetchTweets_AdminPage_Tab_Base {

    
    /**
     * Called when the tab loads.
     * 
     * @remark      load + page slug + tab slug
     */
    public function replyToLoadTab( $oFactory ) {
        
        $this->_handleAuthenticationCallback();
        
    }
    
    /**
     * Receives the callback from Twitter authentication and saves the access token.
     * 
     * @remark      This method is triggered when the user get redirected back to the admin page
     * @since       1.3.0       
     * @since       2.4.5       Moved from the admin page class.
     */
    private function _handleAuthenticationCallback() { 
                
        /* If the oauth_token is old redirect to the authentication page. */
        $_aTemporaryTokens = FetchTweets_WPUtility::getTransient( FetchTweets_Commons::TransientPrefix . '_oauth' );
        if ( false === $_aTemporaryTokens || ! isset( $_aTemporaryTokens['oauth_token'], $_aTemporaryTokens['oauth_token_secret'] )) {
            exit( 
                wp_redirect( 
                    add_query_arg( 
                        array( 
                            'post_type' => 'fetch_tweets', 
                            'page' => 'fetch_tweets_settings', 
                            'tab' => 'authentication',
                        ), 
                        admin_url( $GLOBALS['pagenow'] ) 
                    ) 
                )
            );
        }        
        
        $_oOption = & $GLOBALS['oFetchTweets_Option'];

        /* Create TwitterOAuth object with app key/secret and token key/secret from default phase */
        $_oConnect = new FetchTweets_TwitterOAuth( 
            FetchTweets_Commons::ConsumerKey, 
            FetchTweets_Commons::ConsumerSecret, 
            $_aTemporaryTokens['oauth_token'],
            $_aTemporaryTokens['oauth_token_secret'] 
        );

        /* Request access tokens from twitter */
        $_aAccessTokens = $_oConnect->getAccessToken( $_REQUEST['oauth_verifier'] );
          /* $_aAccessTokens Looks like this
          'oauth_token' => string 'asxxx-sxxx...' (length=50)
          'oauth_token_secret' => string 'xxx....' (length=41)
          'user_id' => string '132.....' (length=10)
          'screen_name' => string 'my_screen_name' (length=9) */
  
        /* Save the access tokens. Normally these would be saved in a database for future use. */
        $_oOption->saveCredentials( 
            array(    
                'access_token'      => $_aAccessTokens['oauth_token'],
                'access_secret'     => $_aAccessTokens['oauth_token_secret'],
                'screen_name'       => $_aAccessTokens['screen_name'],
                'user_id'           => $_aAccessTokens['user_id'],
                'is_connected'      => true,
                'connect_method'    => 'oauth',
            )
        );
                
        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if ( 200 == $_oConnect->http_code ) {
            
            /* The user has been verified */
            $_sRediretURL = add_query_arg( 
                array(
                    'post_type' => 'fetch_tweets',
                    'page' => 'fetch_tweets_settings',
                    'tab' => 'twitter_connect'
                ), 
                admin_url( $GLOBALS['pagenow'] ) 
            );
            
          
        } else {
            
          /* Save HTTP status for error dialogue on authentication page.*/
          // Let the user set authentication keys manually          
            $_sRediretURL = add_query_arg( 
                array( 
                    'post_type' => 'fetch_tweets',
                    'page' => 'fetch_tweets_settings',
                    'tab' => 'authentication' 
                ), 
                admin_url( $GLOBALS['pagenow'] ) 
            );
      
        }
        exit( wp_redirect( $_sRediretURL ) );
    
    }
    
}