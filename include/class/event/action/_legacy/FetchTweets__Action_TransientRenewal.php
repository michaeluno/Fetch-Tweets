<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Renews transients.
 * 
 * @since       2.5.0
 * @action      add             
 * @action      schedule|add    
 */
class FetchTweets__Action_TransientRenewal extends FetchTweets__Action_Base {
    
    protected $_sActionName = 'fetch_tweets_action_transient_renewal';

    /**
     * Performs the cache renewal.
     */
    protected function doAction( /* $_aRequest */ ) {
        $_aParams  = func_get_args();
        $_aRequest = $_aParams[ 0 ];
        $_oFetch   = new FetchTweets_Fetch;
        if ( '_not_api_request' == $_aRequest[ 'key' ] ) {
            $_oFetch->setGETRequestCache( $_aRequest[ 'URI' ] );
        } else {
            $_oFetch->setAPIGETRequestCache( 
                $_aRequest[ 'URI' ], 
                $_aRequest[ 'key' ], 
                $_aRequest[ 'rate_limit_status_key' ] 
            );
        }
    }

}
