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
 * Formats tweet visuals.
 */
class FetchTweets_Output_Tweet___Format___Visual extends FetchTweets_PluginUtility {
    
    private $___aTweets = array();
    
    private $___aArguments = array();
    
    private $___oOption;
    
    /**
     * Sets up properties.
     */
    public function __construct( $aTweet, $aArguments ) {
        
        $this->___aTweet           = $this->getAsArray( $aTweet );
        $this->___aArguments       = $aArguments;
        $this->___oOption          = FetchTweets_Option::getInstance();
        $this->___bIsSSL           = is_ssl();        
        
    }
    
    /**
     * @return      array
     */
    public function get() {
        return $this->___getTweetVisualsFormatted(
            $this->___aTweet,
            $this->___aArguments[ 'avatar_size' ]
        );
    }
    
        static private $___aEntities = array(
            'hashtags'      => null,
            'symbols'       => null,
            'urls'          => null,
            'user_mentions' => null,
            'media'         => null,        
        );
        /*
         * @return      array
         */
        private function ___getTweetVisualsFormatted( array $aTweet, $iProfileImageSize ) {
            
            // If it is a retweet.
            if ( isset( $aTweet[ 'retweeted_status' ][ 'full_text' ] ) ) {
                $aTweet[ 'retweeted_status' ] = $this->___getTweetVisualsFormatted( $aTweet[ 'retweeted_status' ], $iProfileImageSize );
            }
                
            // If media is present, the media urls will be removed.
            $aTweet = $this->___getEmbeddedMediaReplaced( 
                $aTweet, 
                $this->___aArguments[ 'twitter_media' ], 
                $this->___aArguments[ 'external_media' ] 
            );                
                
            // Make the urls in the text hyper-links.
            if ( isset( $aTweet[ 'full_text' ], $aTweet[ 'entities' ] ) ) {    
                $aTweet[ 'entities' ]  = $aTweet[ 'entities' ] + self::$___aEntities;
                $aTweet[ 'full_text' ] = $this->___getURLsClickable( $aTweet[ 'full_text' ], $aTweet[ 'entities' ][ 'urls' ] );
                $aTweet[ 'full_text' ] = $this->___getMediaClickable( $aTweet[ 'full_text' ], $aTweet[ 'entities' ][ 'media' ] );
                $aTweet[ 'full_text' ] = $this->___getHashTagsClickable( $aTweet[ 'full_text' ], $aTweet[ 'entities' ][ 'hashtags' ] );
                $aTweet[ 'full_text' ] = $this->___getUsersClickable( $aTweet[ 'full_text' ], $aTweet[ 'entities' ][ 'user_mentions' ] );
            }
                                    
            // Adjust the profile image size.
            if ( isset( $aTweet[ 'user' ] ) ) {
                $aTweet[ 'user' ] = $aTweet[ 'user' ] + array(
                    'profile_image_url'         => null,         
                    'profile_image_url_https'   => null,
                );                
                $aTweet[ 'user' ][ 'profile_image_url' ]        = $this->___getProfileImageSizeAdjusted( $aTweet[ 'user' ][ 'profile_image_url' ], $iProfileImageSize );
                $aTweet[ 'user' ][ 'profile_image_url_https' ]  = $this->___getProfileImageSizeAdjusted( $aTweet[ 'user' ][ 'profile_image_url_https' ], $iProfileImageSize );
            }   
            
            // The request set `extended` for the rendering mode.
            // @see https://dev.twitter.com/overview/api/upcoming-changes-to-tweets#rendering-modes
            $aTweet[ 'text' ] = $aTweet[ 'full_text' ];
            return $aTweet;
            
        }

            /**
             * Replaces the media links with the embedded element.
             * 
             * This is supposed to be called from the front-end. So if the external embedded media element does not exist in the response array, do nothing. 
             * The background caching system will take care of it.
             * 
             * @since       2.3.1
             * @since       2.4.0       Changed not to add to the text element as the text only consists of pure text without HTML tags. The media will be addes to the media element.
             * @since       2.5.0       Changed it to return a formatted tweet rather than modifying the referenced variable.
             * @return      array
             */
            private function ___getEmbeddedMediaReplaced( $aTweet, $bTwitterMedia, $bExternalMedia ) {
                
                if ( ! isset( $aTweet[ 'full_text' ] ) ) { 
                    return $aTweet; 
                }

                $aTweet[ '_media' ] = '';
          
                if (
                    'replace_media_with_message' === $this->___oOption->aOptions[ 'sensitive_material' ][ 'possibly_sensitive' ]
                    && $aTweet[ 'possibly_sensitive' ]
                ) {
                    $aTweet[ '_media' ] .= "<p>" . __( 'The media may contain sensitive material.', 'fetch-tweets' ) . "</p>";
                    return $aTweet;
                }
                
                // Insert external media files at the bottom of the tweet.
                if ( $bExternalMedia ) {
                    // the plugin inserts this element in the background
                    $aTweet[ '_media' ] .= $this->getElement( $aTweet, array( 'entities', 'embed_external_media' ), '' );
                }
            
                // Insert twitter media files at the bottom of the tweet. 
    // FetchTweets_Debug::log( $aMedia );                 
                if ( $bTwitterMedia ) {
    // @todo handle `sextended_entities`      
                    
                    // the plugin inserts this element in the background
                    if ( isset( $aTweet[ 'entities' ][ 'embed_twitter_media' ] ) ) {                        
                        $aTweet[ '_media' ]   .= $aTweet[ 'entities' ][ 'embed_twitter_media' ]; 
                    } else {
                        $_aMedia               = $this->getElementAsArray( $aTweet, array( 'extended_entities', 'media' ) );
                        $aTweet[ '_media' ]    = $this->___getTwitterMedia( $_aMedia );
                        $aTweet[ 'full_text' ] = $this->___getMediaLinksRemoved( $aTweet[ 'full_text' ], $_aMedia );
                    }
                        
                }            
                return $aTweet;
                
            }
            
                /**
                 * Returns the Twitter media files to the tweet text.
                 * 
                 * @remark          Currently only photos are supported.
                 * @since           1.2.0
                 */ 
                private function ___getTwitterMedia( array $aMedia ) {

                    $_aOutput = array();
                    foreach( $aMedia as $_aMedium ) {
                        
                        // avoid undefined index warnings.
                        $_aMedium = $_aMedium + self::$___aMedia;
                        
                        if ( 'photo' !== $_aMedium[ 'type' ] || ! $_aMedium[ 'media_url' ] ) { 
                            continue; 
                        }
                        
                        $_aOutput[] = "<div class='fetch-tweets-media-photo'>"
                                . "<a href='" . esc_url( $_aMedium['expanded_url'] ) . "'>"
                                    . "<img "
                                            . "src='" . esc_url( $this->___bIsSSL ? $_aMedium[ 'media_url_https' ] : $_aMedium[ 'media_url' ] ) . "' "
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
            private function ___getURLsClickable( $sTweet, $aURLs ) {
                        
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
            
            static private $___aMedia = array(
                'media_url'         => null, 'media_url_https'   => null,
                'url'               => null, 'display_url'       => null,
                'expanded_url'      => null, 'type'              => null,
                'id'                => null, 'id_str'            => null,                
                'indices'           => null, 'sizes'             => null,  
            );
            /**
             * Converts media links in the tweet text.
             */
            private function ___getMediaClickable( $sTweet, $aMedia ) {
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
            private function  ___getMediaLinksRemoved( $sTweet, $aMedia ) {
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
             * Converts hashtags into hyper links.
             * 
             * There are urls in the tweet text. So we need to convert them into hyper links.
             */
            private function ___getHashTagsClickable( $sTweet, $aHashTags ) {
                
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
            private function ___getUsersClickable( $sTweet, $aMentions ) {
                
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
    
            /**
             * 
             * url format example: 
             *     http://a0.twimg.com/profile_images/.../..._normal.jpeg
             *     https://si0.twimg.com/profile_images/../..._normal.jpeg        
             * @see            https://dev.twitter.com/docs/user-profile-images-and-banners
             */
            private function ___getProfileImageSizeAdjusted( $strURL, $intImageSize ) {

                if ( empty( $strURL ) ) { return $strURL; }
                
                $intImageSize = ! is_numeric( $intImageSize ) ? 48 : $intImageSize;
                
                $strNeedle = '/\/.+\K(_normal)(?=(\..+$)|$)/';
                if ( $intImageSize <= 24 ) {
                    return preg_replace( $strNeedle, '_mini', $strURL );
                }
                if ( $intImageSize <= 48 ) {
                    return $strURL;
                }
                if ( $intImageSize <= 73 ) {
                    return preg_replace( $strNeedle, '_bigger', $strURL );
                }
                return preg_replace( $strNeedle, '', $strURL );    // the original picture size.
                
            }
 
}
