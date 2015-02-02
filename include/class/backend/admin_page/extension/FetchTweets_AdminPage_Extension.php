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
 * Defines an admin page.
 * 
 * @since       2.4.5
 */
class FetchTweets_AdminPage_Extension extends FetchTweets_AdminPage_Page_Base {

    /**
     * Called when the page loads.
     * 
     */
    public function replyToLoadPage( $oFactory ) {
        
        // Add in-page tabs.
        new FetchTweets_AdminPage_Extension_FeedList(
            $oFactory,
            $this->sPageSlug,
            array(
                'tab_slug'     => 'get_extensions',
                'title'        => __( 'Get Extensions', 'fetch-tweets' ),
                'order'        => 10,                
            )         
        );
        
        add_action( "do_before_{$this->sPageSlug}", array( $this, 'replyToDoBeforePage' ) );
        
    }
        
    /**
     * Called before the page gets rendered.
     * @remark      do_before_ + page slug
     */
    public function replyToDoBeforePage( $oFactory ) {   
        $oFactory->setPageTitleVisibility( false );
    }    
    
}