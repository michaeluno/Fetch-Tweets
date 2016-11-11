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
class FetchTweets__AdminInPageTab__Misc extends FetchTweets__AdminInPageTab__Base {
    
    /**
     * @return      array
     * @since       2.5.0
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'tab_slug'     => 'misc',
            'title'        => __( 'Misc', 'fetch-tweets' ),
            'order'        => 30,
        );
    }

    protected function _load( $oFactory ) {
        new FetchTweets__FormSection__Capability( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
    }
    

    
}