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
class FetchTweets__AdminInPageTab__Test extends FetchTweets__AdminInPageTab__Base {
    
    /**
     * @return      array
     * @since       2.5.0
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'tab_slug'     => 'test',
            'title'        => __( 'Tests', 'fetch-tweets' ),
            'order'        => 110,                        
        );
    }

    protected function _load( $oFactory ) {
        new FetchTweets__FormSection__TableSizes( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__InstallTables( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__UninstallTables( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__TestHTTPRequest( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
    }
    
    protected function _do( $oFactory ) {}
    
}