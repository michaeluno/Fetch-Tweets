<?php
class FetchTweets_MetaBox_Search_ extends FetchTweets_AdminPageFramework_MetaBox {
    
    /**
     * Registers field types.
     */
    public function start() {
        
        new FetchTweets_DateCustomFieldType( get_class( $this ) );    
        new FetchTweets_GeometryCustomFieldType( get_class( $this ) );    
        
    }
                
    /**
     * Adds form fields for the options to fetch tweets by keyword search to the meta box.
     * 
     * @since            1.0.0
     */ 
    public function setUp() {
        
        $this->addSettingFields(        
            array(
                'field_id'        => 'tweet_type',
                'type'            => 'hidden',
                'value'           => 'search',
                'hidden'          => true,
            ),                          
            array(
                'field_id'        => 'search_keyword',
                'title'           => __( 'Search Keyword', 'fetch-tweets' ),
                'description'     => sprintf( __( 'The keyword to search. For a complex combination of terms and operators, refer to the <strong>Search Operators</strong> section of <a href="%1$s" target="_blank">Using the Twitter Search API</a>.', 'fetch-tweets' ), 'https://dev.twitter.com/docs/using-search' ) 
                    . ' e.g. <code>love OR hate</code>, <code>#wordpress</code>',
                'type'            => 'text',
                'attributes'      => array(
                    'size'  => 40,
                ),                  
            ),
            array(
                'field_id'        => 'item_count',
                'title'           => __( 'Item Count', 'fetch-tweets' ),
                'description'     => __( 'Set how many items should be fetched.', 'fetch-tweets' ) . ' ' 
                    . __( 'Max', 'fetch-tweets' ) . ': 100 '
                    . __( 'Default', 'fetch-tweets' ) . ': 20',
                'type'            => 'number',
                'default'         => 20,
                'attributes'      => array(
                    'max'    =>    100,
                ),
            ),                
            array(
                'field_id'        => 'language',
                'title'           => __( 'Language ', 'fetch-tweets' ),
                'type'            => 'select',
                'label'           => FetchTweets_PluginUtility::getLanguageListForSearchAPI(),
                'default'         => 'none',    
            ),                
            array(
                'field_id'        => 'result_type',
                'title'           => __( 'Result Type', 'fetch-tweets' ),
                'type'            => 'radio',
                'label'           => array( 
                    'mixed'     => 'mixed' . ' - ' . __( 'includes both popular and real time results in the response.', 'fetch-tweets' ),
                    'recent'    => 'recent' . ' - ' . __( 'returns only the most recent results in the response.', 'fetch-tweets' ),
                    'popular'   => 'popular' . ' - ' . __( 'return only the most popular results in the response.', 'fetch-tweets' ),
                ),
                'default' => 'mixed',
            ),
            array(    // since 1.3.3
                'field_id'        => 'until',
                'title'           => __( 'Date Until', 'fetch-tweets' ) . " <span class='description'>(" . __( 'optional', 'fetch-tweets' ) . ")</span>",
                'description'     => __( 'Returns tweets generated before the given date. Set blank not to specify any date.', 'fetch-tweets' )
                    . ' ' . __( 'Format', 'fetch-tweets' ) . ': ' . '<code>yy-mm-dd</code>',
                'type'            => 'date',
                'date_format'     => 'yy-mm-dd',    // yy/mm/dd is the default format.
            ),
            array(    // since 1.3.3
                'field_id'        => 'geocentric_coordinate',
                'title'           => __( 'Geometric Coordinate', 'fetch-tweets' ) . " <span class='description'>(" . __( 'optional', 'fetch-tweets' ) . ")</span>",
                'description'     => __( 'Restricts tweets to users located within a given radius of the given latitude/longitude. Leave them empty not to set any.', 'fetch-tweets' ),
                'type'            => 'geometry',

                // @todo: convert the latitude key to 0 and the longitude key to 1 for APF v2
                // 'label'            => array(
                    // 'latitude' => __( 'Latitude', 'fetch-tweets' ),
                    // 'longitude' => __( 'Longitude', 'fetch-tweets' ),
                // ),
            ),
            array(    // since 1.3.3
                'field_id'        => 'geocentric_radius',
                'title'           => __( 'Geometric Radius', 'fetch-tweets' ) . " <span class='description'>(" . __( 'optional', 'fetch-tweets' ) . ")</span>",
                'type'            => 'size',
                'default'         => array( 'size' => '', 'unit' => 'mi' ),
                'units'           => array(
                    'mi' => __( 'miles', 'fetch-tweets' ),
                    'km' => __( 'kilometers', 'fetch-tweets' ),
                ),
                'description'    => __( 'Leave this empty not to set any. In order to perform the geometric search, this option and the above coordinate must be specified.', 'fetch-tweets' ),
            ),                
            array()
        );
        
    }    

    /**
     * 
     * @since   unknown
     * @since   2.3.8       Allowed 0 for the `item_count` argument.
     */
    public function validation_FetchTweets_MetaBox_Search( $aInput ) {    // validation_ + extended class name
            
        $aInput['item_count'] = $this->oUtil->fixNumber( 
            $aInput['item_count'],     // number to sanitize
            20,     // default
            0,      // minimum
            100
        );
                
        return $aInput;
        
    }    
    
}
