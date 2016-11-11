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
class FetchTweets__FormSection__Export extends FetchTweets__FormSection__Base {

    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'export',
            'title'         => __( 'Export', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array( 
                'field_id'          => 'export_options',
                'title'             => __( 'Export Options', 'fetch-tweets' ),
                'type'              => 'export',
                'value'             => __( 'Download', 'fetch-tweets' ),
                'save'              => false,                
            ),
        );
    }
        
}
