<?php
/**
 * Fetch Tweets
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Renews HTTP request caches in the background.
 * 
 * @since       2.5.0
 * @action      schedule|add    fetch_tweets_action_add_oembed_elements_to_api_request_cache
 */
class FetchTweets__Action_oEmbedAPIRequestCacheModification extends FetchTweets__Action_Base {
    
    protected $_sActionName = 'fetch_tweets_action_add_oembed_elements_to_api_request_cache';
    
    protected $_iArguments  = 3;
        
    /**
     * 
     */
    protected function _doAction( /* $_sRequestURI, $_iCacheDuration, $naTargetElementPath */ ) {
        
        $_aParams             = func_get_args() + array( '', -1, null );
        $_sRequestURI         = $_aParams[ 0 ];
        $_naTargetElementPath = $_aParams[ 2 ];

        $_oCache = new FetchTweets_TwitterAPI___CacheHandler;        
        
        $_aResponse      = $_oCache->get( 
            $_sRequestURI, 
            -1   // cache duration - do not trigger a cache renewal task and force retrieval.
        );

        // If expired or does not exist, it will be empty.
        if ( empty( $_aResponse ) || $this->___isError( $_aResponse ) ) {
            return;
        }
        
        $_aTweets = $this->getTweetsExtracted( $_aResponse, $_naTargetElementPath );
        $_aTweets = $this->___getMediaElementsEmbedded( $_aTweets );
        $this->___setTweets( $_aResponse, $_aTweets, $_naTargetElementPath );
        $_oCache->set( 
            $_sRequestURI,  // cache name
            $_aResponse,    // data
            -1              // cache duration - do not update the expiration column
        );
        
    }
    
        /**
         * @return          boolean
         */
        private function ___isError( $aResponse ) {
            
            $_aError = $this->getElement( $aResponse, array( 'errors', 0, ) );
            if ( isset( $_aError[ 'message' ], $_aError[ 'code' ] ) ) {                
                return true;
            }
            $_sError = $this->getElement( $aResponse, 'error', '' );
            if ( $_sError ) {
                return true;
            }
            return false;
            
        }     
    
        /**
         * Updates the tweets array in the cache response data.
         */
        private function ___setTweets( & $aResponse, $aTweets, $naTargetElementPath ) {

            // If null, the single tweet array is the response array.
            if ( ! isset( $naTargetElementPath ) ) {
                $aResponse = $aTweets[ 0 ];
                return;
            } 
            
            // If empty, the direct first dimension is the tweets container.
            if ( empty( $naTargetElementPath ) ) {
                $aResponse = $aTweets;
                return;
            }
            
            // Otherwise, update the element of the set path.
            $this->setMultiDimensionalArray( 
                $aResponse, // by reference
                $naTargetElementPath, 
                $aTweets 
            );
        
        }  

        /**
         * @return      array
         */
        static public function getTweetsExtracted( $aResponse, $naTargetElementPath ) {
            
            // If the target element path is not set, enclose the response in an array.
            // This is used by tweet ids. In this case, the entire tweet array is returned by the response.
            if ( ! isset( $naTargetElementPath ) ) {
                return array( self::getAsArray( $aResponse ) );
            }
            
            // Otherwise, fetch tweets reside in the target path.
            return empty( $naTargetElementPath )
                ? self::getAsArray( $aResponse )
                : self::getElementAsArray( $aResponse, $naTargetElementPath );            
            
        }    

        /**
         * Adds the embeddable media elements to the tweets array.
         * 
         * @remark           This should be called from an action event which runs in the background because this takes some time.
         * @since            1.3.0
         */
        private function ___getMediaElementsEmbedded( $aTweets ) {

            foreach( $aTweets as $_isIndex => $_aTweet ) {
                                
                // Check if it is a re-tweet.
                if ( isset( $_aTweet[ 'retweeted_status' ][ 'full_text' ] ) ) {
                    $_aTweet[ 'retweeted_status' ] = $this->___getMediaElementEmbedded( $_aTweet[ 'retweeted_status' ] );
                }
                
                $aTweets[ $_isIndex ] = $this->___getMediaElementEmbedded( $_aTweet );
                        
            }                    
            return $aTweets;
        
        }
            /**
             * Adds the embeddable media element to the single tweet element.
             * 
             * This is a helper method for the above addEmbeddableMediaElements() method.
             * 
             * @since            1.3.0
             * @remark           The element with the keys 'embed_external_media' and 'embed_twitter_media' will be inserted into the 'entities' key element.
             * @return           array            The modified tweet element array.
             */
            private function ___getMediaElementEmbedded( $aTweet ) {
                
                if ( isset( $aTweet[ 'entities' ][ 'urls' ] ) ) {
                    $aTweet[ 'entities' ][ 'embed_external_media' ] = $this->___getExternalMedia( 
                        $aTweet[ 'entities' ][ 'urls' ] 
                    );
                }
                                
                if ( isset( $aTweet[ 'extended_entities' ][ 'media' ] ) ) {
                    $aTweet[ 'entities' ][ 'embed_twitter_media' ] = FetchTweets_TweetFormatter::getTwitterMedia( 
                        $aTweet[ 'extended_entities' ][ 'media' ] 
                    );
                }
                
                return $aTweet;
                
            }    
    
                static private $__aEntitiesURLs = array(
                    'url'           => null,
                    'expanded_url'  => null,
                    'display_url'   => null,
                );
    
                /**
                 * Returns the external media files to the tweet text.
                 * 
                 * @remark          The supported providers depend on the WordPress oEmbed class. It has a filter for the providers so it can be customized.
                 * @since           1.2.0
                 */ 
                private function ___getExternalMedia( $aURLs ) {
                    
                    $_oEmbed = new FetchTweets_oEmbed;
                    
                    // There are urls in the tweet text. So they need to be converted into hyper links.
                    $_aOutput = array();
                    foreach( ( array ) $aURLs as $__aURLDetails ) {
                        
                        $__aURLDetails = $__aURLDetails + self::$__aEntitiesURLs;
                        if ( empty( $__aURLDetails[ 'expanded_url' ] ) ) { 
                            continue; 
                        }
                        
                        $_sEmbed = $_oEmbed->get_html( 
                            $__aURLDetails[ 'expanded_url' ], 
                            array( 
                                // 'discover' => false, 
                            ) 
                        );
      
                        if ( empty( $_sEmbed ) ) { 
                            continue; 
                        }                       
                        $_aOutput[] = "<div class='fetch-tweets-external-media'>"
                                . $_sEmbed
                            . "</div>";

                    }
                    return implode( PHP_EOL, $_aOutput );
                
                }
            
}
