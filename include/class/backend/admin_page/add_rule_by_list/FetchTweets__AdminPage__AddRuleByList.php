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
 * Defines an admin page.
 * 
 * @since       2.4.5
 * @since       2.5.0       Renamed from `FetchTweets_AdminPage_AddRuleByList`.
 */
class FetchTweets__AdminPage__AddRuleByList extends FetchTweets__AdminPage__PageBase {

    protected function _getArguments( $oFactory ) {        
        return array(
            'page_slug'     => 'fetch_tweets_add_rule_by_list',
            'title'         => __( 'Add Rule by List', 'fetch-tweets' ),
            'screen_icon'   => FetchTweets_Commons::getPluginURL( '/asset/image/screen_icon_32x32.png' ),
            'order'         => 40,                    
        );
    }
    
    /**
     * Called when the page loads.
     * 
     */
    protected function _loadPage( $oFactory ) {
                
        new FetchTweets__FormSection__AddRuleByList( $oFactory, $this->_sPageSlug );
               
    }
    
    
}
