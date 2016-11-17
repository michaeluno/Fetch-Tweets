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
        
        $this->___aTweets          = $this->getAsArray( $aTweets );
        $this->___aArguments       = $aArguments;
        $this->___oOption          = FetchTweets_Option::getInstance();
        
    }
    
    /**
     * @return      array
     */
    public function get() {
FetchTweets_Debug::log( $this->___aTweets );    
        $_aTweets = $this->___getTweetsFormatted( $this->___aTweets );
                
        // Sort by time - the array is passed by reference.
        $this->___sortTweets( $_aTweets, $this->___aArguments[ 'sort' ] ); 

        // Truncate the array.
        $this->___truncateTweets( $_aTweets, $this->___aArguments[ 'count' ] );
        
        // Take care of embedded media - do this after truncating the array as this is slow.
        foreach( $_aTweets as $_isIndex => $__aTweet ) {
            $_oVisualFormatter     = new FetchTweets_Output_Tweet___Format___Visual( $__aTweet, $this->___aArguments );
            $_aTweets[ $_isIndex ] = $_oVisualFormatter->get();
        }

        return $_aTweets;

    }
    
        /**
         * Drops unnecessary tweets and formats the creation time.
         * 
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
                                            
                // Check if it is a retweet. If a retweet is not allowed, drop it.
                if ( isset( $_aTweet[ 'retweeted_status' ][ 'full_text' ] ) ) {
                    if ( isset( $this->___aArguments[ 'include_rts' ] ) && ! $this->___aArguments[ 'include_rts' ] ) {
                        unset( $aTweets[ $_iIndex ] );
                        continue;
                    }
                    $_aTweet[ 'retweeted_status' ] = $this->___getTweetFormatted( $_aTweet[ 'retweeted_status' ] );
                }
                
                $_aTweet = $this->___getTweetFormatted( $_aTweet );
                            
            }
            return $aTweets;
            
        }     

        
        /**
         * Truncates tweet items.
         */
        private function ___truncateTweets( & $aTweets, $iCount ) {
            if ( ! is_numeric( $iCount ) ) {
                return;
            }
            array_splice( $aTweets, ( integer ) $iCount );
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
         * 
         * @remark            The profile image size won't be passed unless the call is made from a widget or shortcode with direct argument.
         * In other words, for preview pages, the profile image url needs to be taken cared of separately.
         * @remark            It's possible that a response array of a custom query can be passed. This means that it might not be an array of tweets.
         */
        private function ___getTweetFormatted( $aTweet, $iProfileImageSize=48 ) {
            
            // Convert the 'created_at' value to be numeric time.
            if ( isset( $aTweet[ 'created_at' ] ) ) {
                $aTweet[ 'created_at' ] = strtotime( $aTweet[ 'created_at' ] );        
            }

            return $aTweet + array(
                'possibly_sensitive' => null,
            );
            
        }            
 
}
