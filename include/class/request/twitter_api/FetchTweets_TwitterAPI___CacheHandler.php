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
 * Handles Twitter API requests.
 * 
 * @since             2.5.0
 */
class FetchTweets_TwitterAPI___CacheHandler extends FetchTweets_PluginUtility {
    
    /**
     * A cache table class name.
     */
    private $___sCacheTableClass = 'FetchTweets_DatabaseTable_ft_http_requests';    
    
    private $___oCacheTable;
   
    static private $___sSiteCharSet;
    
    /**
     * Sets up properties.
     * @since       2.5.0
     */
    public function __construct( /* $aArguments */ ) {

        $this->___oCacheTable    = new $this->___sCacheTableClass;

        self::$___sSiteCharSet   = isset( self::$___sSiteCharSet )
            ? self::$___sSiteCharSet
            : get_bloginfo( 'charset' );        

        
    }        
        

    /**
     * Sets a cache.
     */
    public function set( $sRequestURI, $mData, $iCacheDuration ) {
        $_bResult     = $this->___oCacheTable->setCache( 
            $this->___getCacheName( $sRequestURI ), // name
            $mData,
            $iCacheDuration // when 0 is passed, use a default value of 86400 (one day). So pass 0 to renew the cache.
                ? ( integer ) $iCacheDuration
                : 86400, // cache life span
            array( // extra column items
                'request_uri' => $sRequestURI,
                'type'        => 'twitter_api_request',
                'charset'     => strtolower( self::$___sSiteCharSet ),
            )
        );      
        return $_bResult;
    }
    
    /**'
     * Retrieve the cache.
     * 
     * @param       string      $sRequestURI        The request URI. Note that the URL query parameters should only include user defined ones.
     * Do not include time specific items such as nonce, timestamps, signature. 
     * @retrurn     array
     */
    public function get( $sRequestURI, $iCacheDuration ) {
        
        $_aCache = $this->___oCacheTable->getCache(  
            $this->___getCacheName( $sRequestURI ), 
            $iCacheDuration
        );   

        /**
         * Allow external components to modify the remained time, 
         * which can be used to trick the below check and return the stored data anyway.
         * So the cache renewal event can be scheduled in the background.
         */
        $_aCache = apply_filters(
            'fetch_tweets_filter_twitter_api_response_cache',
            $_aCache,
            $iCacheDuration,
            array(),        // http arguments
            'twitter_api_request'   // request type
        );        
        if ( 0 >= $_aCache[ 'remained_time' ] ) {
            return array();
        }
        return $_aCache[ 'data' ];
        
    }    
        /**
         * @return      string
         */
        private function ___getCacheName( $sURI ) {
            return 'twitter_api_request_' . md5( $sURI );
        }
         
}
