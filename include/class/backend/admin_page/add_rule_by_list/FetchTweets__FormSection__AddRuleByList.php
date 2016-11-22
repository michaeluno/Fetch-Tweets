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
 * @since       2.5.0
 */
class FetchTweets__FormSection__AddRuleByList extends FetchTweets__FormSection__Base {

    protected $_sSectionID = 'add_rule_by_list';
    
    protected $_oOption;
    
    protected function _construct( $oFactory ) {
        
        $this->_oOption = FetchTweets_Option::getInstance();
            
        add_filter( 
            "field_definition_{$oFactory->oProp->sClassName}_{$this->_sSectionID}_list_owner_accounts", 
            array( $this, 'replyToModifyField_list_owner_accounts' ) 
        );
    }
    
    protected function _getArguments( $oFactory ) {
        $_aArguments =  array(
            'section_id'    => $this->_sSectionID,
            'title'         => __( 'Specify the Screen Name', 'fetch-tweets' ),
            'description'   => __( 'In order to select list, the user name(screen name) of the account that owns the list must be specified.', 'fetch-tweets' ),
        );
        if ( $this->_sPageSlug ) {
            $_aArguments[ 'page_slug' ] = $this->_sPageSlug;
        }
        return $_aArguments;
    }

    protected function _getFields( $oFactory ) {
        return array(
            array(    
                'field_id'      => 'list_owner_accounts',
                'title'         => __( 'Owner Accounts', 'fetch-tweets' ),
                'description'   => __( 'Select the screen name that owns the list.', 'fetch-tweets' ),
                'type'          => 'select',
                'value'         => '',
            ),            
            array(    
                'field_id'      => 'list_owner_screen_name',
                'title'         => __( 'Owner Screen Name', 'fetch-tweets' ) . ' <span class="optional">(' . __( 'optional', 'fetch-tweets' ) . ')</span>',
                'description'   => __( 'The screen name(user name) that owns the list. When the target screen name is not listed above, specify here.', 'fetch-tweets' ) . '<br />'
                    . 'e.g. miunosoft',
                'type'          => 'text',
                'value'         => '',
                'attributes'    => array(
                    'size'    => 40,
                ),                
            ),
            array(  // single button
                'field_id'      => 'list_proceed',
                'type'          => 'submit',
                'before_field'  => "<div class='right-button'>",
                'after_field'   => "</div>",
                'label'         => __( 'Proceed', 'fetch-tweets' ),
                'attributes'    => array(
                    'class'    => 'button button-primary',
                ),                    
            ),
        );
        
    }
    
    /**
     * Field definition callbacks
     * 
     * @callback    filter      field_definition_{class name}_{section id}_{field id}
     * @since       unknown
     * @since       2.4.5       Changed the name from 'field_definition_FetchTweets_AdminPage_add_rule_by_list_list_owner_accounts'
     */
    public function replyToModifyField_list_owner_accounts( $aField ) {   
    
        $_aCredentials = $this->_oOption->getCredentials();
        if ( ! ( $_aCredentials['consumer_key'] && $_aCredentials['consumer_secret'] && $_aCredentials['access_token'] && $_aCredentials['access_secret'] ) ) {
            $aField['before_field'] = '<p class="error">* ' . __( 'The plugin is not connected to Twitter.', 'fetch-tweets' ) . '</p>';
            return $aField;
        }
        
        $_aCredentials[ 'screen_name' ] = isset( $_aCredentials['screen_name'] ) && $_aCredentials['screen_name'] 
            ? $_aCredentials[ 'screen_name' ] 
            : $this->___getScreenName( $_aCredentials );    // for backward compatibility
        
        $_aLabels = array(
                -1 => '--- ' . __( 'Select Account', 'fetch-tweets' ) . ' ---',
            )
            + apply_filters( 
                'fetch_tweets_filter_authenticated_accounts', 
                array(
                    // account id => screen name
                    0 => $_aCredentials['screen_name'],
                ) 
            );
        
        $aField['label'] = $_aLabels;
        return $aField;
        
    }
        private function ___getScreenName( $aCredentials ) {
            
            // Create TwitteroAuth object with API key/secret and token key/secret from default phase.
            $_oConnect = new FetchTweets_TwitterAPI_Verification( 
                $aCredentials[ 'consumer_key' ], 
                $aCredentials[ 'consumer_secret' ], 
                $aCredentials[ 'access_token' ], 
                $aCredentials[ 'access_secret' ]
            );
            
            // Request access tokens from twitter
            $_aResponse = $_oConnect->getStatus();
            return $this->getElement( $_aResponse, 'screen_name' );            
            
        }    
    
    /**
     * @callback        validation_{class name}_{section id}
     * @since           2.5.0
     */
    protected function _validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInformation ) {
        
        // Check if the input has been properly sent.        
        if ( 
            ! isset( 
                $aInputs[ 'list_owner_screen_name' ], 
                $aInputs[ 'list_owner_accounts' ] 
            ) 
        ) {
            $oFactory->setSettingNotice( __( 'Something went wrong. Your input could not be received. Try again and if this happens again, contact the developer.', 'fetch-tweets' ) );
            return $aOldInputs;
        }
        
        $_aCredentials = $this->___getCredentiaslByAccountID( $aInputs[ 'list_owner_accounts' ] );
        
        // Variables
        $_aErrors           = array();    // error array
        $_iAccountID        = -1 == $aInputs[ 'list_owner_accounts' ] 
            ? 0 
            : $aInputs[ 'list_owner_accounts' ];
        $_sOwnerScreenName  = '-1' == $aInputs[ 'list_owner_accounts' ]
            ? $aInputs[ 'list_owner_screen_name' ]    // the manually typed
            : $_aCredentials['screen_name'];
        
        // The list owner screen name must be provided.
        if ( empty( $_sOwnerScreenName ) ) {
            $_aErrors[ $this->_sSectionID ][ 'list_owner_screen_name' ] = __( 'The screen name of the list owner must be specified: ', 'fetch-tweets' ) . $_sOwnerScreenName;
            $oFactory->setFieldErrors( $_aErrors );        
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'fetch-tweets' ) );
            return $aOldInputs;                        
        }
        
        // Fetch the lists by the screen name.
        $_oFetch = new FetchTweets_Fetch(
            $_aCredentials[ 'consumer_key' ],
            $_aCredentials[ 'consumer_secret' ],
            $_aCredentials[ 'access_token' ],
            $_aCredentials[ 'access_secret' ]
        );
        $_aLists = $_oFetch->getListNamesFromScreenName( $_sOwnerScreenName, $_iAccountID );
        if ( empty( $_aLists ) ) {
            $oFactory->setSettingNotice( __( 'No list found.', 'fetch-tweets' ) );
            return $aOldInputs;            
        }

        // Set the transient of the fetched IDs. This will be used right next page load.
        $_sListCacheID = uniqid();
        FetchTweets_WPUtility::setTransient( $_sListCacheID, $_aLists, 60 );
        $oFactory->oUtil->goToURL(
            add_query_arg(     // go to the Manage Accounts page. 
                array( 
                    'post_type'   => FetchTweets_Commons::PostTypeSlug, 
                    'tweet_type'  => 'list',
                    'list_cache'  => $_sListCacheID,
                    'screen_name' => $_sOwnerScreenName,
                    'account_id'  => $_iAccountID,
                ), 
                admin_url( 'post-new.php' ) 
            )             
        );
        
    }
        /**
         * Retrieves the Twitter account credentials by the given account ID.
         * @since            2
         */
        private function ___getCredentiaslByAccountID( $iAccountID ) {
            return $this->_oOption->getCredentialsByID( 
                -1 == $iAccountID 
                    ? 0 
                    : $iAccountID 
            );
        }    

}
