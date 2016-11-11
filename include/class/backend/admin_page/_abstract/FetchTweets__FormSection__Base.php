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
abstract class FetchTweets__FormSection__Base extends FetchTweets_PluginUtility {

    protected $_oFactory;
    
    protected $_sPageSlug;
    
    protected $_sTabSlug;
    
    protected $_sSectionID;

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oFactory, $sPageSLug='', $sTabSlug='' ) {
        
        $this->_oFactory     = $oFactory;
        $this->_sPageSlug    = $sPageSLug ? $sPageSLug : $this->_sPageSlug;
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
            
            foreach( ( array ) $this->_getFields( $oFactory ) as $_aField ) {
                $oFactory->addSettingFields( $_aField );
            }
            
            // Call the user method
            // $this->_addFields( $oFactory, $sSectionID );

            add_filter( 
                'validation_' . $oFactory->oProp->sClassName . '_' . $sSectionID,
                array( $this, 'replyToValidate' ), 
                10, 
                4 
            );
            
        }
    
    protected function _construct( $oFactory ) {
        
    }
        
    protected function _getArguments( $oFactory ) {
        return array();
    }

    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    protected function _addFields( $oFactory, $sSectionID ) {}
    
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