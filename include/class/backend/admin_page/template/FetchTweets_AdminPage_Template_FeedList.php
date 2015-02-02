<?php
/**
 * Fetch Tweets
 * 
 * Fetches and displays tweets from twitter.com.
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Defines an in-page tab.
 * 
 * @since       2.4.5
 */
class FetchTweets_AdminPage_Template_FeedList extends FetchTweets_AdminPage_Tab_Base {
    
    /**
     * The feed url to fetch.
     * 
     * @since       2.4.5
     */
    private $_sFeedURL = 'http://feeds.feedburner.com/MiunosoftFetchTweetsTemplate';

    /**
     * Called when the tab loads.
     * 
     */
    public function replyToLoadTab( $oFactory ) {
        
        add_action( "do_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToDoTab' ) );
        
    }
    
    
   /**
     * Called in the middle of the tab being rendered.
     * 
     * @remark      do_{page slug}_{tab slug}
     */
    public function replyToDoTab( $oFactory ) {
        
        echo "<p>" 
                . sprintf( __( 'Want your template to be listed here? Send the file to %1$s.', 'fetch-tweets' ), 'wpplugins@michaeluno.jp' ) 
            . "</p>";
        
        echo '<div class="ftws_extension_container">' 
                . $this->_getListOfFeedItems()
            . '</div>';
        
    }    
        /**
         * 
         * @return      string
         */
        private function _getListOfFeedItems() {
            
            $_oFeed         = new FetchTweets_Extensions();
            $_aFeedItems    = $_oFeed->fetchFeed( $this->_sFeedURL );
            if ( empty( $_aFeedItems ) ) {
                return "<h3>" 
                        . __( 'No template has been found.', 'fetch-tweets' ) 
                    . "</h3>";
            }
            
            $_aOutput       = array();
            $_iMaxCols      = 3;
            $_aColumnInfo   = array(
                'bIsRowTagClosed'  => false,
                'iCurrRowPos'      => 0,
                'iCurrColPos'      => 0,
            );
            $_aColumnOption = array (
                'sClassAttr'              => 'fetch_tweets_multiple_columns',
                'sClassAttrGroup'         => 'fetch_tweets_multiple_columns_box',
                'sClassAttrRow'           => 'fetch_tweets_multiple_columns_row',
                'sClassAttrCol'           => 'fetch_tweets_multiple_columns_col',
                'sClassAttrFirstCol'      => 'fetch_tweets_multiple_columns_first_col',
            );                
            foreach( $_aFeedItems as $_sTitle => $_aItem ) {
                
                // Increment the position
                $_aColumnInfo['iCurrColPos']++;
                
                // Enclose the item buffer into the item container
                $_sItem = '<div class="' . $_aColumnOption['sClassAttrCol'] 
                            . ' ftws_col_element_of_' . $_iMaxCols . ' '
                            . ' ftws_extension '
                            . ( 1 == $_aColumnInfo['iCurrColPos']
                                ?  $_aColumnOption['sClassAttrFirstCol']  
                                : '' 
                            )
                            . '"'
                        . '>' 
                        . '<div class="ftws_extension_item">' 
                            . "<h4>{$_aItem['title']}</h4>"
                            . $_aItem['description'] 
                            . "<div class='get-now'>"
                                . "<a href='" . esc_url( $_aItem['strLink'] ) . "' target='_blank' rel='nofollow' class='button button-secondary'>" 
                                    . __( 'Get it Now', 'fetch-tweets' )
                                . "</a>"
                            . "</div>"
                        . '</div>'
                    . '</div>';    
                    
                // If it's the first item in the row, add the class attribute. 
                // Be aware that at this point, the tag will be unclosed. Therefore, it must be closed somewhere. 
                if ( $_aColumnInfo['iCurrColPos'] == 1 ) 
                    $_sItem = '<div class="' . $_aColumnOption['sClassAttrRow']  . '">' . $_sItem;
            
                // If the current column position reached the set max column, increment the current position of row
                if ( $_aColumnInfo['iCurrColPos'] % $_iMaxCols == 0 ) {
                    $_aColumnInfo['iCurrRowPos']++;        // increment the row number
                    $_aColumnInfo['iCurrColPos'] = 0;        // reset the current column position
                    $_sItem .= '</div>';  // close the section(row) div tag
                    $_aColumnInfo['bIsRowTagClosed'] =     True;
                }        
                
                $_aOutput[] = $_sItem;
            
            }
            
            // if the section(row) tag is not closed, close it
            if ( ! $_aColumnInfo['bIsRowTagClosed'] ) { 
                $_aOutput[] .= '</div>';    
            }
            $_aColumnInfo['bIsRowTagClosed'] = true;
            
            // enclose the output in the group tag
            return '<div class="' . $_aColumnOption['sClassAttr'] . ' '
                        .  $_aColumnOption['sClassAttrGroup'] . ' '
                    . '"'
                    . '>'
                        . implode( PHP_EOL, $_aOutput )
                    . '</div>';

        }
    
}