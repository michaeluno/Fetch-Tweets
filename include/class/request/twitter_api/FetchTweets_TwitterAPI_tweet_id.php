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
class FetchTweets_TwitterAPI_tweet_id extends FetchTweets_TwitterAPI_screen_name {
    
    protected $_aRateLimitPath = array( 'statuses', '/statuses/show/:id' );
    
    /**
     * Do not set a path to enclose the response in an array.
     */
    protected $_aTargetElementPath = null;
    
    /**
     * @remark      The `tweet_id` argument supports multiple screen names to be passed.
     * @return      array
     * @since       2.5.0
     */
    public function get() {
        return $this->_getMultipleSetsByArgument( 'tweet_id' );
    }
  
    
    /**
     * 
     * @see         https://dev.twitter.com/docs/api/1.1/get/statuses/show/%3Aid
     * @since       2.5.0
     * @return      string
     */
    protected function _getRequestURI() {
        
        return add_query_arg(                 
            array(
                'id' => $this->getElement( $this->_aArguments, array( 'tweet_id' ), 0 ),
            ), 
            "https://api.twitter.com/1.1/statuses/show.json"
        );

    }
    
}
