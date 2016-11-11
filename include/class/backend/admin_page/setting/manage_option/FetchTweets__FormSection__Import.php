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
class FetchTweets__FormSection__Import extends FetchTweets__FormSection__Base {

    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'import',
            'title'         => __( 'Import', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array( 
                'field_id'          => 'import_options',
                'title'             => __( 'Import Options', 'fetch-tweets' ),
                'type'              => 'import',
                'value'             => __( 'Upload Options', 'fetch-tweets' ),
                'save'              => false,                
            )
        );
    }
        
}