<?php
/**
 * Admin Page Framework - Field Type Pack
 * 
 * http://en.michaeluno.jp/fetch-tweets/
 * Copyright (c) 2014-2016 Michael Uno
 * 
 */

if ( ! class_exists( 'FetchTweets_GeometryCustomFieldType' ) ) :

/**
 * Defines the geometry field type.
 * 
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *  <li>`attributes` - (array) The array that defines the HTML attributes of the field elements.
 *      <ul>
 *          <li>`latitude` - (array) The attributes applied the latitude input tag.</li>
 *          <li>`longitude` - (array) The attributes applied the longitude input tag.</li>
 *          <li>`elevation` - (array) The attributes applied the elevation element.</li>
 *          <li>`location_name` - (array) The attributes applied the location_name  element.</li>
 *          <li>`button` - (array) The attributes applied the button element.</li>
 *      </ul>
 *  </li>
 *  <li>`google_maps_api_key` - (string) An Google Maps API key which can be obtained from [here](https://developers.google.com/maps/documentation/javascript/get-api-key?pli=1).</li>
 * </ul>
 * <h3>Example</h3>
 * <code>
 *  array(
 *      'field_id'      => 'geometrical_coordinates',
 *      'section_id'    => 'geometry',
 *      'title'         => __( 'Geometrical Coordinates', 'fetch-tweets' ),
 *      'type'          => 'geometry',
 *      'default'       => array(
 *          'latitude'  => 20,
 *          'longitude' => 20,
 *      ),
 *  )
 * </code>
 * 
 * @since       1.0.0
 * @package     FetchTweets_AdminPageFrameworkFieldTypePack
 * @subpackage  CustomFieldType
 * @version     1.0.2
 */
class FetchTweets_GeometryCustomFieldType extends FetchTweets_AdminPageFramework_FieldType {
        
    /**
     * Defines the field type slugs used for this field type.
     */        
    public $aFieldTypeSlugs = array( 'geometry' );    
        
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes'        => array(
            'value'         => array(
                'latitude'      => 20,
                'longitude'     => 20,
                'elevation'     => null,
                'location_name' => null,
            ),
            'latitude'      => array(),
            'longitude'     => array(),
            'elevation'     => array(),
            'location_name' => array(),
            'button'        => array(),
        ),    
        
        /**
         * @since   1.0.2
         * @see     https://googlegeodevelopers.blogspot.jp/2016/06/building-for-scale-updates-to-google.html
         */
        'google_maps_api_key'    => '',  
    );

    /**
     * Loads the field type necessary components.
     */ 
    protected function setUp() {}
    
    /**
     * Returns an array holding the urls of enqueuing scripts.
     */
    protected function getEnqueuingScripts() { 
        return array();
    }    

    /**
     * Returns an array holding the urls of enqueuing styles.
     */
    protected function getEnqueuingStyles() { 
        return array(
            dirname( __FILE__ ) . '/css/jquery-gmaps-latlon-picker.css',    // a file path can be passed, ( as well as a url )
        ); 
    }    
    
    /**
     * Returns the field type specific JavaScript script.
     */ 
    protected function getScripts() { return ''; } 

    /**
     * Returns IE specific CSS rules.
     */
    protected function getIEStyles() { return ''; }

    /**
     * Returns the field type specific CSS rules.
     */ 
    protected function getStyles() {
        return "/* Geometry Custom Field Type */
            .fetch-tweets-field .gllpMap {width: 100%}
            .fetch-tweets-section .form-table td .gllpLatlonPicker label {
                display: inline-block;
            }
        ";
    }
    
    /**
     * Returns the output of this field type.
     * @return      string
     */
    protected function getField( $aField ) { 
        $this->_loadGoogleMapsAPIScripts( $aField[ 'google_maps_api_key' ] );
        return $aField[ 'before_label' ]
            . "<div class='fetch-tweets-input-label-container'>"
                    . $aField[ 'before_input' ]
                    . ( $aField[ 'label' ] && ! $aField[ 'repeatable' ]
                        ? "<span class='fetch-tweets-input-label-string' style='min-width:" . $this->getLengthSanitized( $aField['label_min_width'] ) . ";'>" 
                                . $aField['label'] 
                            . "</span>"
                        : "" 
                    )
                    . $this->_getInputs( $aField )
                    . $aField[ 'after_input' ]
            . "</div>"
            . $aField[ 'after_label' ];
        
    }
        /**
         * @return      string
         */
        protected function _getInputs( &$aField ) {
            
            // Set up attributes.
            $aBaseAttributes = $aField[ 'attributes' ];
            unset( 
                $aBaseAttributes[ 'latitude' ], 
                $aBaseAttributes[ 'longitude' ], 
                $aBaseAttributes[ 'elevation' ], 
                $aBaseAttributes[ 'location_name' ], 
                $aBaseAttributes[ 'button' ]
            );

            $aButtonAttributes = array(
                'type'    => 'button',
                'id'      => "{$aField['input_id']}_button",
            ) + $aField['attributes']['button'] + $aBaseAttributes;
            $aButtonAttributes['class']    .= ' gllpUpdateButton button button-small';
            
            $aLattitudeAttributes = array(
                'type'    => 'text',
                'id'      => "{$aField['input_id']}_latitude",
                'value'   => isset( $aField['value']['latitude'] ) ? $aField['value']['latitude'] : 20,
                'name'    => "{$aField['_input_name']}[latitude]",                        
            ) + $aField['attributes']['latitude'] + $aBaseAttributes;
            $aLattitudeAttributes['class'] .= ' gllpLatitude';
            
            $aLongitudeAttributes = array(
                'type'    => 'text',
                'id'      => "{$aField['input_id']}_longitude",
                'value'   => isset( $aField['value']['longitude'] ) ? $aField['value']['longitude'] : 20,
                'name'    => "{$aField['_input_name']}[longitude]",
            ) + $aField['attributes']['longitude'] + $aBaseAttributes;            
            $aLongitudeAttributes['class'] .= ' gllpLongitude';

            $aElevationAttributes = array(
                'type'    => 'text',
                'id'      => "{$aField['input_id']}_elevation",
                'value'   => isset( $aField['value']['elevation'] ) ? $aField['value']['elevation'] : null,
                'name'    => "{$aField['_input_name']}[elevation]",
            ) + $aField['attributes']['elevation'] + $aBaseAttributes;            
            $aElevationAttributes['class'] .= ' gllpElevation';        
            
            $aLocationNameAttributes = array(
                'type'    => 'text',
                'id'      => "{$aField['input_id']}_name",
                'value'   => isset( $aField['value']['localtion_name'] ) ? $aField['value']['localtion_name'] : null,
                'name'    => "{$aField['_input_name']}[localtion_name]",
            ) + $aField['attributes']['location_name'] + $aBaseAttributes;            
            $aLocationNameAttributes['class'] .= ' gllpLocationName';
            
            /* Return the output */
            return
                "<div class='gllpLatlonPicker'>"
                    . "<div class='gllpMap map'>" . __( 'Google Maps', 'fetch-tweets' ) . "</div>"
                    . "<label for='{$aField['input_id']}_button' class='update-button'>"
                        . "<a " . $this->getAttributes( $aButtonAttributes ) . ">" . __( 'Update Map', 'fetch-tweets' ) . "</a>"
                    . "</label>"                    
                    . "<label for='{$aField['input_id']}_latitude'>"
                        . "<span class='fetch-tweets-input-label-string' style='min-width:" . $this->getLengthSanitized( $aField['label_min_width'] ) . ";'>" . __( 'Latitude', 'fetch-tweets' ) . "</span>"
                        . "<input " . $this->getAttributes( $aLattitudeAttributes ) . " />"                
                    . "</label><br />"
                    . "<label for='{$aField['input_id']}_longitude'>"
                        . "<span class='fetch-tweets-input-label-string' style='min-width:" . $this->getLengthSanitized( $aField['label_min_width'] ) . ";'>" . __( 'Longitude', 'fetch-tweets' ) . "</span>"
                        . "<input " . $this->getAttributes( $aLongitudeAttributes ) . " />"    
                    . "</label><br />"
                    . "<label for='{$aField['input_id']}_elevation'>"
                        . "<span class='fetch-tweets-input-label-string' style='min-width:" . $this->getLengthSanitized( $aField['label_min_width'] ) . ";'>" . __( 'Elevation', 'fetch-tweets' ) . "</span>"                    
                        . "<input " . $this->getAttributes( $aElevationAttributes ) . " />"
                        . ' ' . __( "metres", "fetch-tweets" )
                    . "</label><br />"                                
                    . "<label for='{$aField['input_id']}_name'>"
                        . "<span class='fetch-tweets-input-label-string' style='min-width:" . $this->getLengthSanitized( $aField['label_min_width'] ) . ";'>" . __( 'Location Name', 'fetch-tweets' ) . "</span>"
                        . "<input " . $this->getAttributes( $aLocationNameAttributes ) . " />"
                    . "</label><br />"
                    . "<label for='{$aField['input_id']}_zoom'>"
                        . "<span class='fetch-tweets-input-label-string' style='min-width:" . $this->getLengthSanitized( $aField['label_min_width'] ) . ";'>" . __( 'zoom', 'fetch-tweets' ) . "</span>"    
                        . "<input type='number' class='gllpZoom' id='{$aField['input_id']}_zoom' min='1' value='3'/>"
                    . "</label><br />"
                . "</div>";    
            
        }
        
        /**
         * Loads Google Map API scripts.
         * @remark      The Google Map API has started requiring an API key and in order to let the developer set their own API keys,
         * The scripts cannot be loaded with the `getEnqueuingScripts()` method.
         * @since       1.0.1
         * @return      void
         */
        private function _loadGoogleMapsAPIScripts( $sAPIKey ) {
            if ( $this->hasBeenCalled( __METHOD__ ) ) {
                return;
            }
            wp_enqueue_script(
                'google-maps-api',   // handle
                'http://maps.googleapis.com/maps/api/js?sensor=false&key=' . $sAPIKey,  // source
                array(),    // dependencies
                null,       // version
                true        // in-footer
            );
            wp_enqueue_script(
                'jquery-gmaps-latlon-picker',   // handle
                $this->getResolvedSRC( dirname( __FILE__ ) . '/js/jquery-gmaps-latlon-picker.js' ), // source
                array( 'google-maps-api', 'jquery' ),    // dependencies
                null,       // version
                true        // in-footer
            );            
        
        }
    
}
endif;