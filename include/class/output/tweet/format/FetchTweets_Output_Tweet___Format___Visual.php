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
class FetchTweets_Output_Tweet___Format___Visual extends FetchTweets_TweetFormatter {
    
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
            
            // For JSON feeds, still the 'full_text' element is not set but uses 'text'.
            if ( isset( $aTweet[ 'text' ] ) && ! isset( $aTweet[ 'full_text' ] ) ) {
                $aTweet[ 'full_text' ] = $aTweet[ 'text' ];
            }
            
            // If it is a retweet.
            if ( isset( $aTweet[ 'retweeted_status' ][ 'full_text' ] ) || isset( $aTweet[ 'retweeted_status' ][ 'text' ] ) ) {
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
                $aTweet[ 'full_text' ] = $this->getURLsClickable( $aTweet[ 'full_text' ], $aTweet[ 'entities' ][ 'urls' ] );
                $aTweet[ 'full_text' ] = $this->getMediaClickable( $aTweet[ 'full_text' ], $aTweet[ 'entities' ][ 'media' ] );
                $aTweet[ 'full_text' ] = $this->getHashTagsClickable( $aTweet[ 'full_text' ], $aTweet[ 'entities' ][ 'hashtags' ] );
                $aTweet[ 'full_text' ] = $this->getUsersClickable( $aTweet[ 'full_text' ], $aTweet[ 'entities' ][ 'user_mentions' ] );
            }
                                    
            // Adjust the profile image size.
            if ( isset( $aTweet[ 'user' ] ) ) {
                $aTweet[ 'user' ] = $aTweet[ 'user' ] + array(
                    'profile_image_url'         => null,         
                    'profile_image_url_https'   => null,
                );                
                $aTweet[ 'user' ][ 'profile_image_url' ]        = $this->getProfileImageSizeAdjusted( $aTweet[ 'user' ][ 'profile_image_url' ], $iProfileImageSize );
                $aTweet[ 'user' ][ 'profile_image_url_https' ]  = $this->getProfileImageSizeAdjusted( $aTweet[ 'user' ][ 'profile_image_url_https' ], $iProfileImageSize );
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
                if ( $bTwitterMedia ) {
// @todo handle `extended_entities`      
                    
                    $_aMedia               = $this->getElementAsArray( $aTweet, array( 'extended_entities', 'media' ) );
                    if ( isset( $aTweet[ 'entities' ][ 'embed_twitter_media' ] ) ) {                        
                        // the plugin inserts this element in the background
                        $aTweet[ '_media' ]   .= $aTweet[ 'entities' ][ 'embed_twitter_media' ];                       
                    } else {
                        $aTweet[ '_media' ]   .= $this->getTwitterMedia( $_aMedia );
                    }
                    $aTweet[ 'full_text' ] = $this->getMediaLinksRemoved( $aTweet[ 'full_text' ], $_aMedia );
                        
                }            
                return $aTweet;
                
            }
 
}
