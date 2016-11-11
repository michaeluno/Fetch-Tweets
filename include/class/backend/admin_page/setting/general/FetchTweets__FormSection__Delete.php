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
class FetchTweets__FormSection__Delete extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'delete',
            'title'         => __( 'Delete Settings', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array(
                'field_id'          => 'delete_upon_uninstall',
                'title'             => __( 'Upon Plugin Uninstallation', 'fetch-tweets' ),
                'type'              => 'checkbox',
                'label'             => __( 'Delete options when the plugin gets uninstalled.', 'fetch-tweets' ),
            ),        
        );
    }
        
}
