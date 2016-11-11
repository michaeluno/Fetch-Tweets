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
class FetchTweets__FormSection__Capability extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return             array(
            'section_id'    => 'capabilities',
            'capability'    => 'manage_options',
            'title'         => __( 'Access Rights', 'fetch-tweets' ),
            'description'   => __( 'Set the access levels to the plugin setting pages.', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array(
            array(
                'field_id'          => 'setting_page_capability',
                'title'             => __( 'Capability', 'fetch-tweets' ),
                'description'       => __( 'Select the user role that is allowed to access the plugin setting pages.', 'fetch-tweets' )
                    . __( 'Default', 'fetch-tweets' ) . ': ' . __( 'Administrator', 'fetch-tweets' ),
                'type'              => 'select',
                'capability'        => 'manage_options',
                'label'             => array(                        
                    'manage_options'    => __( 'Administrator', 'responsive-column-widgets' ),
                    'edit_pages'        => __( 'Editor', 'responsive-column-widgets' ),
                    'publish_posts'     => __( 'Author', 'responsive-column-widgets' ),
                    'edit_posts'        => __( 'Contributor', 'responsive-column-widgets' ),
                    'read'              => __( 'Subscriber', 'responsive-column-widgets' ),
                ),
            ),
            array(  // single button
                'field_id'          => 'submit_misc',
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
