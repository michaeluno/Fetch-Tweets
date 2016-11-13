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
class FetchTweets__FormSection__TableSizes extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return             array(
            'section_id'    => 'table_sizes',
            'title'         => __( 'Table Sizes', 'fetch-tweets' ),
            'save'          => false,
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
                
        $_oHTTPRequestTable = new FetchTweets_DatabaseTable_ft_http_requests;                
        return array(         
            array(
                'field_id'          => 'http_requests',
                'title'             => __( 'HTTP Requests', 'fetch-tweets' ),
                'content'           => "<p>"
                        . print_r( $_oHTTPRequestTable->getTableSize(), true )
                    . "</p>",
            ),
            array(
                'field_id'          => 'tweets',
                'title'             => __( 'Tweets', 'fetch-tweets' ),
                'content'           => "<p>"
                        . __( 'Coming soon...', 'fetch-tweets' ) 
                    . "</p>",              
            )                   
        );
    }
        
 
       
}