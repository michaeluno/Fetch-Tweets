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
class FetchTweets_AdminPage_Setting_Authentication extends FetchTweets_AdminPage_Tab_Base {
 
   /**
     * Called when the tab loads.
     * 
     */
    public function replyToLoadTab( $oFactory ) {
    
        add_action( "do_form_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToDoForm' ) );
    
        // Form sections  
        new FetchTweets_AdminPage_Setting_Authentication_Key(
            $oFactory,
            $this->sPageSlug,
            array(
                'section_id'    => 'authentication_keys',
                'tab_slug'      => 'authentication',
                'title'         => __( 'Authentication Keys', 'fetch-tweets' ),
                'description'   => __( 'These keys are required to process oAuth requests of the twitter API.', 'fetch-tweets' ),
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