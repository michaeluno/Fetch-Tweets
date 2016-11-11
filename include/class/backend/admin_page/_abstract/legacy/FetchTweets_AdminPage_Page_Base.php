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
 * Provides an abstract base for adding pages.
 * 
 * @since       2.4.5
 * @deprecated  2.5.0
 */
abstract class FetchTweets_AdminPage_Page_Base {

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oFactory, $sPageSlug, $sPageTitle, $sScreenIcon='' ) {
        
        $this->oFactory     = $oFactory;
        $this->sPageSlug    = $sPageSlug;
        $this->_addPage( $sPageSlug, $sPageTitle, $sScreenIcon );
        
    }
    
    private function _addPage( $sPageSlug, $sPageTitle, $sScreenIcon ) {
        
        $this->oFactory->addSubMenuItems(
            array(
                'page_slug'     => $sPageSlug,
                'title'         => $sPageTitle,
                'screen_icon'   => $sScreenIcon,
            )                
        );
        add_action( "load_{$sPageSlug}", array( $this, 'replyToLoadPage' ) );
        
    }

    /**
     * Called when the page loads.
     * 
     * @remark      This method should be overridden in each extended class.
     */
    public function replyToLoadPage( $oFactory ) {}
    
}