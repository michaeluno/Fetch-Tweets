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
 * Output tweets by applying the template.
 */
class FetchTweets_Output_Tweet___Format extends FetchTweets_PluginUtility {
    
    private $___aTweets = array();
    
    private $___aArguments = array();
    
    private $___oOption;
    
    /**
     * Sets up properties.
     */
    public function __construct( $aTweets, $aArguments ) {
        
        $this->___aTweets    = $this->getAsArray( $aTweets );
        $this->___aArguments = $aArguments;
        $this->___oOption    = FetchTweets_Option::getInstance();
        $this->___bIsSSL     = is_ssl();
        
    }
    
    /**
     * @return      array
     */
    public function get() {
    
        $_aTweets = $this->___getTweetsFormatted( $this->___aTweets );
                
        // Sort by time - the array is passed by reference.
        $this->___sortTweets( $_aTweets, $this->___aArguments[ 'sort' ] ); 

        // Truncate the array.
        $this->___truncateTweets( $_aTweets, $this->___aArguments[ 'count' ] );
        
        // Take care of embedded media - do this after truncating the array as this is slow.
        foreach( $_aTweets as &$__aTweet ) {
            $this->___replceEmbeddedMedia( 
                $__aTweet, 
                $this->___aArguments[ 'twitter_media' ], 
                $this->___aArguments[ 'external_media' ] 
            );
        }
        
        return $_aTweets;

    }
        /**
         * @since       2.5.0
         * @return      array
         */
        private function ___getTweetsFormatted( $aTweets ) {
                    
            // To prevent duplicates.
            $_aProcessedTweetIDs = array();

            foreach( $aTweets as $_iIndex => &$_aTweet ) {
                
                if ( ! $this->___isProcessable( $_aTweet, $_aProcessedTweetIDs ) ) {
                    unset( $aTweets[ $_iIndex ] );
                    continue;
                }
                
                $_aProcessedTweetIDs[] = $_aTweet[ 'id_str' ];
                                            
                // Check if it is a re-tweet.
                if ( isset( $_aTweet[ 'retweeted_status' ][ 'text' ] ) ) {                
                    if ( isset( $this->___aArguments[ 'include_rts' ] ) && ! $this->___aArguments[ 'include_rts' ] ) {
                        unset( $aTweets[ $_iIndex ] );
                        continue;
                    }                
                    $_aTweet[ 'retweeted_status' ] = $this->___getTweetFormatted( 
                        $_aTweet[ 'retweeted_status' ], 
                        $this->___aArguments[ 'avatar_size' ] 
                    );
                }            
                
                $_aTweet = $this->___getTweetFormatted( 
                    $_aTweet, 
                    $this->___aArguments[ 'avatar_size' ] 
                );
                            
            }
            return $aTweets;
            
        }       
        
        /**
         * Truncates tweet items.
         */
        private function ___truncateTweets( & $aTweets, $iCount ) {
            if ( is_numeric( $iCount ) ) {
                array_splice( $aTweets, ( integer ) $iCount );
            }
        }
    
        /**
         * Sorts tweet array elements.
         */
        private function ___sortTweets( & $aTweets, $sOrderedBy='descending' ) {
            switch( strtolower( $sOrderedBy ) ) {
                case 'ascending':
                    uasort( $aTweets, array( $this, '___sortByTimeAscending' ) );
                    break;
                case 'random':
                    shuffle( $aTweets );
                case 'descending':
                default:
                    uasort( $aTweets, array( $this, '___sortByTimeDescending' ) );
                    break;    
            }
        }    
            private function ___sortByTimeDescending( $a, $b ) {    // callback for the uasort() method.
                if ( isset( $a['created_at'], $b['created_at'] ) ) {
                    return ( int ) $b['created_at'] - ( int ) $a['created_at'];
                }
                return 0;
            }            
            private function ___sortByTimeAscending( $a, $b ) {    // callback for the uasort() method.
                if ( isset( $a['created_at'], $b['created_at'] ) ) {
                    return ( int ) $a['created_at'] - ( int ) $b['created_at'];
                }
                return 0;            
            }  
            
        /**
         * Checks if the passed tweet array is processable or not.
         * 
         * @since       2.4.8
         * @return      boolean
         */
        private function ___isProcessable( $aTweet, array $aProcessedTweetIDs=array() ) {
            
            if ( ! is_array( $aTweet ) ) {
                return false;
            }
            if ( ! isset( $aTweet['id_str'] ) ) {
                return false;
            }
            
            // Consider the tweet array is a mush-up made up of multiple rules.
            if ( in_array( $aTweet[ 'id_str' ], $aProcessedTweetIDs ) ) {
                return false;
            }            
            
            // Check sensitive materials
            if ( 
                'remove' === $this->___oOption->get( array( 'sensitive_material', 'possibly_sensitive' ) )
                && $this->getElement( $aTweet, 'possibly_sensitive' )
            ) {
                return false;
            }

            return true;
        }
        
        /**
         * Replaces the media links with the embedded element.
         * 
         * This is supposed to be called from the front-end. So if the external embedded media element does not exist in the response array, do nothing. 
         * The background caching system will take care of it.
         * 
         * @since           2.3.1
         * @since           2.4.0       Changed not to add to the text element as the text only consists of pure text without HTML tags. The media will be addes to the media element.
         */
        private function ___replceEmbeddedMedia( & $aTweet, $fTwitterMedia, $fExternalMedia ) {
            
            if ( ! isset( $aTweet[ 'text' ] ) ) { 
                return; 
            }
// @todo Consider changing this internal element 
            $aTweet[ '_media' ] = '';
      
            if (
                'replace_media_with_message' === $this->___oOption->aOptions[ 'sensitive_material' ][ 'possibly_sensitive' ]
                && $aTweet[ 'possibly_sensitive' ]
            ) {
                $aTweet[ '_media' ] .= "<p>" . __( 'The media may contain sensitive material.', 'fetch-tweets' ) . "</p>";
                return;
            }
            
            // Insert external media files at the bottom of the tweet.
            if ( $fExternalMedia ) {
                // the plugin inserts this element in the background
                $aTweet[ '_media' ] .= $this->getElement( $aTweet, array( 'entities', 'embed_external_media' ), '' );
            }
        
            // Insert twitter media files at the bottom of the tweet. 
            if ( $fTwitterMedia ) {
                $aTweet[ '_media' ] .= isset( $aTweet['entities']['embed_twitter_media'] )
                    ? $aTweet['entities']['embed_twitter_media']    // the plugin inserts this element in the background
                    : $this->___getTwitterMedia( $this->getElementAsArray( $aTweet, array( 'entities', 'media' ) ) );
            }            
            
        }
        
            /**
             * Returns the Twitter media files to the tweet text.
             * 
             * @remark          Currently only photos are supported.
             * @since           1.2.0
             */ 
            private function ___getTwitterMedia( $aMedia ) {
                
                $_aOutput = array();
                foreach( ( array ) $aMedia as $_aMedium ) {
                    
                    // avoid undefined index warnings.
                    $_aMedium = $_aMedium + array(
                        'type'              => null,
                        'expanded_url'      => null,
                        'media_url'         => null,        
                        'media_url_https'   => null,                
                    );
                    
                    if ( 'photo' !== $_aMedium[ 'type' ] || ! $_aMedium[ 'media_url' ] ) { 
                        continue; 
                    }
                    
                    $_aOutput[] = "<div class='fetch-tweets-media-photo'>"
                            . "<a href='" . esc_url( $_aMedium['expanded_url'] ) . "'>"
                                . "<img src='" . esc_url( $this->___bIsSSL ? $_aMedium['media_url_https'] : $_aMedium['media_url'] ) . "' alt='" . esc_attr( __( 'Twitter Media', 'fetch-tweets' ) ) . "'>"
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
         * 
         * @remark            The profile image size won't be passed unless the call is made from a widget or shortcode with direct argument.
         * In other words, for preview pages, the profile image url needs to be taken cared of separately.
         * @remark            It's possible that a response array of a custom query can be passed. This means that it might not be an array of tweets.
         */
        private function ___getTweetFormatted( $aTweet, $iProfileImageSize=48 ) {
            
            // Make the urls in the text hyper-links.
            if ( isset( $aTweet['text'], $aTweet['entities'] ) ) {    
                $aTweet['entities'] = $aTweet['entities'] + array(
                    'hashtags'      => null,
                    'symbols'       => null,
                    'urls'          => null,
                    'user_mentions' => null,
                    'media'         => null,
                );
                $aTweet['text'] = $this->___getURLsClickable( $aTweet['text'], $aTweet['entities']['urls'] );
                $aTweet['text'] = $this->___getMediaClickable( $aTweet['text'], $aTweet['entities']['media'] );    
                $aTweet['text'] = $this->___getHashTagsClickable( $aTweet['text'], $aTweet['entities']['hashtags'] );    
                $aTweet['text'] = $this->___getUsersClickable( $aTweet['text'], $aTweet['entities']['user_mentions'] );
            }
                                    
            // Adjust the profile image size.
            if ( isset( $aTweet['user'] ) ) {
                $aTweet['user'] = $aTweet['user'] + array(
                    'profile_image_url'         => null,         
                    'profile_image_url_https'   => null,
                );                
                $aTweet['user']['profile_image_url']        = $this->___getProfileImageSizeAdjusted( $aTweet['user']['profile_image_url'], $iProfileImageSize );
                $aTweet['user']['profile_image_url_https']  = $this->___getProfileImageSizeAdjusted( $aTweet['user']['profile_image_url_https'], $iProfileImageSize );
            }

            // Convert the 'created_at' value to be numeric time.
            if ( isset( $aTweet['created_at'] ) ) {
                $aTweet['created_at'] = strtotime( $aTweet['created_at'] );        
            }

            return $aTweet + array(
                'possibly_sensitive' => null,
            );
            
        }
    
            /**
             * Converts plain urls to a hyper-link.
             * 
             * There are urls in the tweet text. So they need to be converted into hyper links.
             */
            private function ___getURLsClickable( $sText, $aURLs ) {
                        
                foreach( ( array ) $aURLs as $_aURLDetails ) {
                    
                    $_aURLDetails = $_aURLDetails + array(    // avoid undefined index warnings.
                        'url' => null,
                        'expanded_url' => null,
                        'display_url' => null,
                    );

                    $sText = str_replace( 
                        $_aURLDetails['url'],    // needle 
                        "<a href='" . esc_url( $_aURLDetails['expanded_url'] ) . "' target='_blank' rel='nofollow'>{$_aURLDetails['display_url']}</a>",     // replace
                        $sText     // haystack
                    );    
                    
                }
                return $sText;
                
            }
            /**
             * Converts media links in the tweet text.
             */
            private function ___getMediaClickable( $sText, $aMedia ) {
                
                foreach( ( array ) $aMedia as $_aDetails ) {
                    
                    $_aDetails = $_aDetails + array(    // avoid undefined index warnings.
                        'media_url'         => null,
                        'media_url_https'   => null,
                        'url'               => null,
                        'display_url'       => null,
                        'expanded_url'      => null,
                        'type'              => null,
                        'sizes'             => null,    // array()
                        'id'                => null,
                        'id_str'            => null,
                        'indices'           => null,    // array()
                    );
                    
                    $sText = str_replace( 
                        $_aDetails['url'],    // needle 
                        "<a href='" . esc_url( $_aDetails['expanded_url'] ) . "' target='_blank' rel='nofollow'>{$_aDetails['display_url']}</a>",     // replace
                        $sText     // haystack
                    );    
                }
                return $sText;
                
            }
            /**
             * Converts hashtags into hyper links.
             * 
             * There are urls in the tweet text. So we need to convert them into hyper links.
             */
            private function ___getHashTagsClickable( $sText, $aHashTags ) {
                
                foreach( ( array ) $aHashTags as $_aDetails ) {
                    
                    $_aDetails = $_aDetails + array(    // avoid undefined index warnings.
                        'text'      => null,
                        'indices'   => null,
                    );
                    
                    $sText = preg_replace( 
                        '/#(\Q' . $_aDetails['text'] . '\E)(\W|$)/',     // needle
                        '<a href="' . esc_url( 'https://twitter.com/search?q=%23$1&src=hash' ) . '" target="_blank" rel="nofollow">#$1</a>$2',    // replacement
                        $sText     // haystack
                    );
                    
                }
                return $sText;
                
            }
            private function ___getUsersClickable( $sText, $aMentions ) {
                
                // There are urls in the tweet text. So they need to be converted into hyper links.
                foreach( ( array ) $aMentions as $_aDetails ) {
                    
                    $_aDetails = $_aDetails + array(    // avoid undefined index warnings.
                        'screen_name'   => null,
                        'name'          => null,
                        'id'            => null, 
                        'id_str'        => null,
                        'indices'       => null,
                    );
                    
                    $sText = preg_replace( 
                        '/@(\Q' . $_aDetails['screen_name'] . '\E)(\W|$)/i',     // needle, case insensitive
                        '<a href="' . esc_url( 'https://twitter.com/$1' ) . '" target="_blank" rel="nofollow">@$1</a>$2',    // replacement
                        $sText     // haystack
                    );
                    
                }
                return $sText;
                
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
