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
 * @action      add     fetch_tweets_action_add_oembed_elements_to_api_request_cache
 */
class FetchTweets__Action_oEmbedAPIRequestCacheModification extends FetchTweets__Action_Base {
    
    protected $_sActionName = 'fetch_tweets_action_add_oembed_elements_to_api_request_cache';    
    protected $_iArguments  = 3;
        
    /* Class specific properties */
    private $___aTweets = array();
    private $___oEmbed;
    
    /**
     * The script started time.
     */
    private $___iStarted = 0;
    
    /**
     * An count for updated tweets.
     */
    private $___iUpdated = 0;
    
    /**
     * An offset margin in seconds for oEmbed HTTP requests.
     * @remark  oEmbed http request for each time limit is 5 seconds so this should be longer than that
     */
    private $___iOffset  = 10;
    
    protected function _construct() {
        $this->___iStarted = time();
    }
    
    /**
     * 
     */
    protected function _doAction( /* $_sRequestURI, $_iCacheDuration, $_naTargetElementPath */ ) {
        
        $_aParams             = func_get_args() + array( '', -1, null );
        $_sRequestURI         = $_aParams[ 0 ];
        $_iCacheDuration      = $_aParams[ 1 ];
        $_naTargetElementPath = $_aParams[ 2 ];

        $_oCache              = new FetchTweets_TwitterAPI___CacheHandler;
        $_aResponse           = $_oCache->get( 
            $_sRequestURI,
            -1   // cache duration - do not trigger a cache renewal task and force retrieval.
        );
        
        if ( ! $this->___shouldProceed( $_aResponse ) ) {
            return;
        }
        
        // Time limit
        $this->___oEmbed     = new FetchTweets_oEmbed;
        $this->___iTimeLimit = $this->___getTimeLimit();
        $this->___aTweets    = $this->getTweetsExtracted( $_aResponse, $_naTargetElementPath );
        $this->___iUpdated   = 0;
        
        try {
            if ( $this->___iTimeLimit < time() ) {
                throw new Exception( 'Time is up!' );
            }            
            $this->___setMediaElementsEmbedded( $this->___aTweets );  
          
        }
        catch( Exception $oException ) {
            
            // Timed out. Reschedule the routine.
            $_bScheduled = $this->scheduleWPCronActionOnce(
                $this->_sActionName,
                array( $_sRequestURI, $_iCacheDuration, $_naTargetElementPath )
            );
            if ( $_bScheduled ) {
                new FetchTweets_Event__BackgroundPageload;
            }
            
        }    

        // If no item is updated, no need to update caches in the database.
        if ( ! $this->___iUpdated ) {
            return;
        }        
        // Update the cache.
        $this->___setTweets( $_aResponse, $this->___aTweets, $_naTargetElementPath );
        $_oCache->set( 
            $_sRequestURI,  // cache name
            $_aResponse,    // data
            -1              // cache duration - do not update the expiration column
        );

    }
    
        /**
         * @return      boolean
         */
        private function ___shouldProceed( $_aResponse ) {
            
            // If expired or does not exist, it will be empty.
            if ( empty( $_aResponse ) || $this->___isError( $_aResponse ) ) {
                return false;
            }
            
            // HTTP request time out will be 5 so if the max execution time is too short, do nothing.
            $_iMaxExecution = $this->getAllowedMaxExecutionTime();
            if ( 0 !== $_iMaxExecution && ( $this->___iOffset + 2 ) >= $_iMaxExecution ) {
                return false;
            }
            return true;
        }
        
        /**
         * Determine the time when the script stops.
         * @return      integer
         */
        private function ___getTimeLimit() {
            $_iMaxExecution = $this->getAllowedMaxExecutionTime();            
            return 0 === $_iMaxExecution
                ? $this->___iStarted + 600   // 10 minutes as maximum
                : $this->___iStarted + $_iMaxExecution - $this->___iOffset;
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
         * Update the tweets by inserting embeddable external media elements.
         * @since       2.5.1
         * @return      void
         * @throw
         */
        private function ___setMediaElementsEmbedded( &$aTweets ) {
            
            foreach( $aTweets as $_isIndex => $_aTweet ) {
                                
                // Check if it is a re-tweet.
                if ( isset( $_aTweet[ 'retweeted_status' ][ 'full_text' ] ) ) {
                    $_aTweet[ 'retweeted_status' ] = $this->___getMediaElementEmbedded( $_aTweet[ 'retweeted_status' ] );
                }
                
                // Update the tweet
                $aTweets[ $_isIndex ] = $this->___getMediaElementEmbedded( $_aTweet );
                $this->___iUpdated++;
                
                // Check time
                if ( $this->___iTimeLimit < time() ) {
                    throw new Exception( 'Time is up!' );
                }
                     
            }                    
        
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

                // @todo examine whether it does not have to be extended entities.
                
                // Do not fetch external media if already set.
                if ( isset( $aTweet[ 'entities' ][ 'urls' ] ) && ! isset( $aTweet[ 'entities' ][ 'embed_external_media' ] ) ) {
                    $aTweet[ 'entities' ][ 'embed_external_media' ] = $this->___getExternalMedia( 
                        $aTweet[ 'entities' ][ 'urls' ] 
                    );
                }
                
                // These are media within Twitter.com.
                if ( isset( $aTweet[ 'extended_entities' ][ 'media' ] ) && ! isset( $aTweet[ 'entities' ][ 'embed_twitter_media' ] ) ) {
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
                                        
                    // There are urls in the tweet text. So they need to be converted into hyper links.
                    $_aOutput = array();
                    foreach( ( array ) $aURLs as $__aURLDetails ) {
                        
                        $__aURLDetails = $__aURLDetails + self::$__aEntitiesURLs;
                        if ( empty( $__aURLDetails[ 'expanded_url' ] ) ) { 
                            continue; 
                        }

                        $_sEmbed = $this->___oEmbed->get_html( 
                            $__aURLDetails[ 'expanded_url' ], 
                            array( 
                                'discover' => FetchTweets_Option::get( array( 'oembed', 'discover' ), false ),
                            ) 
                        );
                        
                        if ( $this->___iTimeLimit < time() ) {
                            throw new Exception( 'Time is up!' );
                        }
                        
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
