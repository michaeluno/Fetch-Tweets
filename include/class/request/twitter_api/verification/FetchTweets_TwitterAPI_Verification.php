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
 * Handles Twitter API verification
 * 
 * @since           2
 * @since           2.5.0            Extends `FetchTweets_PluginUtility`.
 * @filter          apply            fetch_tweets_filter_request_rate_limit_status_keys            [2.3.0+] Applies to the request query that specifies the retrieving status keys. Default: statuses, search, lists.
 * @filter          apply            fetch_tweets_filter_rate_limit_status_translation            [2.3.0+] Applies to the translation array for the rate limit status labels.
 */
class FetchTweets_TwitterAPI_Verification extends FetchTweets_PluginUtility {
    
    public $sConsumerKey;
    public $sConsumerSecret;
    public $sAccessToken;
    public $sAccessSecret;
    
    /**
     * Sets up properties.
     */
    public function __construct( $sConsumerKey, $sConsumerSecret, $sAccessToken, $sAccessSecret ) {
        
        $this->sConsumerKey    = $sConsumerKey;
        $this->sConsumerSecret = $sConsumerSecret;
        $this->sAccessToken    = $sAccessToken;
        $this->sAccessSecret   = $sAccessSecret;
        
    }
    
    /**
     * 
     * @see            https://dev.twitter.com/docs/api/1.1/get/application/rate_limit_status
     */
    public function getStatus() {
        
        $_iCacheDuration = 60; // 1 minutes
        
        // Return the cached response if available.
        $_sCacheID  = FetchTweets_Commons::TransientPrefix . '_' . md5( serialize( array( $this->sConsumerKey, $this->sConsumerSecret, $this->sAccessToken, $this->sAccessSecret ) ) );
        $_vData     = $this->getTransient( $_sCacheID );
        if ( false !== $_vData ) { return $_vData; }
        
        // Perform the requests.
        $_oConnect  =  new FetchTweets_TwitterOAuth( $this->sConsumerKey, $this->sConsumerSecret, $this->sAccessToken, $this->sAccessSecret );
        $_oConnect->setCacheDuration( $_iCacheDuration );    
        $_aUser     = $_oConnect->get( 'account/verify_credentials' );
        
        // If the user id could not be retrieved, it means it failed.
        if ( ! isset( $_aUser['id'] ) || ! $_aUser['id'] ) {
            return array();
        }
            
        // Otherwise, it is okay. Retrieve the current status.
        $_aStatusKeys   = apply_filters( 'fetch_tweets_filter_request_rate_limit_status_keys', array( 'statuses', 'search', 'lists' ) );    // keys can be added such as 'help', 'users' etc
        $_aStatus       = $_oConnect->get( 'https://api.twitter.com/1.1/application/rate_limit_status.json?resources=' . implode( ',', $_aStatusKeys ) );
        
        // Set the cache.
        $_aData         = is_array( $_aStatus ) ? $_aUser + $_aStatus : $_aUser;
        $this->setTransient( $_sCacheID, $_aData, $_iCacheDuration );    // stores the cache only for 60 seconds. 
        
        return $_aData;    
        
    }
    
    /**
     * Returns the number of remaining requests from the given key.
     * 
     * @since       2.3.5
     * @return      integer     The remaining allowed number of requests. -1 for undetected.
     */
    public function getRemaining( array $aDimensionalPath ) {
        
        array_unshift( $aDimensionalPath , 'resources' ); // prepend an item.
        $aDimensionalPath[] = 'remaining';  // append an item.
        
        // Extract the value.
        return $this->getElement( 
            $this->getStatus(), // subject array
            $aDimensionalPath,  // dimensional path
            -1  // default
        );
  
    }
        
    /**
     * Renders the output of status table.
     * @deprecated      2.5.0       Kept for backward compatibility. The Multiple Accounts extension uses this method.
     */
    static public function renderStatus( $aStatus ) {
        $_oAPIStatus = new FetchTweets_Output_TwitterAPIStatus( $this->get() );
        $_oAPIStatus->render();                
    }

}
