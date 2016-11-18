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
class FetchTweets_Template_Settings_Sidebar extends FetchTweets_Template_Settings {

	/**
	 * Overriding properties.
	 */
	protected $sParentPageSlug  = 'fetch_tweets_templates';	// in the url, the ... part of ?page=... 
	protected $sParentTabSlug   = 'sidebar';	// in the url, the ... part of &tab=...
	protected $sTemplateName    = 'Sidebar';	// the template name
	protected $sSectionID       = 'fetch_tweets_template_sidebar';
	
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
		$aFields[ $this->sSectionID ][ 'avatar_size' ] = array(
			'field_id'      => 'avatar_size',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Profile Image Size', 'fetch-tweets' ),
			'description'   => __( 'The avatar size in pixel. Set 0 for no avatar.', 'fetch-tweets' ) 
                . ' ' . __( 'Default', 'fetch-tweets' ) . ': 24',
			'type'          => 'number',
			'default'       => 24, 
			'attributes'	=>	array(
				'size'	=>	 10,
			),
		);	
		$aFields[ $this->sSectionID ][ 'avatar_position' ] = array(
			'field_id'      => 'avatar_position',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Profile Image Position', 'fetch-tweets' ),
			'type'          => 'radio',
			'label'         => array(
				'left'  => __( 'Left', 'fetch-tweets' ),
				'right' => __( 'Right', 'fetch-tweets' ),
			),
			'default'       => 'left', 
		);				
		$aFields[ $this->sSectionID ][ 'width' ] = array(
			'field_id'      => 'width',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Width', 'fetch-tweets' ),
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
		);
		$aFields[ $this->sSectionID ][ 'height' ] = array(
			'field_id'      => 'height',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Height', 'fetch-tweets' ),
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
						
		$aFields[ $this->sSectionID ][ 'background_color' ] = array(
			'field_id'      => 'background_color',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Background Color', 'fetch-tweets' ),
			'type'          => 'color',
			'default'       => 'transparent',
		);		
		$aFields[ $this->sSectionID ][ 'intent_buttons' ] = array(
			'field_id'      => 'intent_buttons',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Intent Buttons', 'fetch-tweets' ),
			'description'   => __( 'These are for Favourite, Reply, and Retweet buttons.', 'fetch-tweets' ),
			'type'          => 'radio',
			'label'         => array(  
				1 => __( 'Both icons and text', 'fetch-tweets' ),
				2 => __( 'Only icons', 'fetch-tweets' ),
				3 => __( 'Only text', 'fetch-tweets' ),
			),
			'default' => 2,
		);
		$aFields[ $this->sSectionID ][ 'intent_script' ] = array(
			'field_id'      => 'intent_script',
			'section_id'    => $this->sSectionID,
			'title'         => __( 'Intent Button Script', 'fetch-tweets' ),
			'type'          => 'checkbox',
			'label'         => __( 'Insert the intent button script that enables a pop-up window for Favorite, Reply, and Retweet.', 'fetch-tweets' ),
			'default'       => 1,
		);
		$aFields[ $this->sSectionID ][ 'visibilities' ] = array(
			'field_id'          => 'visibilities',
			'section_id'        => $this->sSectionID,
			'title'             => __( 'Visibility', 'fetch-tweets' ),
			'type'              => 'checkbox',
			'label'             => array(
				'avatar'			=> __( 'Profile Image', 'fetch-tweets' ),
				'user_name'			=> __( 'User Name', 'fetch-tweets' ),
				'follow_button'     => __( 'Follow Button', 'fetch-tweets' ),   // 2.3.8+
				// 'user_description' => __( 'User Description', 'fetch-tweets' ),
				'time'				=> __( 'Time', 'fetch-tweets' ),
				'intent_buttons'	=> __( 'Intent Buttons', 'fetch-tweets' ),
			),
			'default' => array(
				'avatar'			=> true,
				'user_name'			=> true,
				'follow_button'     => false,
				// 'user_description' => true,
				'time'				=> true,
				'intent_buttons'	=> true,
			),
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
new FetchTweets_Template_Settings_Sidebar( dirname( __FILE__ ) );
