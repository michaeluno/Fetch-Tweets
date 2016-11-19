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
class FetchTweets__FormSection__oEmbed extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'oembed',
            'title'         => __( 'Embedded Media', 'fetch-tweets' ),
            'description'   => __( 'Decide how embedded representations from third party sites should be handled.', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array(
                'field_id'          => 'enabled',
                'title'             => __( 'oEmbed', 'fetch-tweets' ),
                'type'              => 'checkbox',
                'label'             => __( 'Replace external media links with embedded elements.', 'fetch-tweets' ),
                'default'           => true,
            ),        
            array(
                'field_id'          => 'discover',
                'title'             => __( 'Discover', 'fetch-tweets' ),
                'type'              => 'checkbox',
                'label'             => __( 'Attempt to search unknown oEmbed providers. Some major sites that WordPress natively handles such as YouTube and Instagram do not need this option to be enabled.', 'fetch-tweets' ),
                'default'           => false,
            ),
            array(
                'field_id'          => 'cache_discover',
                'title'             => __( 'Cache Discover', 'fetch-tweets' ),
                'type'              => 'checkbox',
                'label'             => __( 'Whether to cache http requests for the above oEmbed provider discovery routines.', 'fetch-tweets' ),            
                'default'           => true,
            ),
            array(
                'field_id'          => 'cache_duration',
                'title'             => __( 'Cache Duration', 'fetch-tweets' ),
                'type'              => 'size',
                'units'          => array(
                    3600      => __( 'hour(s)', 'fetch-tweets' ),
                    86400     => __( 'day(s)', 'fetch-tweets' ),
                    604800    => __( 'week(s)', 'fetch-tweets' ),
                ),
                'attributes'   => array(
                    'size'  => array(
                        'min'   => 1
                    ),
                ),
                'default'       => array(
                    'size'  => 1,
                    'unit'  => 86400,
                ),
            ),            
        );
    }
        
}
