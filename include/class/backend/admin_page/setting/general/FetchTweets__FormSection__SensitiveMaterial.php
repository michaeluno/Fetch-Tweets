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
class FetchTweets__FormSection__SensitiveMaterial extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'sensitive_material',
            'title'         => __( 'Sensitive Materials', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array(
                'field_id'      => 'possibly_sensitive',
                'title'         => __( 'Possibly Sensitive Materials', 'fetch-tweets' ),
                'type'          => 'radio',
                'label'         => array(
                    'do_nothing'                 => __( 'Do nothing.', 'fetch-tweets' ),
                    'remove'                     => __( 'Do not display the tweet.', 'fetch-tweets' ),
                    'replace_media_with_message' => __( 'Replace the media element with a message.', 'fetch-tweets' ),
                ),
                'label_min_width'   => '100%',
                'default'       => 'do_nothing',
                'description'   => array(
                    __( 'Decide which policy should be applied to tweets containing sensitive materials such as pornography.', 'fetch-tweets' ),
                    __( 'Please note that checking sensitive contents is not entirely reliable.', 'fetch-tweets' ),
                ),
            ),
        );
    }
        
}
