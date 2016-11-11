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
 * Defines a tab which is hidden to fill twitter API authentication keys in the form.
 * 
 * @since       2.5.0
 */
class FetchTweets__AdminInPageTab__APIAuthenticationKey extends FetchTweets__AdminInPageTab__Base {
    
    /**
     * @return      array
     * @since       2.5.0
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'tab_slug'          => 'authentication',    // the manual auth keys input page
            'title'             => __( 'Authentication', 'fetch-tweets' ),
            'parent_tab_slug'   => 'twitter_connect',
            'show_in_page_tab'  => false,    
        );
    }

    protected function _load( $oFactory ) {
        add_action( "do_form_{$this->_sPageSlug}_{$this->_sTabSlug}", array( $this, 'replyToDoForm' ) );
        new FetchTweets__FormSection__APIAuthenticationKey( $oFactory, $this->_sPageSlug, $this->_sTabSlug );

    }
    
    /**
     * Called when the form in the tab is about to be rendered.
     * 
     * @callback        action      do_form_{page slug}_{tab slug}
     */
    public function replyToDoForm( $oFactory ) {
        
        $_oAPIVerificationStatus = new FetchTweets_TwitterAPI_VerificationStatus( FetchTweets_Option::getInstance() );
        $_oAPIVerificationStatus->render();
        
    }    

}
