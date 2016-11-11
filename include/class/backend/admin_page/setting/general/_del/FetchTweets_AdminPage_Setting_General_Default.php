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
class FetchTweets_AdminPage_Setting_General_Default extends FetchTweets_AdminPage_Section_Base {

    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            array(
                'field_id'      => 'count',
                'title'         => __( 'Number of Items', 'fetch-tweets' ),
                'help'          => __( 'The number of tweets to display.', 'fetch-tweets' )
                    . __( 'Default', 'fetch-tweets' ) . ': ' . $GLOBALS['oFetchTweets_Option']->aStructure_DefaultParams['count']
                    . __( 'This option corresponds to the <code>count</code> argument value. For instance, with this shortcode, <code>[fetch_tweets id="10" count="30"]</code>, the count value, 30, will override this option. If the <code>count</code> parameter is not set, this option value will be used.', 'fetch-tweets' ),
                'default'       => $GLOBALS['oFetchTweets_Option']->aStructure_DefaultParams['count'],
                'type'          => 'number',
            ),
            array(
                'field_id'      => 'twitter_media',
                'title'         => __( 'Twitter Media', 'fetch-tweets' ),
                'type'          => 'checkbox',
                'label'         => __( 'Display media images posted in the tweet that are recognized as media file by Twitter.' ),
                'help'          => __( 'This option corresponds to the <code>twitter_media</code> argument value. For instance, with this shortcode, <code>[fetch_tweets id="10" count="30"]</code>, the count value, 30, will override this option. If the <code>count</code> parameter is not set, this option value will be used.', 'fetch-tweets' ) . ' '
                    . __( 'Currently only photos are supported by the Twitter API.' ),
                'default'       => $GLOBALS['oFetchTweets_Option']->aStructure_DefaultParams['twitter_media'],
            ),
            array(
                'field_id'      => 'external_media',
                'title'         => __( 'External Media', 'fetch-tweets' ),
                'type'          => 'checkbox',
                'label'         => __( 'Replace media links of external sources to an embedded element.', 'fetch-tweets' ),
                'help'          => __( 'This option corresponds to the <code>external_media</code> argument value. For instance, with this shortcode, <code>[fetch_tweets id="10" count="30"]</code>, the count value, 30, will override this option. If the <code>count</code> parameter is not set, this option value will be used.', 'fetch-tweets' ) . ' '
                    . __( 'Unlike the above media images, there are media links that are not categorized as media by the Twitter API. Thus, enabling this option will attempt to replace them to the embedded elements.', 'fetch-tweets' ) . ' e.g. youtube, vimeo, dailymotion etc.',
                'default'       => $GLOBALS['oFetchTweets_Option']->aStructure_DefaultParams['external_media'],
            )     
        );     
        
        add_filter( "validation_{$oFactory->oProp->sClassName}_{$sSectionID}", array( $this, 'replyToValidate' ), 10, 4 );
        
    }  


    /**
     * Validates the submit data of the 'general' tab of the 'fetch_tweets_settings' page.
     * 
     * @remark      validation_{class name}_{section id}
     */    
    public function replyToValidate( $aInput, $aOriginal, $oFactory, $aSubmitInfo ) {

        $aInput['count'] = $oFactory->oUtil->fixNumber(
            $aInput['count'],
            $GLOBALS['oFetchTweets_Option']->aStructure_DefaultParams['count'],
            1
        );        

        return $aInput;
        
    }    
    
}