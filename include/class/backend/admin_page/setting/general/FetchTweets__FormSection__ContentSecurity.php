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
class FetchTweets__FormSection__ContentSecurity extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'content_security_policy',
            'title'         => __( 'Content Security Policy', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array(
                'field_id'          => 'disable_warnings',
                'title'             => __( 'Disable Warnings', 'fetch-tweets' ),
                'description'       => __( 'The warnings may appear when you enable the Follow button in the template.', 'fetch-tweets' )
                    . ' ' . __( 'This option is to disable the warnings.', 'fetch-tweets' ),
                'type'              => 'checkbox',
                'label'             => __( 'Disable content security policy warnings in the browser console.', 'fetch-tweets' ),
            )      
        );
    }
        
}
