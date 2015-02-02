<?php
/**
 * Fetch Tweets
 * 
 * Fetches and displays tweets from twitter.com.
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Defines an in-page tab.
 * 
 * @since       2.4.5
 */
class FetchTweets_AdminPage_Setting_TwitterConnect extends FetchTweets_AdminPage_Tab_Base {

    /**
     * Called when the tab loads.
     * 
     */
    public function replyToLoadTab( $oFactory ) {
    
        add_action( "do_form_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToDoForm' ) );
    
        // Form sections  
        new FetchTweets_AdminPage_Setting_TwitterConnect_Authentication(
            $oFactory,
            $this->sPageSlug,
            array(
                'section_id'    => 'twitter_connect',   
                'tab_slug'      => 'twitter_connect',
                'title'         => __( 'Authenticate', 'fetch-tweets' ),
            )     
        );
    
    }
    
    /**
     * Called when the form in the tab is about to be rendered.
     * 
     * @remark      do_form_ + page slug + _ + tab slug
     */
    public function replyToDoForm( $oFactory ) {
        
        $_oAPIVerificationStatus = new FetchTweets_TwitterAPI_VerificationStatus( $GLOBALS['oFetchTweets_Option'] );
        $_oAPIVerificationStatus->render();
        
    }
    
}