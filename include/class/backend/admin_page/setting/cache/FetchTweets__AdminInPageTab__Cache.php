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
 * Defines a tab.
 * 
 * @since       2.5.0
 */
class FetchTweets__AdminInPageTab__Cache extends FetchTweets__AdminInPageTab__Base {
    
    /**
     * @return      array
     * @since       2.5.0
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'tab_slug'     => 'cache',
            'title'        => __( 'Cache', 'fetch-tweets' ),
        );
    }

    protected function _load( $oFactory ) {
        new FetchTweets__FormSection__ClearCache( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__Cache( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
    }
    
    protected function _do( $oFactory ) {}
    
}