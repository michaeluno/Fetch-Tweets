<?php
/**
 * Available variables passed from the caller script
 * - $aTweets    : the fetched tweet arrays.
 * - $aArguments : the passed arguments such as item count etc.
 * - $aOptions   : the plugin options saved in the database.
 * 
 * @see     https://dev.twitter.com/overview/terms/display-requirements
 */
 
$_oUtil = new FetchTweets_PluginUtility;

// Set the default template option values.
$aDefaultTemplateValues = array(
    'fetch_tweets_template_plain_avatar_size'       => 48,
    'fetch_tweets_template_plain_avatar_position'   => 'left',
    'fetch_tweets_template_plain_width'             => array( 'size' => 100, 'unit' => '%' ),
    'fetch_tweets_template_plain_height'            => array( 'size' => 100, 'unit' => '%' ),
    'fetch_tweets_template_plain_background_color'  => 'transparent',
    'fetch_tweets_template_plain_intent_buttons'    => 2,
    'fetch_tweets_template_plain_intent_script'     => 1,
    'fetch_tweets_template_plain_visibilities'      => array(
        'avatar'            => true,
        'user_name'         => true,
        'follow_button'     => false,   // 2.3.8+
        // 'user_description' => true,
        'time'              => true,            
        'intent_buttons'    => true,
    ),
    'fetch_tweets_template_plain_margins' => array(
        0 => array( 'size' => '', 'unit' => 'px' ),    // top
        1 => array( 'size' => '', 'unit' => 'px' ),    // right
        2 => array( 'size' => '', 'unit' => 'px' ),    // bottom
        3 => array( 'size' => '', 'unit' => 'px' ),    // left
    ),
    'fetch_tweets_template_plain_paddings' => array(
        0 => array( 'size' => '', 'unit' => 'px' ),    // top
        1 => array( 'size' => '', 'unit' => 'px' ),    // right
        2 => array( 'size' => '', 'unit' => 'px' ),    // bottom
        3 => array( 'size' => '', 'unit' => 'px' ),    // left
    ),    
    'fetch_tweets_template_plain_follow_button_elements'   => array(
        'screen_name'       => 0,
        'follower_count'    => 0,    
    ),    
);

// Some new setting items are not stored in the database, so merge the saved options with the defined default values.
$aTemplateOptions = $_oUtil->uniteArrays( 
    $_oUtil->getElementAsArray( $aOptions, 'fetch_tweets_template_plain' ),
    $aDefaultTemplateValues 
);    

// Finalize the template option values.
$aArgs['avatar_size']               = isset( $aArgs['avatar_size'] ) ? $aArgs['avatar_size'] : $aTemplateOptions['fetch_tweets_template_plain_avatar_size'];
$aArgs['avatar_position']           = isset( $aArgs['avatar_position'] ) ? $aArgs['avatar_position'] : $aTemplateOptions['fetch_tweets_template_plain_avatar_position'];
$aArgs['width']                     = isset( $aArgs['width'] ) ? $aArgs['width'] : $aTemplateOptions['fetch_tweets_template_plain_width']['size'];
$aArgs['width_unit']                = isset( $aArgs['width_unit'] ) ? $aArgs['width_unit'] : $aTemplateOptions['fetch_tweets_template_plain_width']['unit'];
$aArgs['height']                    = isset( $aArgs['height'] ) ? $aArgs['height']: $aTemplateOptions['fetch_tweets_template_plain_height']['size'];
$aArgs['height_unit']               = isset( $aArgs['height_unit'] ) ? $aArgs['height_unit'] : $aTemplateOptions['fetch_tweets_template_plain_height']['unit'];
$aArgs['background_color']          = isset( $aArgs['background_color'] ) ? $aArgs['background_color'] : $aTemplateOptions['fetch_tweets_template_plain_background_color'];
$aArgs['visibilities']              = isset( $aArgs['visibilities'] ) ? $aArgs['visibilities'] : $aTemplateOptions['fetch_tweets_template_plain_visibilities'];
$aArgs['margin_top']                = isset( $aArgs['margin_top'] ) ? $aArgs['margin_top'] : $aTemplateOptions['fetch_tweets_template_plain_margins'][0]['size'];
$aArgs['margin_top_unit']           = isset( $aArgs['margin_top_unit'] ) ? $aArgs['margin_top_unit'] : $aTemplateOptions['fetch_tweets_template_plain_margins'][0]['unit'];
$aArgs['margin_right']              = isset( $aArgs['margin_right'] ) ? $aArgs['margin_right'] : $aTemplateOptions['fetch_tweets_template_plain_margins'][1]['size'];
$aArgs['margin_right_unit']         = isset( $aArgs['margin_right_unit'] ) ? $aArgs['margin_right_unit'] : $aTemplateOptions['fetch_tweets_template_plain_margins'][1]['unit'];
$aArgs['margin_bottom']             = isset( $aArgs['margin_bottom'] ) ? $aArgs['margin_bottom'] : $aTemplateOptions['fetch_tweets_template_plain_margins'][2]['size'];
$aArgs['margin_bottom_unit']        = isset( $aArgs['margin_bottom_unit'] ) ? $aArgs['margin_bottom_unit'] : $aTemplateOptions['fetch_tweets_template_plain_margins'][2]['unit'];
$aArgs['margin_left']               = isset( $aArgs['margin_left'] ) ? $aArgs['margin_left'] : $aTemplateOptions['fetch_tweets_template_plain_margins'][3]['size'];
$aArgs['margin_left_unit']          = isset( $aArgs['margin_left_unit'] ) ? $aArgs['margin_left_unit'] : $aTemplateOptions['fetch_tweets_template_plain_margins'][3]['unit'];
$aArgs['padding_top']               = isset( $aArgs['padding_top'] ) ? $aArgs['padding_top'] : $aTemplateOptions['fetch_tweets_template_plain_paddings'][0]['size'];
$aArgs['padding_top_unit']          = isset( $aArgs['padding_top_unit'] ) ? $aArgs['padding_top_unit'] : $aTemplateOptions['fetch_tweets_template_plain_paddings'][0]['unit'];
$aArgs['padding_right']             = isset( $aArgs['padding_right'] ) ? $aArgs['padding_right'] : $aTemplateOptions['fetch_tweets_template_plain_paddings'][1]['size'];
$aArgs['padding_right_unit']        = isset( $aArgs['padding_right_unit'] ) ? $aArgs['padding_right_unit'] : $aTemplateOptions['fetch_tweets_template_plain_paddings'][1]['unit'];
$aArgs['padding_bottom']            = isset( $aArgs['padding_bottom'] ) ? $aArgs['padding_bottom'] : $aTemplateOptions['fetch_tweets_template_plain_paddings'][2]['size'];
$aArgs['padding_bottom_unit']       = isset( $aArgs['padding_bottom_unit'] ) ? $aArgs['padding_bottom_unit'] : $aTemplateOptions['fetch_tweets_template_plain_paddings'][2]['unit'];
$aArgs['padding_left']              = isset( $aArgs['padding_left'] ) ? $aArgs['padding_left'] : $aTemplateOptions['fetch_tweets_template_plain_paddings'][3]['size'];
$aArgs['padding_left_unit']         = isset( $aArgs['padding_left_unit'] ) ? $aArgs['padding_left_unit'] : $aTemplateOptions['fetch_tweets_template_plain_paddings'][3]['unit'];
$aArgs['intent_buttons']            = isset( $aArgs['intent_buttons'] ) ? $aArgs['intent_buttons'] : ( ! $aArgs['visibilities']['intent_buttons'] ? 0 : $aTemplateOptions['fetch_tweets_template_plain_intent_buttons'] );    // 0: do not show, 1: icons and text, 2: only icons, 3: only text.
$aArgs['intent_button_script']      = isset( $aArgs['intent_button_script'] ) ? $aArgs['intent_button_script'] : $aTemplateOptions['fetch_tweets_template_plain_intent_script'];

$aArgs['follow_button_elements']    = isset( $aArgs['follow_button_elements'] ) ? $aArgs['follow_button_elements'] : $aTemplateOptions['fetch_tweets_template_plain_follow_button_elements'];
$aArgs['follow_button_screen_name'] = isset( $aArgs['follow_button_screen_name'] ) ? $aArgs['follow_button_screen_name'] : ( $aArgs['follow_button_elements']['screen_name'] ? $aArgs['follow_button_elements']['screen_name'] : "false" );
$aArgs['follow_button_count']       = isset( $aArgs['follow_button_count'] ) ? $aArgs['follow_button_count'] : ( $aArgs['follow_button_elements']['follower_count'] ? $aArgs['follow_button_elements']['follower_count'] : "false" );


$sWidth             = $aArgs['width'] ? "max-width: " . $aArgs['width'] . $aArgs['width_unit'] . "; " : '';
$sHeight            = $aArgs['height'] ? "max-height: " . $aArgs['height'] . $aArgs['height_unit'] . "; " : '';
$sBackgroundColor   = $aArgs['background_color'] ? "background-color: {$aArgs['background_color']}; " : '';
$sOverflowY         = '100%' === $aArgs['height'] . $aArgs['height_unit'] ? 'overflow-y: hidden; ' : '';     // removes the vertical scroll bar.
$sMarginTop         = empty( $aArgs['margin_top'] ) ? 0 : $aArgs['margin_top'] . $aArgs['margin_top_unit'];
$sMarginRight       = empty( $aArgs['margin_right'] ) ? 0 : $aArgs['margin_right'] . $aArgs['margin_right_unit'];
$sMarginBottom      = empty( $aArgs['margin_bottom'] ) ? 0 : $aArgs['margin_bottom'] . $aArgs['margin_bottom_unit'];
$sMarginLeft        = empty( $aArgs['margin_left'] ) ? 0 : $aArgs['margin_left'] . $aArgs['margin_left_unit'];
$sPaddingTop        = empty( $aArgs['padding_top'] ) ? 0 : $aArgs['padding_top'] . $aArgs['padding_top_unit'];
$sPaddingRight      = empty( $aArgs['padding_right'] ) ? 0 : $aArgs['padding_right'] . $aArgs['padding_right_unit'];
$sPaddingBottom     = empty( $aArgs['padding_bottom'] ) ? 0 : $aArgs['padding_bottom'] . $aArgs['padding_bottom_unit'];
$sPaddingLeft       = empty( $aArgs['padding_left'] ) ? 0 : $aArgs['padding_left'] . $aArgs['padding_left_unit'];
$sMargins           = ( $sMarginTop ? "margin-top: {$sMarginTop}; " : "" ) . ( $sMarginRight ? "margin-right: {$sMarginRight}; " : "" ) . ( $sMarginBottom ? "margin-bottom: {$sMarginBottom}; " : "" ) . ( $sMarginLeft ? "margin-left: {$sMarginLeft}; " : "" );
$sPaddings          = ( $sPaddingTop ? "padding-top: {$sPaddingTop}; " : "" ) . ( $sPaddingRight ? "padding-right: {$sPaddingRight}; " : "" ) . ( $sPaddingBottom ? "padding-bottom: {$sPaddingBottom}; " : "" ) . ( $sPaddingLeft ? "padding-left: {$sPaddingLeft}; " : "" );
$sMarginForImage    = $aArgs['visibilities']['avatar'] ? ( ( $aArgs['avatar_position'] == 'left' ? "margin-left: " : "margin-right: " ) . ( ( int ) $aArgs['avatar_size'] ) . "px" ) : "";
$bIsSSL             = is_ssl();
$sURLReplyuButton   = esc_url( FetchTweets_Commons::getPluginURL( 'asset/image/reply_48x16.png' ) );
$sURLRetweetButton  = esc_url( FetchTweets_Commons::getPluginURL( 'asset/image/retweet_48x16.png' ) );
$sURLFavoriteButton = esc_url( FetchTweets_Commons::getPluginURL( 'asset/image/favorite_48x16.png' ) );
/*
 * For debugging - uncomment the following lines to see the contents of the arrays.
 */ 
// echo "<pre>" . htmlspecialchars( print_r( $aTweets, true ) ) . "</pre>";     
// echo "<pre>" . htmlspecialchars( print_r( $aArgs, true ) ) . "</pre>";     
// return;

// Start the layout. 
?>

<div class='fetch-tweets' style="<?php echo esc_attr( $sWidth . $sHeight . $sBackgroundColor . $sMargins . $sPaddings . $sOverflowY ); ?>">

    <?php foreach ( $aTweets as $_aDetail ) : ?>
    <?php 
        // If the necessary key is not set, skip.
        if ( ! isset( $_aDetail['user'] ) ) { 
            continue; 
        }
        
        // Check if it's a retweet.
        $_bIsRetweet = isset( $_aDetail['retweeted_status']['text'] );
        if ( $_bIsRetweet && ! $aArgs['include_rts'] ) { 
            continue; 
        }
        $aTweet                 = $_bIsRetweet ? $_aDetail['retweeted_status'] : $_aDetail;
        $sRetweetClassSelector  = $_bIsRetweet ? 'fetch-tweets-retweet' : '';
        
    ?>
    <div class="fetch-tweets-item <?php echo $sRetweetClassSelector; ?>">
        <?php if ( $aArgs['avatar_size'] > 0  && $aArgs['visibilities']['avatar'] ) : 
            $sAvatarURL = getTwitterProfileImageURLBySize( $bIsSSL ? $aTweet['user']['profile_image_url_https'] : $aTweet['user']['profile_image_url'], $aArgs['avatar_size'] );
            $sAvatarURL = esc_url( $sAvatarURL );
            $sAvatarURLSubstitute = getTwitterProfileImageURLBySize( $bIsSSL ? $aTweet['user']['profile_image_url_https'] : $aTweet['user']['profile_image_url'], 100 );
            $sAvatarURLSubstitute = esc_url( $sAvatarURLSubstitute );
        ?>
        <div class='fetch-tweets-profile-image' style="max-width:<?php echo $aArgs['avatar_size'];?>px; float:<?php echo $aArgs['avatar_position']; ?>; clear:<?php echo $aArgs['avatar_position']; ?>;">
            <a href='<?php echo esc_url( "https://twitter.com/" . $aTweet['user']['screen_name'] ); ?>' target='_blank'>
                <img src='<?php echo $sAvatarURL; ?>' style='max-width:<?php echo $aArgs['avatar_size'];?>px; border-radius: 5px;' alt='<?php echo esc_attr( sprintf( __( 'The profile image of %1$s', 'fetch-tweets' ), $aTweet['user']['screen_name'] ) ); ?>' onError='this.onerror=null;this.src="<?php echo $sAvatarURLSubstitute; ?>";' />
            </a>
        </div><!-- fetch-tweets-profile-image -->
        <?php endif; ?>
        <div class='fetch-tweets-main' style='<?php echo $sMarginForImage; ?>;'>
            <div class='fetch-tweets-heading'>
            
               <?php if ( $aArgs['visibilities']['follow_button'] ) : ?>
                <div class='fetch-tweets-follow-button'>
                    <a href="<?php echo esc_url( 'https://twitter.com/' . $aTweet['user']['screen_name'] ); ?>" class="twitter-follow-button" target="_blank" data-lang="<?php echo esc_attr( $aTweet['user']['lang'] ); ?>" data-show-count="<?php echo esc_attr( $aArgs['follow_button_count'] ); ?>" data-show-screen-name="<?php echo esc_attr( $aArgs['follow_button_screen_name'] ); ?>">
                        <?php echo __( 'Follow', 'fetch-tweets' ) . '@' . $aTweet['user']['screen_name']; ?>
                    </a>
                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                </div>        
                <?php endif; ?>            
            
                <?php if ( $aArgs['visibilities']['user_name'] ) : ?>
                <span class='fetch-tweets-user-name'>
                    <strong>
                        <a href='<?php echo esc_url( "https://twitter.com/" . $aTweet['user']['screen_name'] ); ?>' target='_blank'>
                            <?php echo $aTweet['user']['name']; ?>
                        </a>
                    </strong>
                </span>
                <?php endif; ?>
                
                <?php if ( $aArgs['visibilities']['time'] ) : ?>
                <span class='fetch-tweets-tweet-created-at'>
                    <a href='<?php echo esc_url( "https://twitter.com/" . $aTweet['user']['screen_name'] . "/status/" . $aTweet['id_str'] ); ?>' target='_blank'>
                        <?php echo $_oUtil->getLegibleTimeDifference( $aTweet[ 'created_at' ] ) . ' ' . __( 'ago', 'fetch-tweets' ); ?>
                    </a>            
                </span>
                <?php endif; ?>
                
            </div><!-- fetch-tweets-heading -->
            <div class='fetch-tweets-body'>
                <p class='fetch-tweets-text'><?php echo $aTweet['text']; ?>
                    <?php if ( isset( $_aDetail['retweeted_status']['text'] ) ) : ?>
                    <span class='fetch-tweets-retweet-credit'><?php _e( 'Retweeted by', 'fetch-tweets' ) . ' '; ?><a href='<?php echo esc_url( "https://twitter.com/" . $_aDetail['user']['screen_name'] ); ?>' target='_blank'>
                            <?php echo $_aDetail['user']['name']; ?>
                        </a>
                    </span>
                    <?php endif; ?>
                </p><!-- fetch-tweets-text -->
                <?php if ( isset( $_aDetail['_media'] ) ) : ?>
                    <?php echo $_aDetail['_media']; ?>
                <?php endif; ?>
                <?php if ( $aArgs['intent_buttons'] ) : ?>
                    <?php if ( $aArgs['intent_button_script'] ) : ?>
                    <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
                    <?php endif; ?>
                    <ul class='fetch-tweets-intent-buttons'>
                        <li class='fetch-tweets-intent-reply'>
                            <a href='<?php echo esc_url( "https://twitter.com/intent/tweet?in_reply_to=" . $_aDetail['id_str'] ); ?>' rel='nofollow' target='_blank' title='<?php echo esc_attr( __( 'Reply', 'fetch-tweets' ) ); ?>'>
                                <?php if ( 1 == $aArgs['intent_buttons'] || 2 == $aArgs['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-intent-icon' style='background-image: url("<?php echo $sURLReplyuButton; ?>");' ></span>
                                <?php endif; ?>
                                <?php if ( 1 == $aArgs['intent_buttons'] || 3 == $aArgs['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-intent-buttons-text'>
                                    <?php _e( 'Reply', 'fetch-tweets' ); ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class='fetch-tweets-intent-retweet'>
                            <a href='<?php echo esc_url( "https://twitter.com/intent/retweet?tweet_id=" . $_aDetail['id_str'] ); ?>' rel='nofollow' target='_blank' title='<?php echo esc_attr( __( 'Retweet', 'fetch-tweets' ) ); ?>'>
                                <?php if ( 1 == $aArgs['intent_buttons'] || 2 == $aArgs['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-intent-icon' style='background-image: url("<?php echo $sURLRetweetButton; ?>");' ></span>
                                <?php endif; ?>
                                <?php if ( 1 == $aArgs['intent_buttons'] || 3 == $aArgs['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-intent-buttons-text'>
                                    <?php _e( 'Retweet', 'fetch-tweets' ); ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class='fetch-tweets-intent-favorite'>
                            <a href='<?php echo esc_url( "https://twitter.com/intent/favorite?tweet_id=" . $_aDetail['id_str'] ); ?>' rel='nofollow' target='_blank' title='<?php echo esc_attr( __( 'Favorite', 'fetch-tweets' ) ); ?>'>
                                <?php if ( 1 == $aArgs['intent_buttons'] || 2 == $aArgs['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-intent-icon' style='background-image: url("<?php echo $sURLFavoriteButton; ?>");' ></span>
                                <?php endif; ?>
                                <?php if ( 1 == $aArgs['intent_buttons'] || 3 == $aArgs['intent_buttons'] ) : ?>
                                <span class='fetch-tweets-intent-buttons-text'>
                                    <?php _e( 'Favorite', 'fetch-tweets' ); ?>
                                </span>
                                <?php endif; ?>
                            </a>
                        </li>        
                    </ul><!-- fetch-tweets-intent-buttons -->
                <?php endif; ?>
            </div><!-- fetch-tweets-body -->
        </div><!-- fetch-tweets-main -->
    </div><!-- fetch-tweets-item -->
    <?php endforeach; ?>    
</div><!-- fetch-tweets -->