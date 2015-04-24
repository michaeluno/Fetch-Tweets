<?php
/**
 * Fetches and displays tweets from twitter.com with the the Twitter REST API v1.1.
 *  
 * @copyright   Copyright (c) 2013-2015, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * 
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Handles the list table of Fetch Tweets templates. 
 * 
 * @package     Fetch Tweets
 * @since       1.0.0
 * @filter      fetch_tweets_filter_template_listing_table_action_links
 */
class FetchTweets_ListTable_ extends WP_List_Table {
    
    /**
     * Stores the data of the listing items.
     * 
     * @remark      Declare it to be compatible with WordPress v4.2.
     */
    public $aData = array();
       
    /**
     * Stores the arguments.
     * @remark      Declare it to be compatible with WordPress v4.2.
     */
    public $aArgs = array();
       
    /**
     * Sets up properties and hooks.
     */
    public function __construct( $aData ){
         
        // Data
        $this->aData = $this->_formatItems( $aData );

        // Set parent defaults
        $this->aArgs = array(
            'singular'  => 'template',        // singular name of the listed items
            'plural'    => 'templates',        // plural name of the listed items
            'ajax'      => false,            // does this table support ajax?
            'screen'    => null,            // not sure what this is for... 
        );
        if ( ! headers_sent() ) {
            add_action( 'admin_notices', array( $this, 'delayConstructor' ) );
        } else {
            parent::__construct( $this->aArgs );
        }
        
    }
    public function delayConstructor() {
        parent::__construct( $this->aArgs );
    }    
        /**
         * Formats the items.
         * 
         * Converts the template array into an object as object handles backward compatibility formatting processes.
         * 
         * @since       2.3.9
         * @return      array
         */
        private function _formatItems( array $aItems ) {

            $_aItems = array();
            foreach( $aItems as $_sSlug => $_aItem ) {
                if ( ! isset( $_aItem[ 'sSlug' ] ) ) {
                   continue; 
                }
                $_aItems[ $_aItem[ 'sSlug' ] ] = new FetchTweets_Template( $_aItem );
            }
            return $_aItems;
            
        }
    
    
    public function column_default( $oItem, $sColumnName ) {    // 'column_' + 'default'
    
        switch( $sColumnName ){

            case 'description':
                            
                //Build row actions
                $_aActions = array(
                    'version'    => sprintf( __( 'Version', 'fetch-tweets' ) . '&nbsp;' . $oItem->get( 'sVersion' ) ),
                    'author'     => sprintf( '<a href="%s">' . $oItem->get( 'sAuthor' ) . '</a>', $oItem->get( 'sAuthorURI' ) ),
                    'css'        => sprintf( 
                        '<a href="%s">' . __( 'CSS', 'fetch-tweets' ) . '</a>', 
                        $oItem->getURLByFIleName( 'style.css' ) 
                    ),
                );
                
                //Return the title contents
                return sprintf('%1$s <div class="active second">%2$s</div>',
                    /*$1%s*/ $oItem->get( 'sDescription' ),
                    /*$2%s*/ $this->row_actions( $_aActions )
                );
            case 'thumbnail':
                $_sThumbnailURL = $oItem->getThumbnailURL();
                if ( ! $_sThumbnailURL ) { return; }
                
                return "<a class='template-thumbnail' href='#thumb'>"
                        . "<img src='{$_sThumbnailURL}' style='max-width:80px; max-height:80px;' />"
                        . "<span>"
                            . "<div>"
                                . "<img src='{$_sThumbnailURL}' alt='" . esc_attr( $oItem->get( 'sName' ) ) . "' /><br />"
                                . $oItem->get( 'sName' )
                            . "</div>"
                        . "</span>"
                    . "</a>";                
            default:
                return print_r( $oItem, true ); //Show the whole array for troubleshooting purposes
        }
        
    }
    
        
    public function column_name( $oItem ){    // column_{$column_title}
        
        //Build row actions
        $_aActions = array();
        if ( $oItem->get( 'bIsActive' ) ) {
                        
            $_aActions[ 'deactivate' ] = $oItem->get( 'bIsDefault' )
                ? '<span class="disabled">' . __( 'Deactivate', 'fetch-tweets' ) . '</span>'
                : sprintf( '<a href="?post_type=%s&page=%s&action=%s&template=%s">' . __( 'Deactivate', 'fetch-tweets' ) . '</a>', FetchTweets_Commons::PostTypeSlug, $_REQUEST['page'], 'deactivate', $oItem->getSlug() );
            $_aActions[ 'set_default' ] = $oItem->get( 'bIsDefault' )
                ? '<span class="disabled">' . __( 'Set Default', 'fetch-tweets' ) . '</span>'
                : sprintf( '<a href="?post_type=%s&page=%s&action=%s&template=%s">' . __( 'Set Default', 'fetch-tweets' ) . '</a>', FetchTweets_Commons::PostTypeSlug, $_REQUEST['page'], 'set_default', $oItem->getSlug() );    
                
        } else {
            $_aActions[ 'activate' ] = sprintf( '<a href="?post_type=%s&page=%s&action=%s&template=%s">' . __( 'Activate', 'fetch-tweets' ) . '</a>', FetchTweets_Commons::PostTypeSlug, $_REQUEST['page'], 'activate', $oItem->getSlug() );
        }
        $_aActions = apply_filters( 'fetch_tweets_filter_template_listing_table_action_links', $_aActions, $oItem->getSlug() );    

        //Return the title contents
        return sprintf('%1$s %2$s %3$s',    // <span style="color:silver">(id:%2$s)</span>
            /*$1%s*/ $oItem->get( 'bIsActive' ) ? "<strong>" . $oItem->get( 'sName' ) . "</strong>" : $oItem->get( 'sName' ),
            /*$2%s*/ $oItem->get( 'bIsDefault' ) ? "<strong>(" . __( 'Default', 'fetch-tweets' ) . ")</strong>" : '',
            /*$3%s*/ $this->row_actions( $_aActions )
        );
        
    }
    
    public function column_cb( $oItem ){    // column_ + cb
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  
            /*$2%s*/ $oItem->getSlug()  //The value of the checkbox should be the record's id
        );
    }
    
    
    public function get_columns() {
        
        return array(
            'cb'             => '<input type="checkbox" />', //Render a checkbox instead of text
            'name'           => __( 'Template Name', 'fetch-tweets' ),
            'thumbnail'      => __( 'Thumbnail', 'fetch-tweets' ),
            'description'    => __( 'Description', 'fetch-tweets' ),
        );
        
    }
    
    public function get_sortable_columns() {
        
        return array(
            'name'          => array( 'name', false ),     //true means it's already sorted
            // 'thumbnail'  => array( 'thumbnail', false ),
            'description'   => array( 'description', false ),
        );
        
    }
    
    public function get_bulk_actions() {
        return array(
            // 'delete'    => 'Delete',
            'activate'      => __( 'Activate', 'fetch-tweets' ),
            'deactivate'    => __( 'Deactivate', 'fetch-tweets' ),
        );
    }
    
    /**
     * Deals with the bulk actions.
     * 
     * Called from outside.
     * @todo        Adapt the new template format.
     */
    public function process_bulk_action() {
        
        if ( ! isset( $_REQUEST['template'] ) ) { return; }
        
        $_oOption = FetchTweets_Option::getInstance();
        switch( strtolower( $this->current_action() ) ){

            case 'activate':
                foreach( ( array ) $_REQUEST['template'] as $_sTemplateSlug ) {
                    $_oTemplate = $this->aData[ $_sTemplateSlug ];
                    $_oTemplate->aData['bIsActive'] = true;
                    $_oOption->aOptions['arrTemplates'][ $_sTemplateSlug ] = $_oTemplate->aData;
                }
                break;
            case 'deactivate':
                foreach( ( array ) $_REQUEST['template'] as $_sTemplateSlug ) {
                    // $this->aData[ $_sTemplateSlug ]['bIsActive'] = false;
                    // the option array only stores active templates.
                    unset( $_oOption->aOptions['arrTemplates'][ $_sTemplateSlug ] );    
                }
                break;            
            case 'set_default':

                if ( ! is_string( $_REQUEST['template'] ) ) { 
                    return; 
                }
            
                // Set the other templates not to be default.
                foreach( $_oOption->aOptions['arrTemplates'] as &$_aTemplate )  {   // the saved template option array
                    $_aTemplate['bIsDefault'] = false;
                }
                unset( $_oTemplate ); // release the reference in foreach(), to be safe.
                
                // Enable the selected default template.
                $_aDefaultTemplate = $this->aData[ $_REQUEST['template'] ]->aData;
                $_aDefaultTemplate['bIsDefault'] = true;
                $_oOption->aOptions['arrTemplates'][ $_REQUEST['template'] ] = $_aDefaultTemplate;
                $_oOption->aOptions['arrDefaultTemplate'] = $_aDefaultTemplate;
                    
                break;    
            default:
                return;    // do nothing.
                
        }   
        
        $_oOption->saveOptions();
        wp_redirect( admin_url( 'edit.php?post_type=' . FetchTweets_Commons::PostTypeSlug . '&page=' . FetchTweets_Commons::$aPageSlugs[ 'template' ] ) );
        
    }

    function prepare_items() {
            
        /**
         * Set how many records per page to show
         */
        $_iItemsPerPage = 20;
        
    
        /**
         * Define our column headers. 
         */
        $this->_column_headers = array( 
            $this->get_columns(), // $arrColumns
            array(), // $arrHidden
            $this->get_sortable_columns() // $arrSortable
        );
        
     
        /**
         * Process bulk actions.
         */
        // in our case, it is dealt before the header is sent. ( with the Admin page class )
        // $this->process_bulk_action(); 
              
        /**
         * Variables
         */
        $_aData = $this->aData;
                
        
        /**
         * Sort the array.
         */
        usort( $_aData, array( $this, 'usort_reorder' ) );

        /**
         * For pagination.
         */
        $_iCurrentPageNumber    = $this->get_pagenum();
        $_iTotalItems           = count( $_aData );
        $this->set_pagination_args( 
            array(
                'total_items' => $_iTotalItems,                      // calculate the total number of items
                'per_page'    => $_iItemsPerPage,                     // determine how many items to show on a page
                'total_pages' => ceil( $_iTotalItems / $_iItemsPerPage )   // calculate the total number of pages
            )
        );
        $_aData = array_slice( 
            $_aData, 
            ( $_iCurrentPageNumber -1 ) * $_iItemsPerPage,
            $_iItemsPerPage 
        );
        
        /*
         * Set data
         * */
        $this->items = $_aData;
        
    }
        /**
         * Compares two values.
         */
        public function usort_reorder( $a, $b ) {
            
            $_sOrderBy = $this->_getKeyOfOrderBy();
                        
            $_sOrder = ! empty( $_REQUEST['order'] )
                ? strtolower( $_REQUEST['order'] )
                : 'asc'; //If no order, default to asc
            $_iResult = strcmp( $a->get( $_sOrderBy ), $b->get( $_sOrderBy ) ); //Determine sort order
            return 'asc' === $_sOrder 
                ? $_iResult 
                : -$_iResult; // Send final sort direction to usort
            
        }
            private function _getKeyOfOrderBy() {
                
                if ( empty( $_REQUEST['orderby'] ) ) {
                    return 'iIndex';
                }
                if ( 'description' === $_REQUEST['orderby'] ) {
                    return 'sDescription';
                }
                if ( 'name' === $_REQUEST['orderby'] ) {
                    return 'sName';
                }
                
            }
    
}
