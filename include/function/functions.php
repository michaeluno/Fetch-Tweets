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
 * Retrieve tweets.
 * 
 * @param       array       $aArguments
 * @param       integer     $iReturnType        
 *  0: tweet output string which a template is applied.
 *  1: void but prints tweets with a template.
 *  2: fetched tweets array.
 * @return      void|string|array
 */
function fetchTweets( $aArguments, $iReturnType=1 ) {
    
    $iReturnType = ( integer ) $iReturnType;
    $_sOutput    = '';
    if ( ! class_exists( 'FetchTweets_Output_Tweet' ) ) {
        $_sOutput = __( 'The class has not been loaded yet. Use this function after the <code>plugins_loaded</code> hook.', 'fetch-tweets' );
        if ( 1 === $iReturnType ) {
            echo $_sOutput;
            return;
        }
        if ( 0 === $iReturnType ) {
            return $_sOutput;
        }
        return array( 'error' => $_sOutput );
                
    }
    
    $_oFetch   = new FetchTweets_Output_Tweet( $aArguments );
    if ( 1 === $iReturnType ) {
        echo $_oFetch->get();
        return;
    } 
    if ( 0 === $iReturnType ) {
        return $_oFetch->get();
    }
    return $_oFetch->getTweets();
    
}

/**
 * Returns the image url of the specified size.
 * 
 * The image URL format is as bellows.
 *     original:    http(s)://pbs.twimg.com/profile_images/.../[...].jpg
 *     73px by 73px: http(s)://pbs.twimg.com/profile_images/.../[...]_bigger.jpg
 *    48px by 48px: http(s)://pbs.twimg.com/profile_images/.../[...]_normal.jpg 
 *    24px by 24px: http(s)://pbs.twimg.com/profile_images/.../[...]_mini.jpg
 * @see            https://dev.twitter.com/docs/api/1/get/users/profile_image/%3Ascreen_name
 * @see            https://dev.twitter.com/docs/user-profile-images-and-banners
 * @remark        Assume this function is called in loops of tweet element outputs in templates.
 * @since        2.2.1
 */
if ( ! function_exists( 'getTwitterProfileImageURLBySize' ) ) :
function getTwitterProfileImageURLBySize( $sProfileImageURLNormal, $iImageSize ) {
        
    // Parts
    $_aURLParts  = parse_url( $sProfileImageURLNormal );
    $_aPathParts = pathinfo( $_aURLParts['path'] ) + array( 'extension' => null );

    // Path
    $_sPathPartWOFileName = preg_replace( '/[^\/]*$/', '', $_aURLParts['path'] );    // remove sub-string after the last slash
    
    // File name without extension.
    $_sFileNameWOExt = preg_replace( '/_[^_]*$/', '', $_aPathParts['filename'] );    // remove sub-string after the last underscore including the underscore.
    
    // File name suffix
    $_sSuffix = '';
    if ( $iImageSize <= 24 ) {
        $_sSuffix = '_mini';
    } else if ( $iImageSize <= 48 ) {
        $_sSuffix = '_normal';
    } else if ( $iImageSize <= 73 ) {
        $_sSuffix = '_bigger';
    } 
    if ( ! $_aPathParts['extension'] ) {
        $_sSuffix = '';
    }
    
    // Result
    return $_aURLParts['scheme'] . "://"
        . $_aURLParts['host']
        . $_sPathPartWOFileName
        . $_sFileNameWOExt
        . $_sSuffix
        . ( $_aPathParts['extension'] ? '.' . $_aPathParts['extension'] : '' );
        
}
endif;
