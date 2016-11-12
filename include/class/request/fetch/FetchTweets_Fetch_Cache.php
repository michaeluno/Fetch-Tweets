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
 * Handles caching of fetched data.
 * 
 * @since            1.3.4
 * @filter           fetch_tweets_filter_random_credentials
 */
abstract class FetchTweets_Fetch_Cache {
    
    /**
     * Stores the expired transients' request URIs
     */
    protected $arrExpiredTransientsRequestURIs = array();
    
    public function __construct( $sConsumerKey='', $sConsumerSecret='', $sAccessToken='', $sAccessSecret='' ) {
    
        // Set up the connection.
        $this->oOption = FetchTweets_Option::getInstance();
        
        $this->_aApplicationKeys    = $this->_getApplicationKeys( $sConsumerKey, $sConsumerSecret, $sAccessToken, $sAccessSecret );
        $this->oTwitterOAuth        =  new FetchTweets_TwitterOAuth( 
            $this->_aApplicationKeys['consumer_key'],
            $this->_aApplicationKeys['consumer_secret'],
            $this->_aApplicationKeys['access_token'],
            $this->_aApplicationKeys['access_secret']
        );
                        
        $this->oBase64 = new FetchTweets_Base64;    
        
        // Schedule the transient update task.
        add_action( 'shutdown', array( $this, '_replyToUpdateCacheItems' ) );
        
    }
        
        /**
         * Returns the application keys for the Twitter API credentials.
         * 
         * @since            2
         */
        protected function _getApplicationKeys( $sConsumerKey, $sConsumerSecret, $sAccessToken, $sAccessSecret ) {
        
            // If the keys are directly given to the class constructor, use them.
            if ( $sConsumerKey && $sConsumerSecret && $sAccessToken && $sAccessSecret ) {
                return array(
                    'consumer_key'      => $sConsumerKey,
                    'consumer_secret'   => $sConsumerSecret,
                    'access_token'      => $sAccessToken,
                    'access_secret'     => $sAccessSecret,
                );
            }
            
            return apply_filters( 
                'fetch_tweets_filter_random_credentials',  
                $this->oOption->getCredentialsByID( 0 )
            );
            
        }

    /**
     * Performs HTTP GET request with the given URL and sets the cache.
     * 
     * @since            2.1
     * @remark            The scope is public as the event class calls it.
     * @remark          This is not for Twitter API requests.
     */
    public function setGETRequestCache( $sRequestURI ) {
    
        $sRequestURI = trim( $sRequestURI );
        $_aResponse = wp_remote_get( $sRequestURI, array( 'timeout' => 15, 'sslverify' => false ) );

        if ( is_wp_error( $_aResponse ) ) {
            return array();
        }
        
        $_aTweets = json_decode( wp_remote_retrieve_body( $_aResponse ), true );    
                
        if ( empty( $_aTweets ) ) return array();
        
        if ( is_string( $_aTweets ) ) return array();
        
        // If the result is not an array, something went wrong.
        if ( ! is_array( $_aTweets ) ) return ( array ) $_aTweets;        
        
        $this->setTransient( $sRequestURI, $_aTweets );
        return $_aTweets;
        
    }
        
    /**
     * Performs the API request and sets the cache.
     * 
     * @access      public
     * @remark      The scope is public since the cache renewal event also uses it.
     * @param       string      $sRawRequestURI     The request URI that MAY contain embedded access keys in the query keys.
     * @param       string      $sArrayKey          The array key in the response array. For the search API request, a certain key is need to be set.
     * @param       array       $aRateLimitKeys     The representation of dimensional keys for the rate limit status-resource array. 
     */
    public function setAPIGETRequestCache( $sRawRequestURI, $sArrayKey=null, $aRateLimitKeys=array() ) {

        /**
         * Stores requested URIs to prevent multiple requests per a page load which causes the rate limit to exceed.
         */
        static $_aRequestedURIs = array();
                
        $sRawRequestURI = trim( $sRawRequestURI );
        $_sRequestURI   = $this->_sanitizeRequstURI( $sRawRequestURI );
        
        // Check if it has been already requested. If so return an error.
        if ( in_array( $_sRequestURI, $_aRequestedURIs ) ) {
            return array( 'error' => __( 'Excessive requests have been made. Please reload the page.', 'fetch-tweets' ) );
        }        
        $_aRequestedURIs[ $_sRequestURI ] = $_sRequestURI;        
        
        // Check if a custom access keys are set.
        $_aAccessKeys           = $this->_getAccessKeysFromQueryURI( $sRawRequestURI );
        $_oOriginalTwitterOAuth = $this->oTwitterOAuth;
        if ( ! empty( $_aAccessKeys ) ) {
            $this->oTwitterOAuth = new FetchTweets_TwitterOAuth( 
                $_aAccessKeys['consumer_key'],
                $_aAccessKeys['consumer_secret'], 
                $_aAccessKeys['access_token'], 
                $_aAccessKeys['access_secret']
            );
        }
        
        // Check the rate limit.
        if ( ! empty( $aRateLimitKeys ) ) {
            
            $_oRateLimit = new FetchTweets_TwitterAPI_Verification( 
                isset( $_aAccessKeys['consumer_key'] ) ? $_aAccessKeys['consumer_key'] : $this->_aApplicationKeys['consumer_key'], 
                isset( $_aAccessKeys['consumer_secret'] ) ? $_aAccessKeys['consumer_secret'] : $this->_aApplicationKeys['consumer_secret'],
                isset( $_aAccessKeys['access_token'] ) ? $_aAccessKeys['access_token'] : $this->_aApplicationKeys['access_token'],
                isset( $_aAccessKeys['access_secret'] ) ? $_aAccessKeys['access_secret'] : $this->_aApplicationKeys['access_secret']
            );
            $_iRemaining    = $_oRateLimit->getRemaining( $aRateLimitKeys );            
            if ( ! $_iRemaining ) {
                return array( 'error' => __( 'The number of API requests exceeded the rate limit. Please try it later.', 'fetch-tweets' ) );
            }
        }
                
        // Perform the API request.
        $_aTweets =  $this->oTwitterOAuth->get( $_sRequestURI );        
                    
        // Restore the original Twitter oAuth object.
        $this->oTwitterOAuth = $_oOriginalTwitterOAuth;
        
        // If the array key is specified, return the contents of the key element. Otherwise, return the retrieved array intact.
        if ( ! is_null( $sArrayKey ) && isset( $_aTweets[ $sArrayKey ] ) ) {
            $_aTweets = $_aTweets[ $sArrayKey ];
        }
                    
        // If empty, return an empty array.
        if ( empty( $_aTweets ) ) { return array(); }
        
        // If the result is not an array, something went wrong.
        if ( ! is_array( $_aTweets ) ) {
            return ( array ) $_aTweets;
        }
        
        // If an error occurs, do not set the cache.    
        if ( ! $this->oOption->aOptions['cache_settings']['cache_for_errors'] ) {
            if ( isset( $_aTweets['errors'][ 0 ]['message'], $_aTweets['errors'][ 0 ]['code'] ) ) {
                $_aTweets['errors'][ 0 ]['message'] .= "<!-- Request URI: {$_sRequestURI} -->";    
                return ( array ) $_aTweets;
            }
        }
        
        // Save the cache        
        $this->setTransient( $_sRequestURI, $_aTweets );

        return ( array ) $_aTweets;
        
    }
                
        /**
         * Returns an array of access keys from the given request URI.
         * 
         * @since            2
         */
        protected function _getAccessKeysFromQueryURI( $sRequestURI ){
                    
            parse_str( parse_url( $sRequestURI, PHP_URL_QUERY ), $aQuery );
            $_aAccessKeys = array(
                'consumer_key' => isset( $aQuery['consumer_key'] ) ? $aQuery['consumer_key'] : null,
                'consumer_secret' => isset( $aQuery['consumer_secret'] ) ? $aQuery['consumer_secret'] : null,
                'access_token' => isset( $aQuery['access_token'] ) ? $aQuery['access_token'] : null,
                'access_secret' => isset( $aQuery['access_secret'] ) ? $aQuery['access_secret'] : null,
            );    
            
            return isset( $_aAccessKeys['consumer_key'], $_aAccessKeys['consumer_secret'], $_aAccessKeys['access_token'], $_aAccessKeys['access_secret'] )
                ? $_aAccessKeys
                : array();
            
        }
        
        /**
         * Sanitizes the given Twitter request URI
         * 
         * The plugin request URI may contain unnecessary query keys to make transient name unique as the plugin generates transient ID from the request URI.
         * So this method will remove unsupported query keys from the given URI. If unsupported ones are present, Twitter API will return an error.
         * 
         * @since            2
         */
        protected function _sanitizeRequstURI( $sRequestURI ) {
            return remove_query_arg( array( 'consumer_key', 'consumer_secret', 'access_token', 'access_secret' ), trim( $sRequestURI ) );
        }
    
    /**
     * A wrapper method for the set_transient() function.
     * 
     * @since            1.2.0
     * @since            1.3.0            Made it public as the event method uses it.
     */
    public function setTransient( $sRequestURI, $vData, $iTime=null, $bIgnoreLock=false ) {

        $_sTransientKey     = FetchTweets_Commons::TransientPrefix . "_" . md5( trim( $sRequestURI ) );
        $_sLockTransient    = FetchTweets_Commons::TransientPrefix . '_' . md5( "Lock_" . $_sTransientKey );
        
        // Give some time to the server to store transients in case of simultaneous accesses.
        if ( FetchTweets_Cron::isBackground() ) {
            sleep( 1 );
        }
        
        // Check if the transient is locked.
        if ( ! $bIgnoreLock && false !== FetchTweets_WPUtility::getTransient( $_sLockTransient ) ) {
            return;    // it means the cache is being modified.
        }
        
        // Set a lock flag transient that indicates the transient is being renewed.
        if ( ! $bIgnoreLock ) {            
            FetchTweets_WPUtility::setTransient(
                $_sLockTransient, 
                'locked', // the value can be anything that yields true
                10
            );    
        }

        // Store the cache
        $_bIsSet = FetchTweets_WPUtility::setTransient(
            $_sTransientKey, 
            array( 
                'mod'   => $iTime ? $iTime : time(),
                'data'  => $this->oBase64->encode( $vData ),
            )
        );
        if ( ! $_bIsSet ) {                    
            return;
        }       
        
        // Schedule the action to run in the background with WP Cron. If already scheduled, skip.
        // This adds the embedding elements which takes some time to process.
        $_aArgs = array( $sRequestURI );
        if ( $iTime || wp_next_scheduled( 'fetch_tweets_action_transient_add_oembed_elements', $_aArgs ) ) { return; }
        wp_schedule_single_event( 
            time(), 
            'fetch_tweets_action_transient_add_oembed_elements',     // the FetchTweets_Event class will check this action hook and executes it with WP Cron.
            $_aArgs    // must be enclosed in an array.
        );    
        
        if ( 'intense' == $this->oOption->aOptions['cache_settings']['caching_mode'] ) {
            FetchTweets_Cron::see( array(), true );
        } else {
            // $this->arrExpiredTransientsRequestURIs
            wp_remote_get( site_url( "/wp-cron.php" ), array( 'timeout' => 0.01, 'sslverify'   => false, ) );
        }
        
        // Delete the lock transient
        // FetchTweets_WPUtility::deleteTransient( $_sLockTransient );

    }
    
    /**
     * A wrapper method for the get_transient() function.
     * 
     * This method does retrieves the transient with the given transient key. In addition, it checks if it is an array; otherwise, it makes it an array.
     * 
     * @access            public
     * @since            1.2.0
     * @since            1.3.0                Made it public as the event method uses it.
     */ 
    public function getTransient( $sTransientKey, $fForceArray=true ) {

        $_vData = FetchTweets_WPUtility::getTransient( $sTransientKey );
        
        // if it's false, no transient is stored. Otherwise, some values are in there.
        if ( false === $_vData ) { return false; }
                            
        // If it does not have to be an array. Return the raw result.
        if ( ! $fForceArray ) { return $_vData; }
        
        // If it's array, okay.
        if ( is_array( $_vData ) ) { return $_vData; }
        
        // Maybe it's encoded
        if ( is_string( $_vData ) && is_serialized( $_vData ) ) {
            return unserialize( $_vData );
        }
            
        // Maybe it's an object. In that case, convert it to an associative array.
        if ( is_object( $_vData ) ) {
            return get_object_vars( $_vData );
        }
            
        // It's an unknown type. So cast array and return it.
        return ( array ) $_vData;
            
    }
    
    /**
     * The flag to indicate whether the cache update tasks are called or not.
     */
    static private $_bUpdateCacheCalled = null;
    
    /*
     * Schedules the update cache task.
     * 
     * @callback        action      shutdown
     * */
    public function _replyToUpdateCacheItems() {    

        if ( null !== self::$_bUpdateCacheCalled ) {
            return;
        }
        self::$_bUpdateCacheCalled = true;
        
        if ( empty( $this->arrExpiredTransientsRequestURIs ) ) { return; }
        
        // Perform multi-dimensional array_unique()
        // @deprecated As of v2.3.5 the request URI is set to the key, so it is already unique by default.
        // $this->arrExpiredTransientsRequestURIs = array_map( "unserialize", array_unique( array_map( "serialize", $this->arrExpiredTransientsRequestURIs ) ) );
        
        $_iScheduled = 0;
        foreach( $this->arrExpiredTransientsRequestURIs as $_aExpiredCacheRequest ) {
            
            /* the structure of $_aExpiredCacheRequest = array(
                'URI'                    => the API request URI
                'key'                    => the array key that holds the result. e.g. for search results, the 'statuses' key holds the fetched tweets.
                'rate_limit_status_key' => the array holding the representation of the rate limit status dimensional key.
            */
            
            // Check if the URI key holds a valid url.
            if ( ! filter_var( $_aExpiredCacheRequest['URI'], FILTER_VALIDATE_URL ) ) { continue; }
            
            // Schedules the action to run in the background with WP Cron.
            // If already scheduled, skip.
            $_aArgs = array( $_aExpiredCacheRequest );
            if ( wp_next_scheduled( 'fetch_tweets_action_transient_renewal', $_aArgs ) ) { continue; }
            wp_schedule_single_event( 
                time(), 
                'fetch_tweets_action_transient_renewal',     // the FetchTweets_Event class will check this action hook and executes it with WP Cron.
                $_aArgs    // must be enclosed in an array.
            );    
            $_iScheduled++;
            
        }
        
        if ( ! $_iScheduled ) {
            return;
        }
        
        // Call the background process.
        if ( 'intense' == $this->oOption->aOptions['cache_settings']['caching_mode'] ) {
            FetchTweets_Cron::see();    
            return;
        } 
        wp_remote_get( site_url( "/wp-cron.php" ), array( 'timeout' => 0.01, 'sslverify'   => false, ) );        
                
    }
    
        /**
         * Retrieves the server set allowed maximum PHP script execution time.
         * 
         */
        protected function _getAllowedMaxExecutionTime( $iDefault=30, $iMax=120 ) {
            
            $_iSetTime = function_exists( 'ini_get' ) && ini_get( 'max_execution_time' ) 
                ? ( int ) ini_get( 'max_execution_time' ) 
                : $iDefault;
            
            return $_iSetTime > $iMax
                ? $iMax
                : $_iSetTime;
            
        }
        
}
