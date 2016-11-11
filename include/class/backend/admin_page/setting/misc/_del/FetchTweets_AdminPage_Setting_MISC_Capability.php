<?php
/**
 * Fetch Tweets
 * 
 * Fetches and displays tweets from twitter.com.
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Defines an in-page tab.
 * 
 * @since       2.4.5
 */
class FetchTweets_AdminPage_Setting_MISC_Capability extends FetchTweets_AdminPage_Section_Base {

    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
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
            )            
        );     
        
    }    
    
}