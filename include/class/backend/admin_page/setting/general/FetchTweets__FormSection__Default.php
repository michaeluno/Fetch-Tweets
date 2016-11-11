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
class FetchTweets__FormSection__Default extends FetchTweets__FormSection__Base {
    
    /**
     * @return      array
     */
    protected function _getArguments( $oFactory ) {
        return array(
            'section_id'    => 'default_values',
            'title'         => __( 'Default Values', 'fetch-tweets' ),
            'help'          => __( 'Set the default option values which will be applied when the argument values are not set.', 'fetch-tweets' )
                . __( 'These values will be overridden by the argument set directly to the widget options or shortcode.', 'fetch-tweets' ),
        );
    }

    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        $_oOption = FetchTweets_Option::getInstance();
        return array(
            array(
                'field_id'      => 'count',
                'title'         => __( 'Number of Items', 'fetch-tweets' ),
                'help'          => __( 'The number of tweets to display.', 'fetch-tweets' )
                    . __( 'Default', 'fetch-tweets' ) . ': ' . $_oOption->aStructure_DefaultParams['count']
                    . __( 'This option corresponds to the <code>count</code> argument value. For instance, with this shortcode, <code>[fetch_tweets id="10" count="30"]</code>, the count value, 30, will override this option. If the <code>count</code> parameter is not set, this option value will be used.', 'fetch-tweets' ),
                'default'       => $_oOption->aStructure_DefaultParams['count'],
                'type'          => 'number',
                'attributes'    => array(
                    'min'   => 1,
                ),
            ),
            array(
                'field_id'      => 'twitter_media',
                'title'         => __( 'Twitter Media', 'fetch-tweets' ),
                'type'          => 'checkbox',
                'label'         => __( 'Display media images posted in the tweet that are recognized as media file by Twitter.' ),
                'help'          => __( 'This option corresponds to the <code>twitter_media</code> argument value. For instance, with this shortcode, <code>[fetch_tweets id="10" count="30"]</code>, the count value, 30, will override this option. If the <code>count</code> parameter is not set, this option value will be used.', 'fetch-tweets' ) . ' '
                    . __( 'Currently only photos are supported by the Twitter API.' ),
                'default'       => $_oOption->aStructure_DefaultParams['twitter_media'],
            ),
            array(
                'field_id'      => 'external_media',
                'title'         => __( 'External Media', 'fetch-tweets' ),
                'type'          => 'checkbox',
                'label'         => __( 'Replace media links of external sources to an embedded element.', 'fetch-tweets' ),
                'help'          => __( 'This option corresponds to the <code>external_media</code> argument value. For instance, with this shortcode, <code>[fetch_tweets id="10" count="30"]</code>, the count value, 30, will override this option. If the <code>count</code> parameter is not set, this option value will be used.', 'fetch-tweets' ) . ' '
                    . __( 'Unlike the above media images, there are media links that are not categorized as media by the Twitter API. Thus, enabling this option will attempt to replace them to the embedded elements.', 'fetch-tweets' ) . ' e.g. youtube, vimeo, dailymotion etc.',
                'default'       => $_oOption->aStructure_DefaultParams['external_media'],
            )  
        );
    }
    
    protected function _validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        $_oOption = FetchTweets_Option::getInstance();
        $aInputs[ 'count' ] = $oFactory->oUtil->getNumberFixed(
            $aInputs[ 'count' ],
            $_oOption->aStructure_DefaultParams[ 'count' ],
            1
        );                
        return $aInputs;
    }
        
}
