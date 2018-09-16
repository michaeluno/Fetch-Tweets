<?php
/**
 * Fetch Tweets
 * 
 * Fetches and displays tweets from twitter.com.
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 */

if ( ! class_exists( 'TwitterFetchTweetsOAuth' ) ) {    
    require_once( dirname( FetchTweets_Commons::$sPluginPath ) . '/include/library/TwitterOAuth/twitteroauth.php' );
}

/**
 * A Twitter API hander class wrapper.
 */
class FetchTweets_TwitterOAuth extends TwitterFetchTweetsOAuth {
    
    public $host = "https://api.twitter.com/1.1/";

    /* This extended class specific properties. */
    public $iCachingMode   = 3; // do not set caches 
    public $iCacheDuration = 86400;
    public $aHTTPArguments = array();

    /**
     * Sets a cache duration.
     * Use this method before performing the `get()` method.
     */
    public function setCacheDuration( $iCacheDuration ) {
        $this->iCacheDuration = $iCacheDuration;
    }
    /**
     * - 0. get caches/responses + set caches ( get caches if available other wise perform a request, used for normal requests )
     * - 1. get caches ( get only caches, used to check caches )
     * - 2. get responses + set caches ( called in the background to update the cache )
     * - 3. get responses ( do not set caches, used for sensitive queries such as getting credentials )
     */
    public function setCachingMode( $iMode ) {
        $this->iCachingMode = $iMode;
    }
    public function setHTTPArguments( $aArguments ) {
        $this->aHTTPArguments = ( array ) $aArguments;
    }
    
    /**
     * Get the authorize URL
     *
     * @remark  Modified the original method to add the force_login query key-value pair.
     * @return  string
     */
    public function getAuthorizeURL($token, $sign_in_with_twitter = TRUE) {
        if (is_array($token)) {
            $token = $token['oauth_token'];
        }
        if (empty($sign_in_with_twitter)) {
            return $this->authorizeURL() . "?oauth_token={$token}&force_login=true";
        } else {
            return $this->authenticateURL() . "?oauth_token={$token}&force_login=true";
        }
    }    
    
    /**
    * GET wrapper for oAuthRequest.
    * 
    * @remark            Modified the original method to returns the response as an associative array.
    */
    public function get($url, $parameters = array()) {
        $response = $this->oAuthRequest($url, 'GET', $parameters);
        if ($this->format === 'json' && $this->decode_json) {
            return json_decode($response, true);    // return as associative array
        }
        return $response;
    }
  
    /**
    * POST wrapper for oAuthRequest.
    * 
    * @remark            Modified the original method to returns the response as an associative array.
    */
    public function post($url, $parameters = array()) {
        $response = $this->oAuthRequest($url, 'POST', $parameters);
        if ($this->format === 'json' && $this->decode_json) {
            return json_decode($response, true);    // return as associative array
        }
        return $response;
    }

    /**
    * DELETE wrapper for oAuthReqeust.
    *
    * @remark            Modified the original method to returns the response as an associative array.
    */
    public function delete($url, $parameters = array()) {
        $response = $this->oAuthRequest($url, 'DELETE', $parameters);
        if ($this->format === 'json' && $this->decode_json) {
            return json_decode($response, true);    // return as associative array
        }
        return $response;
    }

    /**
    * Format and sign an FetchTweetsOAuth / API request
    * @remark       Overriding this method to add a WordPress filter to the processing url.
    */
    public function oAuthRequest( $sURL, $method, $parameters) {
        
        /**
         * Applies to the API request url before constructing a signature generated from the set query parameters.
         * If the signature is incorrect Twitter API server will not authenticate the request.
         * So any modifications to the URL query parameters have to be done at this point.
         */
        $sURL = apply_filters( 'fetch_tweets_filter_formatting_api_request_uri', $sURL );
        return parent::oAuthRequest( $sURL, $method, $parameters );
        
    }    
    
    /**
     * Make an HTTP request.
     *
     * @remark      Overriding the parent method to use a custom HTTP client.
     * @return      API results
     * @since       2.5.0
     * @param       string      $sURL
     * @param       string      $sMethod        GET, POST, or DELETE
     * @param       string      $sPostFields    formatted POST body fields
     */
    public function http( $sURL, $sMethod, $sPostFields=NULL) {
        
        add_filter( 
            'fetch_tweets_filter_http_response_cache_name', 
            array( __CLASS__, 'replyToGetCacheNameSanitized' ), 
            10, 
            3 
        );

        switch ( $sMethod ) {
            case 'POST':
                $_oHTTP     = new FetchTweets_HTTP_Post( 
                    $sURL,
                    $this->iCacheDuration,
                    array(
                        'body'  => $sPostFields,    // encoded string 
                    ) + $this->aHTTPArguments
                );
                break;
            case 'DELETE':
                $sURL = empty($sPostFields)
                    ? $sURL
                    : "{$sURL}?{$sPostFields}";
                $_oHTTP     = new FetchTweets_HTTP_Delete( 
                    $sURL,
                    $this->iCacheDuration,
                    $this->aHTTPArguments
                );
                break;
            default:
            case 'GET':
                $_oHTTP     = new FetchTweets_HTTP_Get( 
                    $sURL,
                    $this->iCacheDuration,
                    $this->aHTTPArguments                    
                );
                break;
        }
        $_sResponse = $_oHTTP->get( 
            $this->iCachingMode     // 3 is set not to set a cache by default. A separate cache handler class handles caching except credentials.
        );  
        
        // Must update the properties. The status code is referred when redirected back from twitter.com in the API authentication process.
        $this->http_code = $_oHTTP->getStatusCode();
        $this->url = $sURL; 
        
        remove_filter( 
            'fetch_tweets_filter_http_response_cache_name', 
            array( __CLASS__, 'replyToGetCacheNameSanitized' ), 
            10
        );        
        
        return $_sResponse;
        
    }
        
        /**
         * @todo    Examine whether the static scope helps to avoid multiple calls of callbacks.
         * @callback    filter      fetch_tweets_filter_http_response_cache_name
         * @return      string
         */
        static public function replyToGetCacheNameSanitized( $sCacheName, $sOriginalName, $sRequestType ) {
            $_sURL = remove_query_arg(
                array(
                    'oauth_nonce',
                    'oauth_signature',
                    'oauth_signature_method',
                    'oauth_timestamp',
                    'oauth_version',
                    
                    // Do not remove these as multiple accounts need to store their own results.
                    // 'auth_consumer_key',
                    // 'oauth_token', 
                ),
                $sOriginalName // url
            );
            return filter_var( $_sURL, FILTER_VALIDATE_URL )
                ? 'url_type_md5_' . md5( $sRequestType . '_' . $_sURL )
                : $_sURL;
                
        }

}