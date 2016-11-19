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
 * Defines a form section.
 * 
 * @since       2.5.0   
 */
class FetchTweets__FormSection__Cache extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return             array(
            'section_id'    => 'cache_settings',
            'title'         => __( 'Cache Settings', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array(
                'field_id'       => 'cache_for_errors',
                'title'          => __( 'Cache for Errors', 'fetch-tweets' ),
                'type'           => 'checkbox',
                'label'          => __( 'Cache fetched results even for errors.', 'fetch-tweets' ),
                'description'    => __( 'This reduces the chances to reach the Twitter API rate limit.', 'fetch-tweets' ),
            ),
            array(
                'field_id'       => 'caching_mode',
                'title'          => __( 'Caching Mode', 'fetch-tweets' ),
                'type'           => 'radio',
                'label'          => array(
                    'normal'    => __( 'Normal', 'fetch-tweets' ) . ' - ' . __( 'uses WP Cron.', 'fetch-tweets' ),
                    'intense'   => __( 'Intense', 'fetch-tweets' ) . ' - ' . __( 'uses the plugin caching method.', 'fetch-tweets' ),
                ),
                'after_label'    => '<br />',
                'default'        => 'normal',
            ),
            array(
                'field_id'       => 'clearing_interval',
                'title'          => __( 'Clearing Cache Interval', 'fetch-tweets' ),
                'type'           => 'size',
                'units'          => array(
                    3600      => __( 'hour(s)', 'fetch-tweets' ),
                    86400     => __( 'day(s)', 'fetch-tweets' ),
                    604800    => __( 'week(s)', 'fetch-tweets' ),
                ),
                'description'    => __( 'An interval to clear expired caches.', 'fetch-tweets' ),
                'default'        => array(
                    'size'     => 7,
                    'unit'     => 86400,
                ),
                'attributes'   => array(
                    'size'  => array(
                        'min'   => 1
                    ),
                ),
            ),                
            array(  
                'field_id'          => 'submit_cache_settings',
                'type'              => 'submit',
                'before_field'      => "<div class='right-button'>",
                'after_field'       => "</div>",
                'label_min_width'   => 0,
                'label'             => __( 'Save Changes', 'fetch-tweets' ),
                'attributes'        => array(
                    'class'    => 'button button-primary',
                ),
                'save'              => false,
            ),     
        );
    }
 
    protected function _validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        
        // If a new interval is set, remove the scheduled one. A new schedule is done by a separate handelr class.
        if ( 
            $this->getElement( $aInputs, array( 'clearing_interval', 'size' ), 7 ) * $this->getElement( $aInputs, array( 'clearing_interval', 'unit' ), 86400 )
            !== $this->getElement( $aOldInputs, array( 'clearing_interval', 'size' ), 7 ) * $this->getElement( $aOldInputs, array( 'clearing_interval', 'unit' ), 86400 )
        ) {            
            wp_clear_scheduled_hook( 'fetch_tweets_action_http_cache_removal', array() );
        }
    
        return $aInputs;
        
    }
 
}
