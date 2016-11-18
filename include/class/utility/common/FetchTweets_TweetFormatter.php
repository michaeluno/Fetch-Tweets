<?php
/**
 * Fetch Tweets
 * @copyright   Copyright (c) 2013-2016, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * 
 */

/**
 * Provides methods to format tweets.
 * @since       2.5.0
 */
class FetchTweets_TweetFormatter extends FetchTweets_PluginUtility {
    
    static private $___aMedia = array(
        'media_url'         => null, 'media_url_https'   => null,
        'url'               => null, 'display_url'       => null,
        'expanded_url'      => null, 'type'              => null,
        'id'                => null, 'id_str'            => null,                
        'indices'           => null, 'sizes'             => null,  
    );    
    
    /**
     * Returns the Twitter media files to the tweet text.
     * 
     * Used to format a tweet in a twitter response array.
     * 
     * @remark          Currently only photos are supported.
     * @since           1.2.0
     */ 
    static public function getTwitterMedia( array $aMedia ) {

        $_aOutput = array();
        foreach( $aMedia as $_aMedium ) {
            
            // avoid undefined index warnings.
            $_aMedium = $_aMedium + self::$___aMedia;
            
            if ( 'photo' !== $_aMedium[ 'type' ] || ! $_aMedium[ 'media_url' ] ) { 
                continue; 
            }
            
            $_aOutput[] = "<div class='fetch-tweets-media-photo'>"
                    . "<a href='" . esc_url( $_aMedium[ 'expanded_url' ] ) . "'>"
                        . "<img "
                                . "src='" . esc_url( is_ssl() ? $_aMedium[ 'media_url_https' ] : $_aMedium[ 'media_url' ] ) . "' "
                                . "alt='" . esc_attr( __( 'Twitter Media', 'fetch-tweets' ) ) . "' "
                            . "/>"
                    . "</a>"
                . "</div><!-- fetch-tweets-media-photo -->";

        }
        return empty( $_aOutput ) 
                ? ''
                : "<div class='fetch-tweets-media'>" 
                    . implode( PHP_EOL, $_aOutput ) 
                . "</div><!-- fetch-tweets-media -->";
        
    }    
        
    /**
     * Converts plain urls to a hyper-link.
     * 
     * There are urls in the tweet text. So they need to be converted into hyper links.
     */
    static public function getURLsClickable( $sTweet, $aURLs ) {
                
        foreach( ( array ) $aURLs as $_aURLDetails ) {
            
            $_aURLDetails = $_aURLDetails + array(    // avoid undefined index warnings.
                'url' => null,
                'expanded_url' => null,
                'display_url' => null,
            );

            $sTweet = str_replace( 
                $_aURLDetails['url'],    // needle 
                "<a href='" . esc_url( $_aURLDetails['expanded_url'] ) . "' target='_blank' rel='nofollow'>{$_aURLDetails['display_url']}</a>",     // replace
                $sTweet     // haystack
            );    
            
        }
        return $sTweet;
        
    }
    

    /**
     * Converts media links in the tweet text.
     */
    static public function getMediaClickable( $sTweet, $aMedia ) {
        foreach( ( array ) $aMedia as $_aDetails ) {
            $_aDetails = $_aDetails + self::$___aMedia;
            $sTweet = str_replace( 
                $_aDetails[ 'url' ],    // needle 
                "<a href='" . esc_url( $_aDetails[ 'expanded_url' ] ) . "' target='_blank' rel='nofollow'>"
                    . $_aDetails[ 'display_url' ]
                    . "</a>",     // replace
                $sTweet     // haystack
            );    
        }
        return $sTweet;
    }

    
    /**
     * Converts hashtags into hyper links.
     * 
     * There are urls in the tweet text. So we need to convert them into hyper links.
     */
    static public function getHashTagsClickable( $sTweet, $aHashTags ) {
        
        foreach( ( array ) $aHashTags as $_aDetails ) {
            
            $_aDetails = $_aDetails + array(    // avoid undefined index warnings.
                'full_text'      => null,
                'indices'   => null,
            );
            
            $sTweet = preg_replace( 
                '/#(\Q' . $_aDetails['full_text'] . '\E)(\W|$)/',     // needle
                '<a href="' . esc_url( 'https://twitter.com/search?q=%23$1&src=hash' ) . '" target="_blank" rel="nofollow">#$1</a>$2',    // replacement
                $sTweet     // haystack
            );
            
        }
        return $sTweet;
        
    }
    static public function getUsersClickable( $sTweet, $aMentions ) {
        
        // There are urls in the tweet text. So they need to be converted into hyper links.
        foreach( ( array ) $aMentions as $_aDetails ) {
            
            $_aDetails = $_aDetails + array(    // avoid undefined index warnings.
                'screen_name'   => null,
                'name'          => null,
                'id'            => null, 
                'id_str'        => null,
                'indices'       => null,
            );
            
            $sTweet = preg_replace( 
                '/@(\Q' . $_aDetails['screen_name'] . '\E)(\W|$)/i',     // needle, case insensitive
                '<a href="' . esc_url( 'https://twitter.com/$1' ) . '" target="_blank" rel="nofollow">@$1</a>$2',    // replacement
                $sTweet     // haystack
            );
            
        }
        return $sTweet;
        
    }    
    
    static public function  getMediaLinksRemoved( $sTweet, $aMedia ) {
        foreach( ( array ) $aMedia as $_aDetails ) {
            $_aDetails = $_aDetails + self::$___aMedia;
            $sTweet = str_replace( 
                $_aDetails[ 'url' ],  // needle 
                '',                   // replace
                $sTweet               // haystack
            );    
        }
        return $sTweet;
    }    

    /**
     * 
     * url format example: 
     *     http://a0.twimg.com/profile_images/.../..._normal.jpeg
     *     https://si0.twimg.com/profile_images/../..._normal.jpeg        
     * @see            https://dev.twitter.com/docs/user-profile-images-and-banners
     */
    static public function getProfileImageSizeAdjusted( $sURL, $iImageSize ) {

        if ( empty( $sURL ) ) { 
            return $sURL; 
        }
        
        $iImageSize = ! is_numeric( $iImageSize ) ? 48 : $iImageSize;
        
        $sNeedle = '/\/.+\K(_normal)(?=(\..+$)|$)/';
        if ( $iImageSize <= 24 ) {
            return preg_replace( $sNeedle, '_mini', $sURL );
        }
        if ( $iImageSize <= 48 ) {
            return $sURL;
        }
        if ( $iImageSize <= 73 ) {
            return preg_replace( $sNeedle, '_bigger', $sURL );
        }
        return preg_replace( $sNeedle, '', $sURL );    // the original picture size.
        
    }
    
}
