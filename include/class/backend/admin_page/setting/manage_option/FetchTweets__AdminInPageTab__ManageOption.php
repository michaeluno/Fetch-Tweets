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
class FetchTweets__AdminInPageTab__ManageOption extends FetchTweets__AdminInPageTab__Base {
    
    /**
     * @return      array
     * @since       2.5.0
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'tab_slug'     => 'manage_option',
            'title'        => __( 'Manage Options', 'fetch-tweets' ),
            'order'        => 100,                        
        );
    }

    protected function _load( $oFactory ) {
        new FetchTweets__FormSection__Export( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__Import( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__Reset( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
    }
    
    protected function _do( $oFactory ) {}
    
}