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
 * Defines an admin page.
 * 
 * @since       2.4.5
 */
class FetchTweets_AdminPage_AddRuleByList extends FetchTweets_AdminPage_Page_Base {

    /**
     * Called when the page loads.
     * 
     */
    public function replyToLoadPage( $oFactory ) {
        
        $this->oOption  = $GLOBALS['oFetchTweets_Option'];
        $_sSectionID    = 'add_rule_by_list';
        $oFactory->addSettingSections(
            $this->sPageSlug,    // target page slug
            array(
                'section_id'    => $_sSectionID,
                'title'         => __( 'Specify the Screen Name', 'fetch-tweets' ),
                'description'   => __( 'In order to select list, the user name(screen name) of the account that owns the list must be specified.', 'fetch-tweets' ),
            )        
        );
        
        $oFactory->addSettingFields(
            $_sSectionID,    // target section id
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
            )        
        );        
        
        $_sSectionID    = 'add_rule_by_list';
        $_sFieldID      = 'list_owner_accounts';
        add_filter( "field_definition_{$oFactory->oProp->sClassName}_{$_sSectionID}_{$_sFieldID}", array( $this, 'replyToModifyField_ListOwnerAccounts' ) );
        add_filter( "validation_{$this->sPageSlug}", array( $this, 'validatePageFormData' ), 10, 4 );
        
    }
    

    /**
     * Field definition callbacks
     * 
     * @remark      field_definition_{class name}_{section id}_{field id}
     * @since       unknown
     * @since       2.4.5       Changed the name from 'field_definition_FetchTweets_AdminPage_add_rule_by_list_list_owner_accounts'
     */
    public function replyToModifyField_ListOwnerAccounts( $aField ) {   
    
        $_aCredentials = $this->oOption->getCredentials();
        if ( ! ( $_aCredentials['consumer_key'] && $_aCredentials['consumer_secret'] && $_aCredentials['access_token'] && $_aCredentials['access_secret'] ) ) {
            $aField['before_field'] = '<p class="error">* ' . __( 'The plugin is not connected to Twitter.', 'fetch-tweets' ) . '</p>';
            return $aField;
        }
        
        $_aCredentials['screen_name'] = isset( $_aCredentials['screen_name'] ) && $_aCredentials['screen_name'] 
            ? $_aCredentials['screen_name'] 
            : $this->_getScreenName( $_aCredentials );    // for backward compatibility
        
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
        private function _getScreenName( $aCredentials ) {
            
            /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
            $_oConnect = new FetchTweets_TwitterAPI_Verification( 
                $aCredentials['consumer_key'], 
                $aCredentials['consumer_secret'], 
                $aCredentials['access_token'], 
                $aCredentials['access_secret']
            );
            
            /* Request access tokens from twitter */
            $_aResponse = $_oConnect->getStatus();
            return isset( $_aResponse['screen_name'] )
                ? $_aResponse['screen_name']
                : null;
            
        }
    
    /**
     * Validates submitted form data of the page. 
     * @remark validation_{page slug}
     * 
     * @since       unknown
     * @since       2.4.5       Changed the name from 'validation_fetch_tweets_add_rule_by_list'.
     */
    public function validatePageFormData( $aInput, $aOldInput, $oFactory, $aSubmitInformation ) {   
                
        // Check if the input has been properly sent.        
        if ( ! isset( $aInput['add_rule_by_list']['list_owner_screen_name'], $aInput['add_rule_by_list']['list_owner_accounts'] ) ) {
            $oFactory->setSettingNotice( __( 'Something went wrong. Your input could not be received. Try again and if this happens again, contact the developer.', 'fetch-tweets' ) );
            return $aOldInput;
        }
        
        $_aCredentials = $this->_getCredentiaslByAccountID( $aInput['add_rule_by_list']['list_owner_accounts'] );
        
        // Variables
        $_aErrors           = array();    // error array
        $_iAccountID        = $aInput['add_rule_by_list']['list_owner_accounts'] == -1 ? 0 : $aInput['add_rule_by_list']['list_owner_accounts'];                        
        $_sOwnerScreenName  = $aInput['add_rule_by_list']['list_owner_accounts'] == '-1'
            ? $aInput['add_rule_by_list']['list_owner_screen_name']    // the manually typed
            : $_aCredentials['screen_name'];
        
        // The list owner screen name must be provided.
        if ( empty( $_sOwnerScreenName ) ) {
            $_aErrors['add_rule_by_list']['list_owner_screen_name'] = __( 'The screen name of the list owner must be specified: ' ) . $_sOwnerScreenName;
            $oFactory->setFieldErrors( $_aErrors );        
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'fetch-tweets' ) );
            return $aOldInput;                        
        }
        
        // Fetch the lists by the screen name.
        $_oFetch = new FetchTweets_Fetch(
            $_aCredentials['consumer_key'],
            $_aCredentials['consumer_secret'],
            $_aCredentials['access_token'],
            $_aCredentials['access_secret']
        );
        $_aLists = $_oFetch->getListNamesFromScreenName( $_sOwnerScreenName, $_iAccountID );
        if ( empty( $_aLists ) ) {
            $oFactory->setSettingNotice( __( 'No list found.', 'fetch-tweets' ) );
            return $aOldInput;            
        }

        // Set the transient of the fetched IDs. This will be used right next page load.
        $_sListCacheID = uniqid();
        FetchTweets_WPUtilities::setTransient( $_sListCacheID, $_aLists, 60 );        
        exit( 
            wp_redirect( 
                add_query_arg(     // go to the Manage Accounts page. 
                    array( 
                        'post_type' => FetchTweets_Commons::PostTypeSlug, 
                        'tweet_type' => 'list',
                        'list_cache' => $_sListCacheID,
                        'screen_name' => $_sOwnerScreenName,
                        'account_id' => $_iAccountID,
                    ), 
                    admin_url( 'post-new.php' ) 
                )             
                // "post-new.php?post_type=fetch_tweets&tweet_type=list&list_cache={$_sListCacheID}&screen_name={$_sOwnerScreenName}" 
            ) 
        );
        
    }
        /**
         * Retrieves the Twitter account credentials by the given account ID.
         * @since            2
         */
        private function _getCredentiaslByAccountID( $iAccountID ) {
            return $this->oOption->getCredentialsByID( 
                -1 == $iAccountID 
                    ? 0 
                    : $iAccountID 
            );
        }    
    
 
}