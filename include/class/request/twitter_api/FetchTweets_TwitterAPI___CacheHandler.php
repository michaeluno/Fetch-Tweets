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
 * Handles caching for Twitter API requests.
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
            ( integer ) $iCacheDuration,
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
     * @param       string      $sRequestURI            The request URI. Note that the URL query parameters should only include user defined ones.
     * Do not include time specific items such as nonce, timestamps, signature. 
     * @param       integer     $iCacheDuration         The cache duration of the retrieving cache. If `-1` is passed, the cache will be retrieved anyway.
     * @param       null|array  $naTargetElementPath    The element path that contains tweets as it differs among different request types.
     * @return      array       The cached data. If it is expired, an empty array will be returned. To force retrieving the cache, pass `-1` to the cache duration parameter.
     */
    public function get( $sRequestURI, $iCacheDuration, $naTargetElementPath=null ) {

        $_aCache = $this->___oCacheTable->getCache(  
            $this->___getCacheName( $sRequestURI ), 
            $iCacheDuration
        );   

        // Force getting the cache if the cache duration is -1. Used in the background cache modification.
        if ( -1 === $iCacheDuration ) {
            return $_aCache[ 'data' ];
        }        
        
        /**
         * Allow external components to modify the remained time, 
         * which can be used to trick the below check and return the stored data anyway.
         * So the cache renewal event can be scheduled in the background.
         */
        $_aCache = apply_filters(
            'fetch_tweets_filter_twitter_api_response_cache',
            $_aCache,
            $iCacheDuration,
            array(),                 // http arguments
            'twitter_api_request',   // request type
            $naTargetElementPath     // the dimensional path of the tweets element
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
