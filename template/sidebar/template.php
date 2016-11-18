<?php
/**
 * Available variables passed from the caller script
 * - $aTweets    :the fetched tweet arrays.
 * - $aArguments :the passed arguments such as item count etc.
 * - $aOptions   :the plugin options saved in the database.
 * 
 * @see     https://dev.twitter.com/overview/terms/display-requirements
 */
 
$_oUtil = new FetchTweets_PluginUtility;

// Set the default template option values.
$_aDefaultTemplateOptions = array(
    'avatar_size'       => 24,
    'avatar_position'   => 'left',
    'width'             => array( 'size' => 100, 'unit' => '%' ),
    'height'            => array( 'size' => 100, 'unit' => '%' ),
    'background_color'  => 'transparent',
    'intent_buttons'    => 2,
    'intent_script'     => 1,
    'visibilities'      => array(
        'avatar'            => true,
        'user_name'         => true,
        'follow_button'     => false,   // 2.3.8+
        // 'user_description' => true,
        'time'              => true,            
        'intent_buttons'    => true,
    ),
    'margins' => array(
        0 => array( 'size' => '', 'unit' => 'px' ),    // top
        1 => array( 'size' => '', 'unit' => 'px' ),    // right
        2 => array( 'size' => '', 'unit' => 'px' ),    // bottom
        3 => array( 'size' => '', 'unit' => 'px' ),    // left
    ),
    'paddings' => array(
        0 => array( 'size' => '', 'unit' => 'px' ),    // top
        1 => array( 'size' => '', 'unit' => 'px' ),    // right
        2 => array( 'size' => '', 'unit' => 'px' ),    // bottom
        3 => array( 'size' => '', 'unit' => 'px' ),    // left
    ),    
    'follow_button_elements'   => array(
        'screen_name'       => 0,
        'follower_count'    => 0,    
    ),    
);


// Some new setting items are not stored in the database, so merge the saved options with the defined default values.     
$aTemplateOptions = $_oUtil->uniteArrays( 
    $_oUtil->getElementAsArray( $aOptions, 'fetch_tweets_template_sidebar' ), 
    $_aDefaultTemplateOptions
);

$aArguments['avatar_size']               = isset( $aArguments['avatar_size'] ) ? $aArguments['avatar_size'] : $aTemplateOptions['avatar_size'];
$aArguments['avatar_position']           = isset( $aArguments['avatar_position'] ) ? $aArguments['avatar_position'] : $aTemplateOptions['avatar_position'];
$aArguments['width']                     = isset( $aArguments['width'] ) ? $aArguments['width'] : $aTemplateOptions['width']['size'];
$aArguments['width_unit']                = isset( $aArguments['width_unit'] ) ? $aArguments['width_unit'] : $aTemplateOptions['width']['unit'];
$aArguments['height']                    = isset( $aArguments['height'] ) ? $aArguments['height']: $aTemplateOptions['height']['size'];
$aArguments['height_unit']               = isset( $aArguments['height_unit'] ) ? $aArguments['height_unit'] : $aTemplateOptions['height']['unit'];
$aArguments['background_color']          = isset( $aArguments['background_color'] ) ? $aArguments['background_color'] : $aTemplateOptions['background_color'];
$aArguments['visibilities']              = isset( $aArguments['visibilities'] ) ? $aArguments['visibilities'] : $aTemplateOptions['visibilities'];
$aArguments['margin_top']                = isset( $aArguments['margin_top'] ) ? $aArguments['margin_top'] : $aTemplateOptions['margins'][0]['size'];
$aArguments['margin_top_unit']           = isset( $aArguments['margin_top_unit'] ) ? $aArguments['margin_top_unit'] : $aTemplateOptions['margins'][0]['unit'];
$aArguments['margin_right']              = isset( $aArguments['margin_right'] ) ? $aArguments['margin_right'] : $aTemplateOptions['margins'][1]['size'];
$aArguments['margin_right_unit']         = isset( $aArguments['margin_right_unit'] ) ? $aArguments['margin_right_unit'] : $aTemplateOptions['margins'][1]['unit'];
$aArguments['margin_bottom']             = isset( $aArguments['margin_bottom'] ) ? $aArguments['margin_bottom'] : $aTemplateOptions['margins'][2]['size'];
$aArguments['margin_bottom_unit']        = isset( $aArguments['margin_bottom_unit'] ) ? $aArguments['margin_bottom_unit'] : $aTemplateOptions['margins'][2]['unit'];
$aArguments['margin_left']               = isset( $aArguments['margin_left'] ) ? $aArguments['margin_left'] : $aTemplateOptions['margins'][3]['size'];
$aArguments['margin_left_unit']          = isset( $aArguments['margin_left_unit'] ) ? $aArguments['margin_left_unit'] : $aTemplateOptions['margins'][3]['unit'];
$aArguments['padding_top']               = isset( $aArguments['padding_top'] ) ? $aArguments['padding_top'] : $aTemplateOptions['paddings'][0]['size'];
$aArguments['padding_top_unit']          = isset( $aArguments['padding_top_unit'] ) ? $aArguments['padding_top_unit'] : $aTemplateOptions['paddings'][0]['unit'];
$aArguments['padding_right']             = isset( $aArguments['padding_right'] ) ? $aArguments['padding_right'] : $aTemplateOptions['paddings'][1]['size'];
$aArguments['padding_right_unit']        = isset( $aArguments['padding_right_unit'] ) ? $aArguments['padding_right_unit'] : $aTemplateOptions['paddings'][1]['unit'];
$aArguments['padding_bottom']            = isset( $aArguments['padding_bottom'] ) ? $aArguments['padding_bottom'] : $aTemplateOptions['paddings'][2]['size'];
$aArguments['padding_bottom_unit']       = isset( $aArguments['padding_bottom_unit'] ) ? $aArguments['padding_bottom_unit'] : $aTemplateOptions['paddings'][2]['unit'];
$aArguments['padding_left']              = isset( $aArguments['padding_left'] ) ? $aArguments['padding_left'] : $aTemplateOptions['paddings'][3]['size'];
$aArguments['padding_left_unit']         = isset( $aArguments['padding_left_unit'] ) ? $aArguments['padding_left_unit'] : $aTemplateOptions['paddings'][3]['unit'];
$aArguments['intent_buttons']            = isset( $aArguments['intent_buttons'] ) ? $aArguments['intent_buttons'] : ( ! $aArguments['visibilities']['intent_buttons'] ? 0 : $aTemplateOptions['intent_buttons'] );    // 0: do not show, 1: icons and text, 2: only icons, 3: only text.
$aArguments['intent_button_script']      = isset( $aArguments['intent_button_script'] ) ? $aArguments['intent_button_script'] : $aTemplateOptions['intent_script'];

$aArguments['follow_button_elements']    = isset( $aArguments['follow_button_elements'] ) ? $aArguments['follow_button_elements'] : $aTemplateOptions['follow_button_elements'];
$aArguments['follow_button_screen_name'] = isset( $aArguments['follow_button_screen_name'] ) ? $aArguments['follow_button_screen_name'] : ( $aArguments['follow_button_elements']['screen_name'] ? $aArguments['follow_button_elements']['screen_name'] : "false" );
$aArguments['follow_button_count']       = isset( $aArguments['follow_button_count'] ) ? $aArguments['follow_button_count'] : ( $aArguments['follow_button_elements']['follower_count'] ? $aArguments['follow_button_elements']['follower_count'] : "false" );


$sWidth             = $aArguments['width'] ? "max-width: " . $aArguments['width'] . $aArguments['width_unit'] . "; " : '';
$sHeight            = $aArguments['height'] ? "max-height: " . $aArguments['height'] . $aArguments['height_unit'] . "; " : '';
$sBackgroundColor   = $aArguments['background_color'] ? "background-color: {$aArguments['background_color']}; " : '';
$sOverflowY         = '100%' === $aArguments['height'] . $aArguments['height_unit'] ? 'overflow-y: hidden; ' : '';     // removes the vertical scroll bar.
$sMarginTop         = empty( $aArguments['margin_top'] ) ? 0 : $aArguments['margin_top'] . $aArguments['margin_top_unit'];
$sMarginRight       = empty( $aArguments['margin_right'] ) ? 0 : $aArguments['margin_right'] . $aArguments['margin_right_unit'];
$sMarginBottom      = empty( $aArguments['margin_bottom'] ) ? 0 : $aArguments['margin_bottom'] . $aArguments['margin_bottom_unit'];
$sMarginLeft        = empty( $aArguments['margin_left'] ) ? 0 : $aArguments['margin_left'] . $aArguments['margin_left_unit'];
$sPaddingTop        = empty( $aArguments['padding_top'] ) ? 0 : $aArguments['padding_top'] . $aArguments['padding_top_unit'];
$sPaddingRight      = empty( $aArguments['padding_right'] ) ? 0 : $aArguments['padding_right'] . $aArguments['padding_right_unit'];
$sPaddingBottom     = empty( $aArguments['padding_bottom'] ) ? 0 : $aArguments['padding_bottom'] . $aArguments['padding_bottom_unit'];
$sPaddingLeft       = empty( $aArguments['padding_left'] ) ? 0 : $aArguments['padding_left'] . $aArguments['padding_left_unit'];
$sMargins           = ( $sMarginTop ? "margin-top: {$sMarginTop}; " : "" ) . ( $sMarginRight ? "margin-right: {$sMarginRight}; " : "" ) . ( $sMarginBottom ? "margin-bottom: {$sMarginBottom}; " : "" ) . ( $sMarginLeft ? "margin-left: {$sMarginLeft}; " : "" );
$sPaddings          = ( $sPaddingTop ? "padding-top: {$sPaddingTop}; " : "" ) . ( $sPaddingRight ? "padding-right: {$sPaddingRight}; " : "" ) . ( $sPaddingBottom ? "padding-bottom: {$sPaddingBottom}; " : "" ) . ( $sPaddingLeft ? "padding-left: {$sPaddingLeft}; " : "" );
$iHeightForAvatar   = $aArguments[ 'visibilities' ][ 'avatar' ] 
    ? ( int ) $aArguments[ 'avatar_size' ] . 'px'
    : '';
$iGMTOffset         = ( integer ) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
$bIsSSL             = is_ssl();
$sURLReplyuButton   = esc_url( FetchTweets_Commons::getPluginURL( 'asset/image/reply_48x16.png' ) );
$sURLRetweetButton  = esc_url( FetchTweets_Commons::getPluginURL( 'asset/image/retweet_48x16.png' ) );
$sURLFavoriteButton = esc_url( FetchTweets_Commons::getPluginURL( 'asset/image/favorite_48x16.png' ) );

$sOppsiteAvatarPos  = 'left' === $aArguments[ 'avatar_position' ] ? 'right' : 'left';

/*
 * For debugging - uncomment the following lines to see the contents of the arrays.
 */ 
// echo "<pre>" . htmlspecialchars( print_r( $aTweets, true ) ) . "</pre>";     
// echo "<pre>" . htmlspecialchars( print_r( $aArguments, true ) ) . "</pre>";     
// return;

// Start the layout. 
?>

<div class='fetch-tweets-sidebar' style="<?php echo esc_attr( $sWidth . $sHeight . $sBackgroundColor . $sMargins . $sPaddings . $sOverflowY ); ?>">

    <?php foreach ( $aTweets as $_aDetail ) : ?>
    <?php 
        // If the necessary key is not set, skip.
        if ( ! isset( $_aDetail['user'] ) ) { 
            continue; 
        }
        
        // Check if it's a retweet.
        $_bIsRetweet = isset( $_aDetail['retweeted_status']['text'] );
        if ( $_bIsRetweet && ! $aArguments[ 'include_rts' ] ) { 
            continue; 
        }
        $aTweet                 = $_bIsRetweet ? $_aDetail['retweeted_status'] : $_aDetail;
        $sRetweetClassSelector  = $_bIsRetweet ? 'fetch-tweets-sidebar-retweet' : '';
        
    ?>
    <div class="fetch-tweets-sidebar-item <?php echo $sRetweetClassSelector; ?>">
        <div class='fetch-tweets-sidebar-main'>
            <div class='fetch-tweets-sidebar-heading' style='min-height:<?php echo $iHeightForAvatar; ?>;'>
                <?php if ( $aArguments['avatar_size'] > 0  && $aArguments['visibilities']['avatar'] ) : 
                    $sAvatarURL = getTwitterProfileImageURLBySize( $bIsSSL ? $aTweet['user']['profile_image_url_https'] : $aTweet['user']['profile_image_url'], $aArguments['avatar_size'] );
                    $sAvatarURL = esc_url( $sAvatarURL );
                    $sAvatarURLSubstitute = getTwitterProfileImageURLBySize( $bIsSSL ? $aTweet['user']['profile_image_url_https'] : $aTweet['user']['profile_image_url'], 100 );
                    $sAvatarURLSubstitute = esc_url( $sAvatarURLSubstitute );
                ?>
                <div class='fetch-tweets-sidebar-profile-image' style="max-width:<?php echo $aArguments['avatar_size'];?>px; float:<?php echo $aArguments['avatar_position']; ?>; clear:<?php echo $aArguments['avatar_position']; ?>; margin-<?php echo $sOppsiteAvatarPos;?>: 0.5em;">
                    <a href='<?php echo esc_url( "https://twitter.com/" . $aTweet['user']['screen_name'] ); ?>' target='_blank'>
                        <img src='<?php echo $sAvatarURL; ?>' style='max-width:<?php echo $aArguments['avatar_size'];?>px; border-radius: 5px;' alt='<?php echo esc_attr( sprintf( __( 'The profile image of %1$s', 'fetch-tweets' ), $aTweet['user']['screen_name'] ) ); ?>' onError='this.onerror=null;this.src="<?php echo $sAvatarURLSubstitute; ?>";' />
                    </a>
                </div><!-- fetch-tweets-sidebar-profile-image -->
                <?php endif; ?>            
                <?php if ( $aArguments['visibilities']['follow_button'] ) : ?>  
                <div class='fetch-tweets-sidebar-follow-button'>
                    <a href="<?php echo esc_url( 'https://twitter.com/' . $aTweet['user']['screen_name'] ); ?>" class="twitter-follow-button" target="_blank" data-lang="<?php echo esc_attr( $aTweet['user']['lang'] ); ?>" data-show-count="<?php echo esc_attr( $aArguments['follow_button_count'] ); ?>" data-show-screen-name="<?php echo esc_attr( $aArguments['follow_button_screen_name'] ); ?>">
                        <?php echo __( 'Follow', 'fetch-tweets' ) . '@' . $aTweet['user']['screen_name']; ?>
                    </a>
                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                </div>        
                <?php endif; ?>            
            
                <?php if ( $aArguments['visibilities']['user_name'] ) : ?>
                <span class='fetch-tweets-sidebar-user-name'>
                    <strong>
                        <a href='<?php echo esc_url( "https://twitter.com/" . $aTweet['user']['screen_name'] ); ?>' target='_blank'>
                            <?php echo $aTweet['user']['name']; ?>
                        </a>
                    </strong>
                </span>
                <?php endif; ?>
                
                <?php if ( $aArguments['visibilities']['time'] ) : ?>
                <span class='fetch-tweets-sidebar-tweet-created-at'>
                    <a href='<?php echo esc_url( "https://twitter.com/" . $aTweet['user']['screen_name'] . "/status/" . $aTweet['id_str'] ); ?>' target='_blank'>
                        <?php echo human_time_diff( strtotime( $aTweet[ 'created_at' ] ), current_time( 'timestamp' ) - $iGMTOffset ) . ' ' . __( 'ago', 'fetch-tweets' ); ?>
                    </a>            
                </span>
                <?php endif; ?>
                
            </div><!-- fetch-tweets-sidebar-heading -->
            <div class='fetch-tweets-sidebar-body'>
                <p class='fetch-tweets-sidebar-text'><?php echo $aTweet['text']; ?>
                    <?php if ( isset( $_aDetail['retweeted_status']['text'] ) ) : ?>
                    <span class='fetch-tweets-sidebar-retweet-credit'><?php _e( 'Retweeted by', 'fetch-tweets' ) . ' '; ?><a href='<?php echo esc_url( "https://twitter.com/" . $_aDetail['user']['screen_name'] ); ?>' target='_blank'>
                            <?php echo $_aDetail['user']['name']; ?>
                        </a>
                    </span>
                    <?php endif; ?>
                </p><!-- fetch-tweets-sidebar-text -->
                <?php if ( isset( $_aDetail['_media'] ) ) : ?>
                    <?php echo $_aDetail['_media']; ?>
                <?php endif; ?>
                <?php if ( $aArguments['intent_buttons'] ) : ?>
                    <?php if ( $aArguments['intent_button_script'] ) : ?>
                    <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
                    <?php endif; ?>
                    <ul class='fetch-tweets-sidebar-intent-buttons'>
                        <li class='fetch-tweets-sidebar-intent-reply'>
                            <a href='<?php echo esc_url( "https://twitter.com/intent/tweet?in_reply_to=" . $_aDetail['id_str'] ); ?>' rel='nofollow' target='_blank' title='<?php echo esc_attr( __( 'Reply', 'fetch-tweets' ) ); ?>'>
                                <?php if ( 1 == $aArguments['intent_buttons'] || 2 == $aArguments['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-sidebar-intent-icon' style='background-image: url("<?php echo $sURLReplyuButton; ?>");' ></span>
                                <?php endif; ?>
                                <?php if ( 1 == $aArguments['intent_buttons'] || 3 == $aArguments['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-sidebar-intent-buttons-text'>
                                    <?php _e( 'Reply', 'fetch-tweets' ); ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class='fetch-tweets-sidebar-intent-retweet'>
                            <a href='<?php echo esc_url( "https://twitter.com/intent/retweet?tweet_id=" . $_aDetail['id_str'] ); ?>' rel='nofollow' target='_blank' title='<?php echo esc_attr( __( 'Retweet', 'fetch-tweets' ) ); ?>'>
                                <?php if ( 1 == $aArguments['intent_buttons'] || 2 == $aArguments['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-sidebar-intent-icon' style='background-image: url("<?php echo $sURLRetweetButton; ?>");' ></span>
                                <?php endif; ?>
                                <?php if ( 1 == $aArguments['intent_buttons'] || 3 == $aArguments['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-sidebar-intent-buttons-text'>
                                    <?php _e( 'Retweet', 'fetch-tweets' ); ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class='fetch-tweets-sidebar-intent-favorite'>
                            <a href='<?php echo esc_url( "https://twitter.com/intent/favorite?tweet_id=" . $_aDetail['id_str'] ); ?>' rel='nofollow' target='_blank' title='<?php echo esc_attr( __( 'Favorite', 'fetch-tweets' ) ); ?>'>
                                <?php if ( 1 == $aArguments['intent_buttons'] || 2 == $aArguments['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-sidebar-intent-icon' style='background-image: url("<?php echo $sURLFavoriteButton; ?>");' ></span>
                                <?php endif; ?>
                                <?php if ( 1 == $aArguments['intent_buttons'] || 3 == $aArguments['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-sidebar-intent-buttons-text'>
                                    <?php _e( 'Favorite', 'fetch-tweets' ); ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </li>        
                    </ul><!-- fetch-tweets-sidebar-intent-buttons -->
                <?php endif; ?>
            </div><!-- fetch-tweets-sidebar-body -->
        </div><!-- fetch-tweets-sidebar-main -->
    </div><!-- fetch-tweets-sidebar-item -->
    <?php endforeach; ?>    
</div><!-- fetch-tweets -->
