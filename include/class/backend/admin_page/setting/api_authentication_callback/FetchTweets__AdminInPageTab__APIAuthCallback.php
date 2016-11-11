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
class FetchTweets__AdminInPageTab__APIAuthCallback extends FetchTweets__AdminInPageTab__Base {
    
    /**
     * @return      array
     * @since       2.5.0
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'tab_slug'     => 'twitter_callback',
            'title'        => __( 'Callback', 'fetch-tweets' ),
            'show_in_page_tab'  => false,    
        );
    }

    protected function _load( $oFactory ) {
        
        // If the oauth_token is old, redirect to the authentication page.
        $_aTemporaryTokens = $this->getTransient( FetchTweets_Commons::TransientPrefix . '_oauth' );
        if ( false === $_aTemporaryTokens || ! isset( $_aTemporaryTokens['oauth_token'], $_aTemporaryTokens['oauth_token_secret'] )) {
            $this->goToURL(
                add_query_arg( 
                    array( 
                        'post_type' => 'fetch_tweets', 
                        'page'      => 'fetch_tweets_settings', 
                        'tab'       => 'authentication',
                    ), 
                    admin_url( $GLOBALS[ 'pagenow' ] ) 
                )                
            );
        }        
        
        $_oOption = FetchTweets_Option::getInstance();

        // Create TwitterOAuth object with app key/secret and token key/secret from default phase.
        $_oConnect = new FetchTweets_TwitterOAuth( 
            FetchTweets_Commons::ConsumerKey, 
            FetchTweets_Commons::ConsumerSecret, 
            $_aTemporaryTokens[ 'oauth_token' ],
            $_aTemporaryTokens[ 'oauth_token_secret' ] 
        );

        /**
         * Request access tokens from twitter.
         *
         * `$_aAccessTokens` Looks like this
         * ```
         *  'oauth_token'         => (string) 'asxxx-sxxx...' (length=50)
         *  'oauth_token_secret'  => (string) 'xxx....' (length=41)
         *  'user_id'             => (string) '132.....' (length=10)
         *  'screen_name'         => (string) 'my_screen_name' (length=9)
         * ```
         */
        $_aAccessTokens = $_oConnect->getAccessToken( $_REQUEST[ 'oauth_verifier' ] );
  
        // Save the access tokens. Normally these would be saved in a database for future use.
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
        
        $this->goToURL( $this->___getRedirectURL( $_oConnect ) );
        
    }
        
        /**
         * @since       2.5.0
         * @return      string
         */
        private function ___getRedirectURL( $_oConnect ) {
            
            // If HTTP response is 200 continue otherwise send to connect page to retry.
            if ( 200 == $_oConnect->http_code ) {
                
                // The user has been verified.
                return add_query_arg( 
                    array(
                        'post_type' => 'fetch_tweets',
                        'page'      => 'fetch_tweets_settings',
                        'tab'       => 'twitter_connect'
                    ), 
                    admin_url( $GLOBALS[ 'pagenow' ] ) 
                );
    
            } 
                
            // Save HTTP status for error dialogue on authentication page.
            // Let the user set authentication keys manually          
            return add_query_arg( 
                array( 
                    'post_type' => 'fetch_tweets',
                    'page'      => 'fetch_tweets_settings',
                    'tab'       => 'authentication' 
                ), 
                admin_url( $GLOBALS[ 'pagenow' ] ) 
            );
            
        }

}
