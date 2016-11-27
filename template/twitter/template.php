<?php
/**
 * Available variables passed from the caller script
 * - $aTweets    : the fetched tweet arrays.
 * - $aArguments : the passed arguments such as item count etc.
 * - $aOptions   : the plugin options saved in the database.
 * 
 * @see     https://dev.twitter.com/rest/reference/get/statuses/oembed
 * @see     https://dev.twitter.com/web/embedded-tweets
 * @see     https://dev.twitter.com/web/embedded-tweets/parameters
 */

 /**
  * https://publish.twitter.com/oembed?url=https://twitter.com/[screenname]/status/[tweet id]&hide_thread=1&
  * - widget_type=video               -> <blockquote class="twitter-video">
  * - widget_type=video&hide_tweet=1  -> <blockquote class="twitter-video" data-status="hidden">
  * - hide_media=1                    -> <blockquote class="twitter-tweet" data-cards="hidden">
  */
// 
 
$_sSiteCharset = strtolower( get_bloginfo( 'charset' ) );

$_oUtil = new FetchTweets_PluginUtility;


// Some new setting items are not stored in the database, so merge the saved options with the defined default values.     
$aTemplateOptions = $_oUtil->uniteArrays( 
    $_oUtil->getElementAsArray( $aOptions, 'template_twitter' ), 
    array(
        'width'             => array( 'size' => 100, 'unit' => '%' ),
        'height'            => array( 'size' => 100, 'unit' => '%' ),
        'margins' => array(
            0 => array( 'size' => '', 'unit' => 'px' ),    // top
            1 => array( 'size' => '', 'unit' => 'px' ),    // right
            2 => array( 'size' => '', 'unit' => 'px' ),    // bottom
            3 => array( 'size' => '', 'unit' => 'px' ),    // left
        ),
        'paddings' => array(
            0 => array( 'size' => '', 'unit' => 'px' ),    // top
            1 => array( 'size' => '', 'unit' => 'px' ),    // right
            2 => array( 'size' => '', 'unit' => 'px' ),    // bottom
            3 => array( 'size' => '', 'unit' => 'px' ),    // left
        ),    
        'tweet_maxwidth'     => 550,
        'cards'              => false,
        'hide_conversation'  => false,
        'theme'              => 'light',
        'link_color'         => '#55acee',
        'align'              => 'center',
        'language'           => 'en',
    )
);

$aArguments['width']                     = isset( $aArguments['width'] ) ? $aArguments['width'] : $aTemplateOptions['width']['size'];
$aArguments['width_unit']                = isset( $aArguments['width_unit'] ) ? $aArguments['width_unit'] : $aTemplateOptions['width']['unit'];
$aArguments['height']                    = isset( $aArguments['height'] ) ? $aArguments['height']: $aTemplateOptions['height']['size'];
$aArguments['height_unit']               = isset( $aArguments['height_unit'] ) ? $aArguments['height_unit'] : $aTemplateOptions['height']['unit'];
$aArguments['margin_top']                = isset( $aArguments['margin_top'] ) ? $aArguments['margin_top'] : $aTemplateOptions['margins'][0]['size'];
$aArguments['margin_top_unit']           = isset( $aArguments['margin_top_unit'] ) ? $aArguments['margin_top_unit'] : $aTemplateOptions['margins'][0]['unit'];
$aArguments['margin_right']              = isset( $aArguments['margin_right'] ) ? $aArguments['margin_right'] : $aTemplateOptions['margins'][1]['size'];
$aArguments['margin_right_unit']         = isset( $aArguments['margin_right_unit'] ) ? $aArguments['margin_right_unit'] : $aTemplateOptions['margins'][1]['unit'];
$aArguments['margin_bottom']             = isset( $aArguments['margin_bottom'] ) ? $aArguments['margin_bottom'] : $aTemplateOptions['margins'][2]['size'];
$aArguments['margin_bottom_unit']        = isset( $aArguments['margin_bottom_unit'] ) ? $aArguments['margin_bottom_unit'] : $aTemplateOptions['margins'][2]['unit'];
$aArguments['margin_left']               = isset( $aArguments['margin_left'] ) ? $aArguments['margin_left'] : $aTemplateOptions['margins'][3]['size'];
$aArguments['margin_left_unit']          = isset( $aArguments['margin_left_unit'] ) ? $aArguments['margin_left_unit'] : $aTemplateOptions['margins'][3]['unit'];
$aArguments['padding_top']               = isset( $aArguments['padding_top'] ) ? $aArguments['padding_top'] : $aTemplateOptions['paddings'][0]['size'];
$aArguments['padding_top_unit']          = isset( $aArguments['padding_top_unit'] ) ? $aArguments['padding_top_unit'] : $aTemplateOptions['paddings'][0]['unit'];
$aArguments['padding_right']             = isset( $aArguments['padding_right'] ) ? $aArguments['padding_right'] : $aTemplateOptions['paddings'][1]['size'];
$aArguments['padding_right_unit']        = isset( $aArguments['padding_right_unit'] ) ? $aArguments['padding_right_unit'] : $aTemplateOptions['paddings'][1]['unit'];
$aArguments['padding_bottom']            = isset( $aArguments['padding_bottom'] ) ? $aArguments['padding_bottom'] : $aTemplateOptions['paddings'][2]['size'];
$aArguments['padding_bottom_unit']       = isset( $aArguments['padding_bottom_unit'] ) ? $aArguments['padding_bottom_unit'] : $aTemplateOptions['paddings'][2]['unit'];
$aArguments['padding_left']              = isset( $aArguments['padding_left'] ) ? $aArguments['padding_left'] : $aTemplateOptions['paddings'][3]['size'];
$aArguments['padding_left_unit']         = isset( $aArguments['padding_left_unit'] ) ? $aArguments['padding_left_unit'] : $aTemplateOptions['paddings'][3]['unit'];
$sWidth             = $aArguments['width'] ? "max-width: " . $aArguments['width'] . $aArguments['width_unit'] . "; " : '';
$sHeight            = $aArguments['height'] ? "max-height: " . $aArguments['height'] . $aArguments['height_unit'] . "; " : '';
$sOverflowY         = '100%' === $aArguments['height'] . $aArguments['height_unit'] ? 'overflow-y: hidden; ' : '';     // removes the vertical scroll bar.
$sMarginTop         = empty( $aArguments['margin_top'] ) ? 0 : $aArguments['margin_top'] . $aArguments['margin_top_unit'];
$sMarginRight       = empty( $aArguments['margin_right'] ) ? 0 : $aArguments['margin_right'] . $aArguments['margin_right_unit'];
$sMarginBottom      = empty( $aArguments['margin_bottom'] ) ? 0 : $aArguments['margin_bottom'] . $aArguments['margin_bottom_unit'];
$sMarginLeft        = empty( $aArguments['margin_left'] ) ? 0 : $aArguments['margin_left'] . $aArguments['margin_left_unit'];
$sPaddingTop        = empty( $aArguments['padding_top'] ) ? 0 : $aArguments['padding_top'] . $aArguments['padding_top_unit'];
$sPaddingRight      = empty( $aArguments['padding_right'] ) ? 0 : $aArguments['padding_right'] . $aArguments['padding_right_unit'];
$sPaddingBottom     = empty( $aArguments['padding_bottom'] ) ? 0 : $aArguments['padding_bottom'] . $aArguments['padding_bottom_unit'];
$sPaddingLeft       = empty( $aArguments['padding_left'] ) ? 0 : $aArguments['padding_left'] . $aArguments['padding_left_unit'];
$sMargins           = ( $sMarginTop ? "margin-top: {$sMarginTop}; " : "" ) . ( $sMarginRight ? "margin-right: {$sMarginRight}; " : "" ) . ( $sMarginBottom ? "margin-bottom: {$sMarginBottom}; " : "" ) . ( $sMarginLeft ? "margin-left: {$sMarginLeft}; " : "" );
$sPaddings          = ( $sPaddingTop ? "padding-top: {$sPaddingTop}; " : "" ) . ( $sPaddingRight ? "padding-right: {$sPaddingRight}; " : "" ) . ( $sPaddingBottom ? "padding-bottom: {$sPaddingBottom}; " : "" ) . ( $sPaddingLeft ? "padding-left: {$sPaddingLeft}; " : "" );

$_aContainerAttributes = array(
    'class' => 'fetch-tweets-twitter',
    'style' => $sWidth . $sHeight . $sMargins . $sPaddings . $sOverflowY,
);

echo "<div " . $_oUtil->getAttributes( $_aContainerAttributes ) . ">";
foreach ( $aTweets as $_aDetail ){

    // Check if it's a retweet.
    $_bIsRetweet = isset( $_aDetail['retweeted_status']['text'] );
    if ( $_bIsRetweet && ! $aArgs['include_rts'] ) { 
        continue; 
    }    
    $_aTweet            = $_bIsRetweet ? $_aDetail[ 'retweeted_status' ] : $_aDetail;
    $_aBQTagAttributes  = array(
        'class'             => 'twitter-tweet',
        'data-cards'        => $_oUtil->getElement( $aTemplateOptions, 'cards' )             
            ? 'hidden'  
            : null,
        'data-conversation' => $_oUtil->getElement( $aTemplateOptions, 'hide_conversation' )
            ? 'none'
            : null,
        'data-theme'        => 'dark' === $_oUtil->getElement( $aTemplateOptions, 'theme' )
            ? 'dark'
            : null,
        'data-link-color'   => $_oUtil->getElement( $aTemplateOptions, array( 'link_color' ), null ),
        'data-width'        => $_oUtil->getElement( $aTemplateOptions, array( 'tweet_maxwidth' ), null ),
        'data-align'        => $_oUtil->getElement( $aTemplateOptions, array( 'align' ), 'center' ),
        'data-lang'         => $_oUtil->getElement( $aTemplateOptions, array( 'language' ), 'en' ),
        // 'data-dnt'          => 
    );
    $_aPTagAttributes   = array(
        'lang'  => 'en',
        'dir'   => 'ltr'
    );
    
    $_sTweetURL   = "https://twitter.com/" . $_aTweet[ 'user' ][ 'screen_name' ] . "/status/" . $_aTweet[ 'id_str' ];
    echo "<blockquote " . $_oUtil->getAttributes( $_aBQTagAttributes ) . ">"
            . "<p " . $_oUtil->getAttributes( $_aPTagAttributes ) . ">"
                . $_aTweet[ 'text' ]
            . "</p>"
            . "&mdash; " 
            . $_aDetail[ 'user' ][ 'name' ]
            . ' @(' . $_aDetail[ 'user' ][ 'screen_name' ] . ') '
            . "<a href='" . esc_url( $_sTweetURL ) . "' target='_blank' rel='nofollow'>"
                . $_oUtil->getLegibleTimeDifference( $_aTweet[ 'created_at' ] ) . ' ' . __( 'ago', 'fetch-tweets' )
            . "</a>"            
        . "</blockquote>"
        . "<script async src='//platform.twitter.com/widgets.js' charset='" . $_sSiteCharset . "'></script>";
 
}
echo "</div>";
