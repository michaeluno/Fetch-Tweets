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
 * Adds a Contact page to the plugin.
 * 
 * @since   2.4.0
 */
class FetchTweets_AdminPage_Contact extends FetchTweets_AdminPageFramework {

    /**
     * Sets up pages.
     * 
     * This method automatically gets triggered with the wp_loaded hook. 
     */
    public function setUp() {

        $_sCapability = FetchTweets_Option::get( array( 'capabilities', 'setting_page_capability' ) );
        if ( $_sCapability ) {
            $this->setCapability( $_sCapability );
        }
      
        /* ( required ) Set the root page */
        $_sPostTypeSlug = FetchTweets_Commons::PostTypeSlug;
        $this->setRootMenuPageBySlug( "edit.php?post_type={$_sPostTypeSlug}"  );
            
        /* ( required ) Add sub-menu items (pages or links) */
        $this->addSubMenuItems(     
            array(
                'title'        => __( 'Contact', 'fetch-tweets' ),
                'page_slug'    => 'fetch_tweets_contact',
                'screen_icon'  => 'feedback',
                'order'        => 200,
            )
        );

        /* ( optional ) Disable the automatic settings link in the plugin listing table. */    
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string.
        
    }
    
    /**
     * The pre-defined callback method triggered when one of the added pages loads
     */
    public function load() { 

        // ( optional ) Determine the page style.
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
        $this->setPageTitleVisibility( false, 'fetch_tweets_contact' ); // disable the page title of a specific page.

    }
    
    /**
     * Do page specific settings.
     * 
     * @callback        action      load_{page slug}
     */
    public function load_fetch_tweets_contact() {    
    
        $this->addInPageTabs( 
            'fetch_tweets_contact',  // the target page slug 
            array(
                'tab_slug'  => 'report',
                'title'     => __( 'Report Issues', 'fetch-tweets' ),
            )
        );     
                            
    }
    
    public function load_fetch_tweets_contact_report() {    // load_ + {page_slug} + {tab slug}
  
        $_oCurrentUser = wp_get_current_user();
  
        $this->addSettingSections(
            'fetch_tweets_contact',  // the target page slug            
            array(
                'section_id'        => 'report',
                'tab_slug'          => 'report',
                'title'             => __( 'Report Issues', 'fetch-tweets' ),
                'description'       => __( 'If you encounter a problem, report it from here.', 'fetch-tweets' ),
            )
        );
        $this->addSettingFields(
            'report',
            array( 
                'field_id'          => 'name',
                'title'             => __( 'Your Name', 'fetch-tweets' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_firstname || $_oCurrentUser->user_firstname 
                    ? $_oCurrentUser->user_lastname . ' ' .  $_oCurrentUser->user_lastname 
                    : '',
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   => __( 'Type your name.', 'admin-page-framewrok-demo' ),
                ),
            ),    
            array( 
                'field_id'          => 'from',
                'title'             => __( 'Your Email Address', 'fetch-tweets' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_email,
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   =>  __( 'Type your email that the developer replies backt to.', 'fetch-tweets' )
                ),
            ),                
            array( 
                'field_id'          => 'expected_result',
                'title'             => __( 'Expected Behavior', 'fetch-tweets' ),
                'type'              => 'textarea',
                'description'       => __( 'Tell how the framework should work.', 'fetch-tweets' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),
            ),  
            array( 
                'field_id'          => 'actual_result',
                'title'             => __( 'Actual Behavior', 'fetch-tweets' ),
                'type'              => 'textarea',
                'description'      => __( 'Describe the behavior of the framework.', 'fetch-tweets' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),                
            ),     
            array(
                'field_id'          => 'attachments',
                'title'             => __( 'Screenshots', 'fetch-tweets' ),
                'type'              => 'image',
                'repeatable'        => true,
                'attributes'        => array(
                    'size'  => 40,
                    'preview' => array(
                        'style' => 'max-width: 200px;'
                    ),
                ),                                
            ),               
            array(
                'field_id'      => 'system_information',
                'type'          => 'system',     
                'title'         => __( 'System Information', 'fetch-tweets' ),
                'data'          => array(
                    __( 'Custom Data', 'fetch-tweets' )    => __( 'This is custom data inserted with the data argument.', 'fetch-tweets' ),
                    __( 'Current Time', 'admin-page-framework' )        => '', // Removes the Current Time Section.
                ),
                'attributes'    => array(
                    'rows'          =>  10,
                ),
                'hidden'        => true,
            ),    
            array(
                'field_id'      => 'saved_options',
                'type'          => 'system',  
                'title'         => __( 'Saved Options', 'fetch-tweets' ),
                'data'          => array(
                    // Removes the default data by passing an empty value below.
                    'Admin Page Framework'  => '', 
                    'WordPress'             => '', 
                    'PHP'                   => '', 
                    'MySQL'                 => '', 
                    'Server'                => '',
                ) 
                + $this->_getOptionsForReport(), 
                'attributes'    => array(
                    'rows'          =>  10,
                ),
                'hidden'        => true,
            ),       
            array( 
                'field_id'          => 'allow_sending_system_information',
                'title'             => __( 'Confirmation', 'fetch-tweets' ),
                'type'              => 'checkbox',
                'label'             => __( 'I agree that the site information such as PHP version and WordPress version and the plugin options will be sent to the developer along with the message to help trouble-shoot the problem.', 'fetch-tweets' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),                
            ),          
            array( 
                'field_id'          => 'send',
                'type'              => 'submit',
                'label_min_width'   => 0,
                'value'             => isset( $_GET['confirmation'] )
                    ? __( 'Send', 'fetch-tweets' )
                    : __( 'Preview', 'fetch-tweets' ),
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                    'class' => isset( $_GET['confirmation'] )
                        ? null
                        : 'button-secondary',
                ),    
                'email'             => array(
                    // Each argument can accept a string or an array representing the dimensional array key.
                    // For example, if there is a field for the email title, and its section id is 'my_section'  and  the field id is 'my_field', pass an array, array( 'my_section', 'my_field' )
                    'to'            => 'wpplugins@michaeluno.jp',
                    'subject'       => 'Reporting Issue of Fetch Tweets',
                    'message'       => array( 'report' ), // the section name enclosed in an array. If it is a field, set it to the second element like array( 'seciton id', 'field id' ).
                    'headers'       => '',
                    'attachments'   => '', // the file path
                    'name'          => '', // The email sender name. If the 'name' argument is empty, the field named 'name' in this section will be applied
                    'from'          => '', // The sender email address. If the 'from' argument is empty, the field named 'from' in this section will be applied.
                    // 'is_html'       => true,
                ),
            ),     
            array()
        );
        
    }
        /**
         * Returns the options array by removing private information.
         * 
         * It is the stored options of the main demo class
         * 
         * @since       2.4.0
         */
        private function _getOptionsForReport()  {
            
            $_aOptions = get_option( FetchTweets_Commons::$sAdminKey, array() );
            $_aOptions['twitter_connect']['access_token'] = isset( $_aOptions['twitter_connect']['access_token'] ) && $_aOptions['twitter_connect']['access_token']
                ? 'set'
                : 'not set';
            $_aOptions['twitter_connect']['access_secret'] = isset( $_aOptions['twitter_connect']['access_secret'] ) && $_aOptions['twitter_connect']['access_secret']
                ? 'set'
                : 'not set';                
            $_aOptions['authentication_keys']['access_token'] = isset( $_aOptions['authentication_keys']['access_token'] ) && $_aOptions['authentication_keys']['access_token']
                ? 'set'
                : 'not set';
            $_aOptions['authentication_keys']['access_secret'] = isset( $_aOptions['authentication_keys']['access_secret'] ) && $_aOptions['authentication_keys']['access_secret']
                ? 'set'
                : 'not set'; 
            return $_aOptions;
            
        }
    
    /**
     * Validates the submitted data.
     * @sicne       2.4.0
     */
    public function validation_fetch_tweets_contact_report( $aInput, $aOldInput, $oFactory ) {
      
        // Local variables
        $_bIsValid = true;
        $_aErrors  = array();
      
        if ( ! $aInput['report']['allow_sending_system_information'] ) {
            $_bIsValid = false;
            $_aErrors['report']['allow_sending_system_information'] = __( 'We need necessary information to help you.', 'fetch-tweets' );
        }
        
        if ( ! $_bIsValid ) {
        
            $oFactory->setFieldErrors( $_aErrors );     
            $oFactory->setSettingNotice( __( 'Please help us to help you.', 'fetch-tweets' ) );        
            return $aOldInput;
            
        }     
     
        // Otherwise, process the data.
        return $aInput;        
        
    }
    
}
