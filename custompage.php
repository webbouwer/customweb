<?php
/**
 * Template Name: Custom Page
 * Theme main index file
 */
require_once('functions.php');

// the current page/post data
global $post;
// determine header image
$header_image = get_header_image();

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <?php
    if ( ! isset( $content_width ) ) $content_width = 360; // mobile first
    echo
    //'<link rel="canonical" href="'.home_url(add_query_arg(array(),$wp->request)).'">'
	'<link rel="pingback" href="'.get_bloginfo( 'pingback_url' ).'" />'
	.'<link rel="shortcut icon" href="images/favicon.ico" />'
	// tell devices wich screen size to use by default
	.'<meta name="viewport" content="initial-scale=1.0, width=device-width" />'
	.'<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">';
    // more info for og api's
    echo '<meta property="og:title" content="' . get_the_title() . '"/>'
        .'<meta property="og:type" content="website"/>'
		.'<meta property="og:url" content="' . get_permalink() . '"/>'
		.'<meta property="og:site_name" content="'.esc_attr( get_bloginfo( 'name', 'display' ) ).'"/>'
		.'<meta property="og:description" content="'.get_bloginfo( 'description' ).'"/>';
        if( !has_post_thumbnail( $post->ID )) { //the post does not have featured image, use a default image
            if( !empty($header_image) ){
                $default_image = get_theme_mod( 'wp_main_theme_identity_logo', get_header_image() );
                echo '<meta property="og:image" content="' . $default_image . '"/>';
            }
		}else{
			$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
			echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
		}
    // include wp head
    wp_head();

    // https://css-tricks.com/perfect-full-page-background-image/
    $bgposition = get_theme_mod('background_position', 'bottom center');
    $bgattacht = get_theme_mod('background_attachment', 'fixed');
    $bgrepeat = get_theme_mod('background_repeat', 'no-repeat');
    $bgsize = get_theme_mod('background_size', 'cover');
    $headerbgstyle = ' style="background-image:url('.esc_url( get_background_image() ).');background-position:'.$bgposition.';background-attachment:'.$bgattacht.';background-size:'.$bgsize.';background-repeat:'.$bgrepeat.';"';

    $stylesheet = get_template_directory_uri().'/custom.css';
    echo '<link rel="stylesheet" id="wp-theme-custom-style"  href="'.$stylesheet.'" type="text/css" media="all" />';

echo '</head>';
echo '<body '.$headerbgstyle.' '; body_class(); echo '>';
?>
    <div id="pagecontainer" class="site">
        <div id="topcontent">
            <div class="outerspace">
                Custom page theme<hr />
            <?php
            wp_main_theme_toplogo_html();
            wp_main_theme_menu_html( 'top' , true  );
            wp_main_theme_widgetarea_html( 'widgets-top-sidebar' );
            ?>
            </div>
        </div>
        <div id="maincontent">
            <div class="outerspace">

                <?php
                if( is_customize_preview() ){
                    echo '<div id="area-main-menu" class="customizer-placeholder">Main menu</div>';
                }
                wp_main_theme_menu_html( array( 'main' ) );
                ?>

                <div id="maincontentbar">
                    <?php
                    if( is_customize_preview() ){
                        echo '<div id="area-page-main-content" class="customizer-placeholder">Page main content</div>';
                    }
                    wp_main_theme_loop_html();
                    ?>
                </div>
                <div id="mainsidebar">
                    <?php
                    if( is_customize_preview() ){
                        echo '<div id="area-side-menu" class="customizer-placeholder">Side menu</div>';
                    }
                    wp_main_theme_menu_html( 'side' );

                    wp_main_theme_widgetarea_html( 'sidebar' );

                    ?>
                </div>
                <div class="clr"></div>

            </div>
        </div>
        <div id="bottomcontent">
            <div class="outerspace">
            <?php
            if( is_customize_preview() ){
                echo '<div id="area-bottom-menu" class="customizer-placeholder">Bottom menu</div>';
            }
            wp_main_theme_menu_html( 'bottom' );

            wp_main_theme_widgetarea_html( 'widgets-bottom-sidebar' );
            ?>
            </div>
        </div>
    </div>
<?php
    wp_footer();
    echo '</body></html>';
?>


