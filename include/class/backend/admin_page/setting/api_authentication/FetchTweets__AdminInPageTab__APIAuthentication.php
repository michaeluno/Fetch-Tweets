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
class FetchTweets__AdminInPageTab__APIAuthentication extends FetchTweets__AdminInPageTab__Base {
    
    /**
     * @return      array
     * @since       2.5.0
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'tab_slug'     => 'twitter_connect',    // the oAuth connection page
            'title'        => __( 'Authentication', 'fetch-tweets' ),
            'order'        => 1,          
        );
    }

    protected function _load( $oFactory ) {
        add_action( "do_form_{$this->_sPageSlug}_{$this->_sTabSlug}", array( $this, 'replyToDoForm' ) );
        new FetchTweets__FormSection__APIAuthentication( $oFactory, $this->_sPageSlug, $this->_sTabSlug );

    }

    /**
     * Called when the form in the tab is about to be rendered.
     * 
     * @remark      do_form_ + page slug + _ + tab slug
     */
    public function replyToDoForm( $oFactory ) {
        
        $_oAPIVerificationStatus = new FetchTweets_TwitterAPI_VerificationStatus();
        $_oAPIVerificationStatus->render();
        
    }
    
}
