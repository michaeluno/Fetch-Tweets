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
 * Provides an abstract base for adding form sections.
 * 
 * @since       2.5.0   
 */
abstract class FetchTweets__FormSection__Base extends FetchTweets__AdminElementBase {

    protected $_sPageSlug;
    
    protected $_sTabSlug;
    
    protected $_sSectionID;

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oFactory, $sPageSlug='', $sTabSlug='' ) {
        
        $this->_oFactory     = $oFactory;
        $this->_sPageSlug    = $sPageSlug ? $sPageSlug : $this->_sPageSlug;
        $this->_sTabSlug     = $sTabSlug ? $sTabSlug : $this->_sTabSlug;
        $this->_aArguments   = $this->_getArguments( $oFactory );
        $this->_sSectionID   = $this->_sSectionID
            ? $this->_sSectionID
            : $this->getElement( $this->_aArguments, array( 'section_id' ), '' );
            
        $this->_construct( $oFactory );
            
        if ( ! $this->_sSectionID ) {
            return;
        }
        $this->___addSection( $oFactory, $this->_sSectionID, $this->_aArguments );

    }
    
        private function ___addSection( $oFactory, $sSectionID, array $aArguments ) {
            
            $oFactory->addSettingSections( $aArguments );        
            
            // Set the target section id
            $oFactory->addSettingFields( $sSectionID );
            
            // Set field-sets.
            foreach( ( array ) $this->_getFields( $oFactory ) as $_aFieldset ) {
                $_aFieldset[ 'tab_slug' ] = $this->getElement( $_aFieldset, 'tab_slug', $this->_sTabSlug );                
                $oFactory->addSettingFields( $_aFieldset );
            }
            
            add_filter( 
                'validation_' . $oFactory->oProp->sClassName . '_' . $sSectionID,
                array( $this, 'replyToValidate' ), 
                10, 
                4 
            );
            
        }
    
    /**
     * @return      array
     */
    protected function _getFields( $oFactory ) {
        return array();
    }
    
    /**
     * @return      array
     * @callback    filter      validation_{class name}_{section id}
     */
    public function replyToValidate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        return $this->_validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo );
    }
    
    protected function _validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        return $aInputs;
    }
    
}