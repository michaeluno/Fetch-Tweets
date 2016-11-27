<?php
/**
 * Adds a setting tab in the Fetch Tweets admin pages. 
 * 
 * If you are modifying the template to create your own, modify this extended class.
 * The setting arrays follows the specifications of Admin Page Framework v2. 
 * 
 * @package		Fetch Tweets
 * @subpackage	Sidebar Template
 * @see			http://wordpress.org/plugins/admin-page-framework/
 */
class FetchTweets_Template_Settings_Twitter extends FetchTweets_Template_Settings {

	/**
	 * Overriding properties.
	 */
	protected $sParentPageSlug  = 'fetch_tweets_templates';	// in the url, the ... part of ?page=... 
	protected $sParentTabSlug   = 'twitter';	// in the url, the ... part of &tab=...
	protected $sTemplateName    = 'Twitter';	// the template name
	protected $sSectionID       = 'template_twitter';
	
	/**
	 * Modify these methods. 
	 * This defines form sections. Set the section ID and the description here.
	 * The array structure follows the rule of Admin Page Framework. ( https://github.com/michaeluno/admin-page-framework )
	 */
	public function addSettingSections( $aSections ) {
			
		$aSections[ $this->sSectionID ] = array(
			'section_id'	=> $this->sSectionID,
			'page_slug'		=> $this->sParentPageSlug,
			'tab_slug'		=> $this->sParentTabSlug,
			'title'			=> $this->sTemplateName,
			'description'	=> sprintf( __( 'Options for the %1$s template.', 'fetch-tweets' ), $this->sTemplateName ) . ' ' 
				. __( 'These will be the default values and be overridden by the arguments passed directly by the widgets, the shortcode, or the PHP function.', 'fetch-tweets' ),
		);
		return $aSections;
	
	}
	/*
	 * This defines form fields. Return the field arrays. 
	 * The array structure follows the rule of Admin Page Framework. ( https://github.com/michaeluno/admin-page-framework )
	 * */
	public function addSettingFields( $aFields ) {
		
		if ( ! class_exists( 'FetchTweets_Commons' ) ) {
            return $aFields;	// if the main class does not exist, do nothing.
        }
		
		$aFields[ $this->sSectionID ] = array();		
		$aFields[ $this->sSectionID ][ 'width' ] = array(
			'field_id'      => 'width',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Container Width', 'fetch-tweets' ),
			'description'   => __( 'The width of the output.', 'fetch-tweets' ) . ' ' . __( 'Default', 'fetch-tweets' ) . ': 100%',
			'type'          => 'size',
			'units'         => array(
				'%'     => '%',
				'px'    => 'px',
				'em'    => 'em',
			),
			'default'       => array(
				'size'	=> 100,
				'unit'	=> '%',
			),
			'delimiter'     => '<br />',
            'attributes'    => array(
                'size'  => array(
                    'min'   => 1,
                ),
            ),
		);
		$aFields[ $this->sSectionID ][ 'height' ] = array(
			'field_id'      => 'height',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Container Height', 'fetch-tweets' ),
			'description'   => __( 'The height of the output.', 'fetch-tweets' ) . ' ' . __( 'Default', 'fetch-tweets' ) . ': 400px',
			'type'          => 'size',
			'units'         => array(
				'%'     => '%',
				'px'    => 'px',
				'em'    => 'em',
			),
			'default'       => array(
				'size'	=> 100,
				'unit'	=> '%',
			),
			'delimiter' => '<br />',
            'attributes'    => array(
                'size'  => array(
                    'min'   => 1,
                ),
            ),            
		);		
		$aFields[ $this->sSectionID ][ 'margins' ] = array(
			'field_id'      => 'margins',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Margins', 'fetch-tweets' ),
			'description'   => __( 'The margins of the output element. Leave them empty not to set any margin.', 'fetch-tweets' ),
			'type'          => 'size',
			'units'         => array( '%' => '%', 'px' => 'px', 'em' => 'em', ),
			'delimiter'     => '<br />',
			'label'         =>	__( 'Top', 'fetch-tweets' ),
			array(
				'label'	=>	__( 'Right', 'fetch-tweets' ),
			),
			array(
				'label'	=>	__( 'Bottom', 'fetch-tweets' ),
			),
			array(
				'label'	=>	__( 'Left', 'fetch-tweets' ),
			),
		);		
		$aFields[ $this->sSectionID ][ 'paddings' ] = array(
			'field_id'      => 'paddings',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Paddings', 'fetch-tweets' ),
			'description'   => __( 'The paddings of the output element. Leave them empty not to set any padding.', 'fetch-tweets' ),
			'type'          => 'size',
			'units'         => array( '%' => '%', 'px' => 'px', 'em' => 'em', ),
			'delimiter'     => '<br />',
			'label'         => __( 'Top', 'fetch-tweets' ),
			array(
				'label'	=>	__( 'Right', 'fetch-tweets' ),
			),
			array(
				'label'	=>	__( 'Bottom', 'fetch-tweets' ),
			),
			array(
				'label'	=>	__( 'Left', 'fetch-tweets' ),
			),			
		);		
			
		$aFields[ $this->sSectionID ][ 'tweet_maxwidth' ] = array(
			'field_id'      => 'tweet_maxwidth',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Tweet Width', 'fetch-tweets' ),
			'description'   => __( 'The max-width of each tweet.', 'fetch-tweets' ) . ' ' . __( 'Default', 'fetch-tweets' ) . ': 100%',
			'type'          => 'number',
            'after_input'   => ' px',
			'default'       => '550',
            'attributes'    => array(
                'max'   => 550,
                'min'   => 1,
            ),
		);                  
		$aFields[ $this->sSectionID ][ 'cards' ] = array(
			'field_id'      => 'cards',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Cards', 'fetch-tweets' ),
			'type'          => 'checkbox',
            'label'         => __( 'Do not expand tweets to photo, video, or link previews.', 'fetch-tweets' ),
            'default'       => false,
		);
		$aFields[ $this->sSectionID ][ 'hide_conversation' ] = array(
			'field_id'      => 'hide_conversation',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Conversation', 'fetch-tweets' ),
			'type'          => 'checkbox',
            'label'         => __( 'Display only the cited Tweet even if it is in reply to another Tweet.', 'fetch-tweets' ),
            'default'       => true,
		);
		$aFields[ $this->sSectionID ][ 'theme' ] = array(
			'field_id'      => 'theme',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Theme', 'fetch-tweets' ),
			'type'          => 'select',
            'label'         => array(
                'light'  => __( 'Light', 'fetch-tweets' ),
                'dark'   => __( 'Dark', 'fetch-tweets' ),
            ),
            'default'       => 'light',
		);
		$aFields[ $this->sSectionID ][ 'link_color' ] = array(
			'field_id'      => 'link_color',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Link Color', 'fetch-tweets' ),
			'type'          => 'color',
            'default'       => '#55acee',
		);
		$aFields[ $this->sSectionID ][ 'align' ] = array(
			'field_id'      => 'align',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Align', 'fetch-tweets' ),
			'type'          => 'radio',
            'label'         => array(
                'left'        => __( 'Left', 'fetch-tweets' ),
                'center'      => __( 'Center', 'fetch-tweets' ),
                'right'       => __( 'Right', 'fetch-tweets' ),
            ),
            'default'       => 'center',
		);
		$aFields[ $this->sSectionID ][ 'language' ] = array(
			'field_id'      => 'language',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Language', 'fetch-tweets' ),
			'type'          => 'select',
            'label'         => FetchTweets_PluginUtility::getLanguageListForSearchAPI(),
            'default'       => 'en',
		);        
		$aFields[ $this->sSectionID ][ '_submit' ] = array(
			'field_id'      => 'submit',
			'section_id'    => $this->sSectionID,
			'type'          => 'submit',
            'save'          => false,
			'before_field'  => "<div class='right-button'>",
			'after_field'   => "</div>",
			'label'         => __( 'Save Changes', 'fetch-tweets' ),
			'attributes'	=>	array(
				'class'	=>	'button button-primary',
			),
		);
		return $aFields;		
	}
	
	public function validateSettings( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
		
		return $aInputs;
		
	}
	
}
new FetchTweets_Template_Settings_Twitter( dirname( __FILE__ ) );
