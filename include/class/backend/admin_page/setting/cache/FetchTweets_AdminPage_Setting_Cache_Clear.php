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
 * @since       2.4.8       Changed the name from `FetchTweets_AdminPage_Setting_Reset_Cache`.
 */
class FetchTweets_AdminPage_Setting_Cache_Clear extends FetchTweets_AdminPage_Section_Base {

    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            array(    
                'field_id'          => 'clear_caches',
                'title'             => __( 'Clear Tweet Caches', 'fetch-tweets' ),
                'type'              => 'submit',
                'href'              => add_query_arg(
                    $_GET,
                    isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : 'edit.php'
                ),
                'label'             => __( 'Clear', 'fetch-tweets' ),
                'description'   => __( 'If you need to refresh the fetched tweets, clear the caches.', 'fetch-tweets' ),
                'attributes'        => array(
                    'class' => 'button button-secondary',  
                ),
            ),            
            array()            
        );       
        
        add_action( 
            "submit_{$oFactory->oProp->sClassName}_{$sSectionID}_clear_caches",     // action hook name
            array( $this, 'replyToSubmitField' ),   // callback
            10, // priority
            5   // number of parameters
        );
        
    }    

    public function replyToSubmitField() {
        
        $_aParams = func_get_args();
        FetchTweets_WPUtility::clearTransients();
        
        $_oFactory = $_aParams[ 2 ];
        $_oFactory->setSettingNotice( __( 'The caches have been cleared.', 'fetch-tweets' ) );
        
    }
    
}