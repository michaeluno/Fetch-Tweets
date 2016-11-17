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
 * Defines the main plugin post type used for tweet fetching rules.
 */
class FetchTweets_PostType extends FetchTweets_AdminPageFramework_PostType {

    public function setUp() {

        $this->oOption = FetchTweets_Option::getInstance();

        $_sCapability = FetchTweets_Option::get( array( 'capabilities', 'setting_page_capability' ), 'manage_options' );
        
        $this->setArguments(
            array(            // argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
                'labels'            => $this->_getPostTypeLabelArguments(),
                'public'            => true,
                'menu_position'     => 110,
                'supports'          => array( 'title' ),
                'taxonomies'        => array( '' ),
                'menu_icon'         => $this->oProp->bIsAdmin 
                    ? FetchTweets_Commons::getPluginURL( '/asset/image/menu_icon_16x16.png' ) 
                    : '',
                'has_archive'       => true,
                'hierarchical'      => false,
                'show_admin_column' => true,
                'screen_icon'       => $this->oProp->bIsAdmin
                    ? FetchTweets_Commons::getPluginURL( "/asset/image/screen_icon_32x32.png" )
                    : '',        
                'exclude_from_search' => ! FetchTweets_Option::get( array( 'search', 'is_searchable' ) ),
                'capabilities' => array(    // 2.4.8+
                    'publish_posts'         => $_sCapability, // 'publish_assignments',
                    'edit_posts'            => $_sCapability, // 'edit_assignments',
                    'edit_others_posts'     => $_sCapability, // 'edit_others_assignments',
                    'delete_posts'          => $_sCapability, // 'delete_assignments',
                    'delete_others_posts'   => $_sCapability, // 'delete_others_assignments',
                    'read_private_posts'    => $_sCapability, // 'read_private_assignments',
                    'edit_post'             => $_sCapability, // 'edit_assignment',
                    'delete_post'           => $_sCapability, // 'delete_assignment',
                    'read_post'             => $_sCapability, // 'read_assignment'
                ),   
                'show_submenu_add_new'  => false,   // 2.5.0+ an admin page will be placed instead                
            )        
        );

        $this->addTaxonomy( 
            FetchTweets_Commons::TagSlug, 
            array(
                'labels'                => $this->oProp->bIsAdmin 
                    ? $this->_getTaxonomyTagLabelArgumnents()
                    : array(),
                'show_ui'               => true,
                'show_tagcloud'         => false,
                'hierarchical'          => false,
                'show_admin_column'     => true,
                'show_in_nav_menus'     => false,
                'show_table_filter'     => true,    // framework specific key
                'show_in_sidebar_menus' => true,    // framework specific key
                'submenu_order'         => 70,      // 2.5.0
            )
        );
        
        $_sCurrentPostTypeInAdmin = isset( $GLOBALS['post_type'] ) 
            ? $GLOBALS['post_type']
            : ( isset( $_GET['post_type'] ) ? $_GET['post_type'] : '' );
        
        // For admin
        if ( $_sCurrentPostTypeInAdmin === $this->oProp->sPostType && is_admin() ) {
            
            $this->setAutoSave( false );
            $this->setAuthorTableFilter( true );            
            add_filter( 'enter_title_here', array( $this, '_replyToChangeTitleMetaBoxFieldLabel' ) );   
            
        }
               
    }
   
        private function _getPostTypeLabelArguments() {            
            return array(
                'name'                  => __( 'Fetch Tweets', 'fetch-tweets' ),
                'all_items'             => __( 'Manage Rules', 'fetch-tweets' ),    // sub menu label
                'singular_name'         => __( 'Fetch Tweets Rule', 'fetch-tweets' ),
                'menu_name'             => __( 'Fetch Tweets', 'fetch-tweets' ),    // this changes the root menu name 
                'add_new'               => __( 'Add Rule by Screen Name', 'fetch-tweets' ),
                'add_new_item'          => __( 'Add New Rule', 'fetch-tweets' ),
                'edit'                  => __( 'Edit', 'fetch-tweets' ),
                'edit_item'             => __( 'Edit Rule', 'fetch-tweets' ),
                'new_item'              => __( 'New Rule', 'fetch-tweets' ),
                'view'                  => __( 'View', 'fetch-tweets' ),
                'view_item'             => __( 'View Fetched Tweets', 'fetch-tweets' ),
                'search_items'          => __( 'Search Rules', 'fetch-tweets' ),
                'not_found'             => __( 'No rule found for fetching tweets', 'fetch-tweets' ),
                'not_found_in_trash'    => __( 'No Rule Found for Fetching Tweets in Trash', 'fetch-tweets' ),
                'parent'                => __( 'Parent Rule', 'fetch-tweets' ),
            );
        }
        private function _getTaxonomyTagLabelArgumnents() {
            return array(
                'name' => __( 'Tags', 'fetch-tweets' ),
                'add_new_item' => __( 'Add New Tag', 'fetch-tweets' ),
                'new_item_name' => __( 'New Tag', 'fetch-tweets' ),
            );
            
        }
    public function _replyToChangeTitleMetaBoxFieldLabel( $sText ) {
        return __( 'Set the rule name here.', 'fetch-tweets' );        
    }
        
    /**
     * Displays tweet previews in the front-end.
     * 
     * @since       Unknown
     * @since       2.4.8       Changed the name from `_replyToPreviewTweets`.
     */
    public function content( $sContent ) {
        return $sContent 
            . fetchTweets( 
                array( 
                    'id'    => $GLOBALS[ 'post' ]->ID,
                    'count' => get_post_meta( $GLOBALS[ 'post' ]->ID, 'item_count', true ),
                ),
                false // do not echo but return the output
            );    
    }

    /*
     * Extensible methods
     */
    public function columns_fetch_tweets( $aHeaderColumns ) {    // columns_{post type slug}
        
        return    array(
            'cb'                =>    '<input type="checkbox" />',    // Checkbox for bulk actions. 
            'title'             =>    __( 'Rule Name', 'fetch-tweets' ),        // Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
            'tweettype'         =>    __( 'Tweet Type', 'fetch-tweets' ),
            'template'          =>    __( 'Template',    'fetch-tweets' ),
            'fetch_tweets_tag'  => __( 'Tags', 'fetch-tweets' ),    // Tags for the post. 
            'code'              => __( 'Shortcode / PHP Code', 'fetch-tweets' ),
        );                    
        
    }    

    // public function sortable_fetch_tweets( $aSortableHeaderColumns ) {    // sortable_columns_{post type slug}
        // return $aSortableHeaderColumns;
    // }        

    /*
     * Callback methods
     */
    public function cell_fetch_tweets_fetch_tweets_tag( $sCell, $iPostID ) {    // cell_{post type}_{column key}
        
        // Get the genres for the post.
        $_aTerms = get_the_terms( $iPostID, FetchTweets_Commons::TagSlug );
    
        // If no tag is assigned to the post,
        if ( empty( $_aTerms ) ) {
            return '—';
        }
    
        // Loop through each term, linking to the 'edit posts' page for the specific term. 
        $_aOutput = array();
        foreach ( $_aTerms as $_oTerm ) {
            $_aOutput[] = sprintf( '<a href="%s">%s</a>',
                esc_url( add_query_arg( array( 'post_type' => $GLOBALS['post']->post_type, FetchTweets_Commons::TagSlug => $_oTerm->slug ), 'edit.php' ) ),
                esc_html( sanitize_term_field( 'name', $_oTerm->name, $_oTerm->term_id, FetchTweets_Commons::TagSlug, 'display' ) )
            );
        }

        // Join the terms, separating them with a comma.
        return join( ', ', $_aOutput );
        
    }
    public function cell_fetch_tweets_tweettype( $sCell, $iPostID ) {    // cell_{post type slug}_{column key}

        switch ( get_post_meta( $iPostID, 'tweet_type', true ) ) {
            case 'search':
                return __( 'Search', 'fetch-tweets' );
            case 'screen_name':
                return __( 'Screen Name (User Timeline)', 'fetch-tweets' );
            case 'list':
                return __( 'List', 'fetch-tweets' );
            case 'home_timeline':
                return __( 'Home Timeline', 'fetch-tweets' );
            case 'feed':
                return __( 'Feed', 'fetch-tweets' );                
            case 'custom_query':
                return __( 'Custom Query', 'fetch-tweets' );                                
            case 'tweet_id':    // 2.3+
                return __( 'Tweet ID', 'fetch-tweets' );                                                
        }

    }
    
    /**
     * Returns the template name by post ID.
     */
    public function cell_fetch_tweets_template( $sCell, $iPostID ) {    // cell_{post type slug}_{column key}

        $_sTemplateSlug = get_post_meta( $iPostID, 'fetch_tweets_template', true );
        $_oTemplate     = new FetchTweets_Template( $_sTemplateSlug );
        return $sCell . $_oTemplate->get( 'sName' );
        
    }
    
    public function cell_fetch_tweets_code( $sCell, $iPostID ) {
        return '<p>'
                . '<span>[fetch_tweets id="' . $iPostID . '"]</span>' . '<br />'
                . '<span>&lt;?php fetchTweets( array( ‘id’ =&gt; ' . $iPostID . ' ) ); ?&gt;</span>'        
            . '</p>';
    }
    
}
