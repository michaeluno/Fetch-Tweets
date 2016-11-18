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
 * Provides methods to retrieve direct tweet output arguments.
 * 
 * @since       2.5.0
 */
class FetchTweets_Output_Tweet___Argument_Direct extends FetchTweets_Output_Tweet___Argument_Base {
        
    /**
     * Formats direct arguments.
     * 
     * Direct arguments here refers to user-input arguments such as parameters of the short-code the `fecthTweets()` function, and the plugin widgets.
     * 
     * @return      array
     */
    public function get() {
        return $this->___getDirectArgumentsFormatted( $this->_aArguments );
    }
        /**
         * Formats the direct argument array. 
         * 
         * Here not merging with the default values. It is assumed this arguments are directly set by the user via shortocde, widget, or the PHP function.
         * 
         * @return      array
         * @since       2.5.0
         */
        private function ___getDirectArgumentsFormatted( $aArguments ) {
            
            if ( $this->getElement( $aArguments, 'get', false ) ) {
                $aArguments = $_GET + $aArguments;
            }
           
            $aArguments[ 'id' ] = $this->___getRuleIDs( $aArguments );
            return $aArguments;
            
        }    
            /**
             * @return      array
             */
            private function ___getRuleIDs( $_aArguments ) {
                
                // only to avoid undefined index warnings
                $_aArguments = $_aArguments + $this->getDefaults();
                
                $_aTags    = $this->___getTags( $_aArguments );   
                if ( ! empty( $_aTags ) ) {
                    return $this->___getRuleIDsByTag( $_aTags, $_aArguments );
                }
                
                $_asIDs    = $this->getElement( 
                    $_aArguments, 
                    array( 'id' ), 
                    $this->getElement( $_aArguments, array( 'ids' ), '' )  // default - backward compatibility
                );
                $_aIDs     = is_array( $_asIDs ) 
                    ? $_asIDs 
                    : $this->getStringIntoArray( ( string ) $_asIDs, ',' );
                return $_aIDs;
                
            }
                /**
                 * @return      array
                 */
                private function ___getTags( $aArguments ) {
                    $_asTags    = $this->getElement( 
                        $aArguments, 
                        array( 'tag' ), 
                        $this->getElement( 
                            $aArguments, 
                            array( 'tags' ), // backward compatibility
                            '' 
                        ) 
                    );        
                    // For widgets, the value is already an array.
                    return is_array( $_asTags )
                        ? $_asTags    
                        : $this->getStringIntoArray( ( string ) $_asTags, ',' );
                }
                
                /**
                 * Retrieves Unit (post) IDs from specified taxonomy terms.
                 * @return      array
                 */
                private function ___getRuleIDsByTag( $aTags, $aArguments ) {                    
                    return isset( $aArguments[ 'tag_field_type' ] ) && in_array( strtolower( $aArguments[ 'tag_field_type' ] ), array( 'id', 'slug' ) )
                        ? $this->___getPostIDsByTag( 
                            $aTags, 
                            $aArguments[ 'tag_field_type' ], 
                            trim( $aArguments[ 'operator' ] ) 
                        )
                        : $this->___getPostIDsByTagName( 
                            $aTags,
                            trim( $aArguments[ 'operator' ] ) 
                        );
                        
                }
                    private function ___getPostIDsByTagName( array $aTermNames, $sOperator='AND' ) {
                        
                        $_aTermSlugs = array();
                        foreach( $aTermNames as $_sTermName ) {
                            $_aTerm        = get_term_by( 'name', $_sTermName, FetchTweets_Commons::TagSlug, ARRAY_A );
                            $_aTermSlugs[] = $_aTerm[ 'slug' ];
                        }
                        return $this->___getPostIDsByTag( $_aTermSlugs, 'slug', $sOperator );
                                
                    }
                    /**
                     * @return      array
                     */
                    private function ___getPostIDsByTag( $aTermSlugs, $sFieldType='slug', $sOperator='AND' ) {

                        if ( empty( $aTermSlugs ) ) {
                            return array();
                        }

                        $_oResults   = new WP_Query( 
                            array(
                                'post_type'      => FetchTweets_Commons::PostTypeSlug,    // fetch_tweets
                                'posts_per_page' => -1, // ALL posts
                                'fields'         => 'ids',
                                'tax_query'      => array(
                                    array(
                                        'taxonomy'  => FetchTweets_Commons::TagSlug,    // fetch_tweets_tag
                                        'field'     => $this->___getFieldKeySanitized( $sFieldType ),    // id or slug
                                        'terms'     => $aTermSlugs,    // the array of term slugs
                                        'operator'  => $this->___getOperatorSanitized( $sOperator ),    // 'IN', 'NOT IN', 'AND. If the item is only one, use AND.
                                    )
                                )
                            )
                        );
                        return $_oResults->posts;
                        
                    }
                        /**
                         * @return      string
                         */
                        private function ___getFieldKeySanitized( $sField ) {
                            switch( strtolower( trim( $sField ) ) ) {
                                case 'id':
                                    return 'id';
                                default:
                                case 'slug':
                                    return 'slug';
                            }        
                        }
                        /**
                         * @return      string
                         */
                        private function ___getOperatorSanitized( $sOperator ) {
                            switch( strtoupper( trim( $sOperator ) ) ) {
                                case 'NOT IN':
                                    return 'NOT IN';
                                case 'AND':
                                    return 'AND';
                                default:
                                case 'IN':
                                    return 'IN';                                
                            }
                        }
 
}
