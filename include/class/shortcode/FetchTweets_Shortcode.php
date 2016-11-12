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
 * Handles the shortcode.
 */
class FetchTweets_Shortcode {
    
    /**
     * Registers the shortcode.
     */
    public function __construct( $sShortCode ) {
        add_shortcode( $sShortCode, array( $this, '_replyToGetOutput' ) );
    }
    
    /**
     * Returns the output by the given argument.
     */
    public function _replyToGetOutput( $aArgs ) {
        return fetchTweets( $aArgs, false );
    }    

}
