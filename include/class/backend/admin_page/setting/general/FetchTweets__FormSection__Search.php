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
class FetchTweets__FormSection__Search extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'search',
            'title'         => __( 'Search', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array(
                'field_id'          => 'is_searchable',
                'title'             => __( 'Rules', 'fetch-tweets' ),
                'type'              => 'checkbox',
                'label'             => __( 'Make the preview pages of created rules searchable with the WordPress search form.', 'fetch-tweets' ),
            ),        
            array(  // single button
                'field_id'          => 'submit_general_settings',
                'type'              => 'submit',
                'before_field'      => "<div class='right-button'>",
                'after_field'       => "</div>",
                'label_min_width'   => 0,
                'label'             => __( 'Save Changes', 'fetch-tweets' ),
                'attributes'        => array(
                    'class'    => 'button button-primary',
                ),
                'save'              => false,
            ) 
        );
    }
        
}
