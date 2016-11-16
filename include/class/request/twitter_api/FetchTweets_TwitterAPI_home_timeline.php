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
class FetchTweets_TwitterAPI_home_timeline extends FetchTweets_TwitterAPI_Base {
    
    protected $_aRateLimitPath = array( 'statuses', '/statuses/home_timeline' );
    
    protected $_aTargetElementPath = array();
    
    /**
     * @since       2.5.0
     * @see         https://dev.twitter.com/docs/api/1.1/get/statuses/home_timeline
     * @return      string
     */
    protected function _getRequestURI() {
        
        return add_query_arg( 
            array(
                'include_entities'   => 1,
                'count'             => $this->getElement( $this->_aArguments, array( 'count' ), 100 ),  // The maximum number is 200 but it likely exceeds 1mb and causes MySQL error
                'include_rts'       => ( integer ) ( boolean ) $this->getElement( $this->_aArguments, array( 'include_rts' ), false ),
                'exclude_replies'   => ( integer ) ( boolean ) $this->getElement( $this->_aArguments, array( 'exclude_replies' ), false ),                

// @todo examine whether twitter API accepts these keys to be sent.
                // the following keys are for a plugin internal use to generate unique cache by request URI, 
                // not part of Twitter API request.
                'consumer_key'       => $this->_aCredentials[ 'consumer_key' ],
                'consumer_secret'    => $this->_aCredentials[ 'consumer_secret' ],
                'access_token'       => $this->_aCredentials[ 'access_token' ],
                'access_secret'      => $this->_aCredentials[ 'access_secret' ],
            ), 
            "https://api.twitter.com/1.1/statuses/home_timeline.json" 
        );

    }
    
}
