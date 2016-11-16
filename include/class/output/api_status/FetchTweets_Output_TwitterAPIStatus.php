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
 * Handles rendering outputs.
 * 
 * @since             2.5.0
 */
class FetchTweets_Output_TwitterAPIStatus extends FetchTweets_Output_Base {

    /**
     * @return      string
     */
    public function get() {
        
        $_sOutput  = $this->___getConnectionStatus( $this->_aArguments );
        $_sOutput .= $this->___getRateLimitStatuses( $this->_aArguments );
        return $_sOutput;
        
    }
        private function ___getConnectionStatus( $aStatus ) {
            
            $_bIsConnected = ( boolean ) $this->getElement( $aStatus, 'id' );
            $_sScreenName  = $this->getElement( $aStatus, 'screen_name', '' );
            
            $_sOutput  = '';
            $_sOutput .= "<h3>" . __( 'Status', 'fetch-tweets' ) . "</h3>";
            $_sOutput .= "<table class='form-table auth-status'>"
                    . "<tbody>"
                        . "<tr valign='top'>"
                            . "<th scope='row'>"
                                . __( 'Status', 'fetch-tweets' )
                            . "</th>"
                            . "<td>"
                                . ( $_bIsConnected 
                                    ? '<span class="authenticated">' . __( 'Authenticated', 'fetch-tweets' ) . '</span>'
                                    : '<span class="unauthenticated">' . __( 'Not authenticated', 'fetch-tweets' ) . '</span>'
                                )
                            . "</td>"
                        . "</tr>"
                        
                        . "<tr valign='top'>"
                            . "<th scope='row'>"
                                . __( 'Screen Name', 'fetch-tweets' )
                            . "</th>"
                            . "<td>" 
                                . $_sScreenName 
                            . "</td>"
                        . "</tr>"
                    . "</tbody>"
                . "</table>";
            return $_sOutput;
            
        }
        private function ___getRateLimitStatuses( $aStatus ) {   
            return "<h3>" . PHP_EOL
                    . __( 'Request Limits', 'fetch-tweets' ) . PHP_EOL
                . "</h3>" . PHP_EOL
                . "<table class='form-table auth-status'>" . PHP_EOL
                    . "<tbody>" . PHP_EOL
                    . $this->___getRateLimitRows( isset( $aStatus['resources'] ) ? $aStatus['resources'] : array() )
                    . "</tbody>" . PHP_EOL
                . "</table>" . PHP_EOL;            
        }
            private function ___getRateLimitRows( array $aStatusResources ) {
                
                $_aTranslation = array(
                    'statuses'                      => __( 'Statuses', 'fetch-tweets' ),
                    'lists'                         => __( 'Lists', 'fetch-tweets' ),
                    'search'                        => __( 'Search', 'fetch-tweets' ),
                    '/lists/subscribers'            => __( 'Subscribers', 'fetch-tweets' ),
                    '/lists/list'                   => __( 'List', 'fetch-tweets' ),
                    '/lists/memberships'            => __( 'Memberships', 'fetch-tweets' ),
                    '/lists/ownerships'             => __( 'Ownerships', 'fetch-tweets' ),
                    '/lists/subscriptions'          => __( 'Subscriptions', 'fetch-tweets' ),
                    '/lists/members'                => __( 'Members', 'fetch-tweets' ),
                    '/lists/subscribers/show'       => __( 'Subscribers Show', 'fetch-tweets' ),
                    '/lists/statuses'               => __( 'Statuses', 'fetch-tweets' ),
                    '/lists/members/show'           => __( 'Members Show', 'fetch-tweets' ),
                    '/lists/show'                   => __( 'Show', 'fetch-tweets' ),                    
                    '/search/tweets'                => __( 'Tweets', 'fetch-tweets' ),    
                    '/statuses/mentions_timeline'   => __( 'Mentions Timeline', 'fetch-tweets' ),    
                    '/statuses/lookup'              => __( 'Lookup', 'fetch-tweets' ),    
                    '/statuses/show/:id'            => __( 'Show ID', 'fetch-tweets' ),    
                    '/statuses/oembed'              => __( 'Oembed', 'fetch-tweets' ),    
                    '/statuses/retweeters/ids'      => __( 'Retweeters IDs', 'fetch-tweets' ),    
                    '/statuses/home_timeline'       => __( 'Home Timeline', 'fetch-tweets' ),    
                    '/statuses/user_timeline'       => __( 'User Timeline', 'fetch-tweets' ),    
                    '/statuses/friends'             => __( 'Friends', 'fetch-tweets' ),
                    '/statuses/retweets/:id'        => __( 'Retweets ID', 'fetch-tweets' ),    
                    '/statuses/retweets_of_me'      => __( 'Retweets Of Me', 'fetch-tweets' ),    
                );
                $_aTranslation = apply_filters( 'fetch_tweets_filter_rate_limit_status_translation', $_aTranslation );
                
                $_aOutput = array();
                foreach( $aStatusResources as $__sResourceKey => $__aStatusResource ) {                    
                    $_aOutput[] = $this->___getRateLimitRow( $__sResourceKey, $__aStatusResource, $_aTranslation );
                }
                return implode( PHP_EOL, $_aOutput );
                
            }        
                private function ___getRateLimitRow( $sResourceKey, $aStatusResource, $aTranslation ) {
                                    
                    return "<tr valign='top'>" . PHP_EOL
                            . "<th scope='row'>"    
                                . ( isset( $aTranslation[ $sResourceKey ] ) ? $aTranslation[ $sResourceKey ] : $sResourceKey )
                            . "</th>"
                            . "<td>"
                                . $this->___getRateLimitResouceTable( $aStatusResource, $aTranslation )
                            . "</td>"
                        . "</tr>" . PHP_EOL;
                                    
                }
                    private function ___getRateLimitResouceTable( $aStatusResource, $aTranslation ) {
                    
                        return "<table class='form-table status-resources'>" . PHP_EOL
                                . "<tbody>" . PHP_EOL
                                    . $this->___getRateLimitResouceRows( $aStatusResource, $aTranslation )
                                . "</tbody>" . PHP_EOL
                            . "</table>" . PHP_EOL;

                        return print_r( $aStatusResource, true );
                        
                    }
                        private function ___getRateLimitResouceRows( $aStatusResource, $aTranslation ) {
                            
                            $_aOutput = array();
                            foreach( $aStatusResource as $__sStatusKey => $__aStatus ) {
                                $_aOutput[] = $this->___getRateLimitResourceRow( $__sStatusKey, $__aStatus, $aTranslation );
                            }
                            return implode( PHP_EOL, $_aOutput );
                            
                        }
                            private function ___getRateLimitResourceRow( $sStatusKey, $aStatus, $aTranslation ) {
                                
                                $_iPercentage       = round( $aStatus['remaining'] / $aStatus['limit'] * 100 );                
                                return "<tr valign='top'>" . PHP_EOL
                                        . "<th scope='row'>"    
                                            . ( 
                                                isset( $aTranslation[ $sStatusKey ] )
                                                    ? $aTranslation[ $sStatusKey ]
                                                    : $sStatusKey 
                                            )
                                        . "</th>"
                                        . "<td>"
                                            . "<div class='progress-bar button button-secondary' style='margin-bottom: 1em; padding: 0; width: 100%; height: 1em; background-color: #fff;'>" 
                                                . "<div class='button button-primary' style='margin: 0; padding: 0; width: {$_iPercentage}%; height: inherit; background-color:#5dade2';>"
                                                . "</div>"
                                            . "</div>"
                                            . "<div class='progress-bar-label'>"
                                                . $this->___getReadableRateLimitStatus( $aStatus )
                                            . "</div>"
                                        . "</td>"
                                    . "</tr>" . PHP_EOL;
                                
                            }
                                private function ___getReadableRateLimitStatus( array $aStatus ){
                                    
                                    static $_iOffsetSeconds;
                                    $_iOffsetSeconds = $_iOffsetSeconds ? $_iOffsetSeconds : get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
                                    
                                    if ( ! isset( $aStatus['remaining'], $aStatus['limit'], $aStatus['reset'] ) ) {
                                        return __( 'n/a', 'fetch-tweets' );
                                    }
                                                        
                                    return $aStatus['remaining'] . ' / ' . $aStatus['limit'] . "&nbsp;&nbsp;&nbsp;"
                                        . "( " 
                                            . __( 'Will be reset at', 'fetch-tweets' ) . ' ' . date( "F j, Y, g:i a" , $aStatus['reset'] + $_iOffsetSeconds )
                                        . " )"
                                        ;
                                    
                                }
    
}
