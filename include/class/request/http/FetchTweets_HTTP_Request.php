<?php
/**
 * Fetch Tweets
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Performs HTTP request with the Post method.
 * 
 * 
 * @since       2.5.0       
 */
class FetchTweets_HTTP_Request extends FetchTweets_HTTP_Base {

    /**
     * Stores the request type.
     * 
     * Change this property to mark a cache item in the database.
     */
    protected $_sRequestType = 'wp_remote_request';
 
    /**
     * Performs HTTP request.
     * 
     * @since       1.0.0
     */    
    protected function _getHTTPResponse( $sURL, array $aArguments ) {
        return function_exists( 'wp_safe_remote_request' )
            ? wp_safe_remote_request( $sURL, $aArguments )
            : wp_remote_request( $sURL, $aArguments );
    }    
    
}
