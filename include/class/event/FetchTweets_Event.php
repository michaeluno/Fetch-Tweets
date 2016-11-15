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
 * Event handler.
 *  
 * @since        1.0.0
 */
final class FetchTweets_Event extends FetchTweets_PluginUtility {
    
    /**
     * Handles events in the background.
     */
    public function __construct() {
 
        $_aEventActionClassNames = array(
            'FetchTweets__Action_HTTPCacheRenewal',
            'FetchTweets__Action_TwitterAPIResponseCacheRenewal',
            'FetchTweets__Action_TransientRenewal',
            'FetchTweets__Action_SimplePieCacheRenewal',
            'FetchTweets__Action_oEmbedUpdate',
        );
        foreach( $_aEventActionClassNames as $_sClassName ) {
            new $_sClassName;
        }
        
        // This must be called after the above action hooks are added.
        $_oOption = FetchTweets_Option::getInstance();
        if ( 'intense' === $_oOption->get( array( 'cache_settings', 'caching_mode' ) ) ) {
            new FetchTweets_Cron(
                apply_filters(
                    'fetch_tweets_filter_plugin_cron_actions',
                    array(
                        'fetch_tweets_action_transient_renewal',
                        'fetch_tweets_action_transient_add_oembed_elements',
                        'fetch_tweets_action_simplepie_renew_cache',
                        'fetch_tweets_action_http_cache_renewal',
                        'fetch_tweets_action_twitter_api_response_cache_renewal',
                    )
                )
            );    
        } else {
            if ( FetchTweets_Cron::isBackground() ) {
                exit;
            }
        }
                
        // Redirects
        if ( $this->getElement( $_GET, 'fetch_tweets_link' ) ) {
            $_oRedirect = new FetchTweets_Redirects;
            $_oRedirect->go( $_GET['fetch_tweets_link'] );    // will exit there.
        }
            
        // Draw the cached image.
        if ( $this->getElement( $_GET, 'fetch_tweets_image' ) && is_user_logged_in() ) {            
            $_oImageLoader = new FetchTweets_ImageHandler( 'FTWS' );
            $_oImageLoader->draw( $_GET['fetch_tweets_image'] );
            exit;
        }            
            
    }
                  
}
