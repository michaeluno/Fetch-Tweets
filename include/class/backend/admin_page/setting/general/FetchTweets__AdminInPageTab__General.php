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
class FetchTweets__AdminInPageTab__General extends FetchTweets__AdminInPageTab__Base {
    
    /**
     * @return      array
     * @since       2.5.0
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'tab_slug'     => 'general',
            'title'        => __( 'General', 'fetch-tweets' ),
            'order'        => 20,     
        );
    }

    protected function _load( $oFactory ) {
        new FetchTweets__FormSection__Default( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__SensitiveMaterial( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__ContentSecurity( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__oEmbed( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__Search( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__Delete( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
        new FetchTweets__FormSection__Submit( $oFactory, $this->_sPageSlug, $this->_sTabSlug );
    }

}
