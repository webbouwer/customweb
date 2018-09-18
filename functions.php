<?php
/**
 * theme main functions file
 */

// register functions
require_once( get_template_directory() . '/customizer.php'); // customizer functions


// register options
function theme_post_thumbnails() {
	add_theme_support( 'custom-background' );
    add_theme_support( 'custom-header' );
    add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'theme_post_thumbnails' );

// register menu's
function basic_setup_register_menus() {
	register_nav_menus(
		array(
		'top' => __( 'Top menu' , 'resource' ),
		'main' => __( 'Main menu' , 'resource' ),
		'side' => __( 'Side menu' , 'resource' ),
		'bottom' => __( 'Bottom menu' , 'resource' )
		)
	);
}
add_action( 'init', 'basic_setup_register_menus' );

// activate the Links Manager:: add_filter( 'pre_option_link_manager_enabled', '__return_true' );

// register widgets
function basic_setup_widgets_init() {
	if (function_exists('register_sidebar')) {
        // Not using the default wordpress widget, keep the widget slot for management (repositioning options)
		register_sidebar(array(
			'name' => 'Widgets Header',
			'id'   => 'widgets-header',
			'description'   => 'This is a standard wordpress widgetized area.',
			'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widgetpadding">',
			'after_widget'  => '<div class="clr"></div></div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
		));
		// Topcontent main widgets
		register_sidebar(array(
			'name' => 'Widgets Top',
			'id'   => 'widgets-top-sidebar',
			'description'   => 'This the widgetized area in the top sidebar.',
			'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widgetpadding">',
			'after_widget'  => '<div class="clr"></div></div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
		));
		// Maincontent sidebar widgets
		register_sidebar(array(
			'name' => 'Widgets Sidebar',
			'id'   => 'sidebar',
			'description'   => 'This is the standard wordpress widgetized sidebar area.',
			'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widgetpadding">',
			'after_widget'  => '<div class="clr"></div></div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
		));
		// Bottomcontent main widgets
		register_sidebar(array(
			'name' => 'Widgets Bottom',
			'id'   => 'widgets-bottom-sidebar',
			'description'   => 'This the widgetized area in the bottom sidebar.',
			'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widgetpadding">',
			'after_widget'  => '<div class="clr"></div></div></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>'
		));
	}
}
add_action( 'widgets_init', 'basic_setup_widgets_init' );


// register global variables (options/customizer)
$wp_global_data = array();
// all post data global
function wp_main_theme_get_postdata(){
        $args = array(
            //'tag'               => json_encode($this->tagfilter),
            //'category_name'     => json_encode($this->catfilter),
            //'post_type'         => 'post', // 'any',  = incl pages
            //'post__not_in'      => $this->loadedID,
            'post_status'       => 'publish',
            'orderby'           => 'date',
            'order'             => 'DESC',      // 'DESC', 'ASC' or 'RAND'
            'posts_per_page'  => -1,
            //'posts_offset'      => $ppload,
            //'suppress_filters'  => false,
        );
        $query = new WP_Query( $args );
        $response = array();
        $count = array();
        if ( $query->have_posts() ) :
            while ( $query->have_posts() ) : $query->the_post();
                // post text
                $excerpt_length = 120; // words
                $post = get_post($post->id);
                $fulltext = $post->post_content;//  str_replace( '<!--more-->', '',);
                $content = apply_filters('the_content', $fulltext );
                $excerpt = truncate( $content, $excerpt_length, '', false, true );  // get_the_excerpt()
                $response[] = array(
                    'id' => get_the_ID(),
                    'link' => get_the_permalink(),
                    'title' => get_the_title(),
                    'image' => get_the_post_thumbnail(),
                    'excerpt' => $excerpt,
                    'content' => $content,
                    'cats' => wp_get_post_terms( get_the_ID(), 'category', array("fields" => "slugs")),
                    'tags' => wp_get_post_terms( get_the_ID(), 'post_tag', array("fields" => "slugs")),
                    'date' => get_the_date(),
                    'timestamp' => strtotime(get_the_date()),
                    'author' => get_the_author(),
                    'custom_field_keys' => get_post_custom_keys()
                );
            endwhile;
        else:
           $response[0] = 'No posts found';
        endif;
        wp_reset_query();
        ob_clean();
        //wp_die();
        return $response;
}
function wp_main_theme_get_customizer(){
    return json_encode( get_theme_mods() );
}
function wp_main_theme_get_all_posts(){
    return json_encode( wp_main_theme_get_postdata() );
}
function wp_main_theme_get_all_tags(){
    return json_encode( get_terms( 'post_tag' ) );
}
function wp_main_theme_get_all_categories(){
    return json_encode( get_categories( array("type"=>"post") ) );
}
// data for global js
$wp_global_data['customdata']   = wp_main_theme_get_customizer();
$wp_global_data['postdata']     = wp_main_theme_get_all_posts();
$wp_global_data['tagdata']      = wp_main_theme_get_all_tags();
$wp_global_data['catdata']      = wp_main_theme_get_all_categories();

// register global customizer variables
function wp_main_theme_global_js() {
    // add jquery
    wp_enqueue_script("jquery"); // default wp jquery
    wp_register_script( 'custom_global_js', get_template_directory_uri().'/global.js', 99, '1.0', false); // register the script
    global $wp_global_data; // get global data var
	wp_localize_script( 'custom_global_js', 'site_data', $wp_global_data ); // localize the global data list for the script
    // localize the script with specific data.
    //$color_array = array( 'color1' => get_theme_mod('color1'), 'color2' => '#000099' );
    //wp_localize_script( 'custom_global_js', 'object_name', $color_array );
    // The script can be enqueued now or later.
    wp_enqueue_script( 'custom_global_js');
}
add_action('wp_enqueue_scripts', 'wp_main_theme_global_js');


// register style sheet
function wp_main_theme_stylesheet(){
    $stylesheet = get_template_directory_uri().'/style.css';
    echo '<link rel="stylesheet" id="wp-theme-main-style"  href="'.$stylesheet.'" type="text/css" media="all" />';
}
add_action( 'wp_head', 'wp_main_theme_stylesheet', 9999 );

// register style sheet function for editor
function onepiece_editor_styles() {
    add_editor_style( 'style.css' );
}
add_action( 'admin_init', 'onepiece_editor_styles' );



// theme html output toplogo (custom_logo) or site title home link
function wp_main_theme_toplogo_html(){

    if( is_customize_preview() ){
        echo '<div id="area-custom-image" class="customizer-placeholder">Logo image</div>';
    }

    if( get_theme_mod('wp_main_theme_identity_logo', '') != '' ){
        $custom_logo_url = get_theme_mod('wp_main_theme_identity_logo');
        $custom_logo_attr = array(
            'class'    => 'custom-logo',
            'itemprop' => 'logo',
        );
        echo sprintf( '<a href="%1$s" class="custom-logo-link image" rel="home" itemprop="url">%2$s</a>',
        esc_url( home_url( '/' ) ),
        '<img id="toplogo" src="'.$custom_logo_url.'" border="0" />'
        );
    }else if( get_theme_mod('custom_logo', '') != '' ){
        $custom_logo_id = get_theme_mod('custom_logo');
        $custom_logo_attr = array(
            'class'    => 'custom-logo',
            'itemprop' => 'logo',
        );
        echo sprintf( '<a href="%1$s" class="custom-logo-link image" rel="home" itemprop="url">%2$s</a>',
        esc_url( home_url( '/' ) ),
        wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr )
        );
    }else{
        echo sprintf( '<a href="%1$s" class="custom-logo-link text" rel="home" itemprop="url">%2$s</a>',
        esc_url( home_url( '/' ) ),
        esc_attr( get_bloginfo( 'name', 'display' ) ) //get_bloginfo( 'description' )
        );
    }
}


// theme html output menu's by name (str or array, default primary)
function wp_main_theme_menu_html( $menu, $primary = false ){

    if( $menu != '' || is_array( $menu ) ){
        $chk = 0;
        if( is_array( $menu ) ){

            // multi menu
            foreach( $menu as $nm ){
                if( has_nav_menu( $nm ) ){
                    echo '<div id="'.$nm.'menubox"><div id="'.$nm.'menu" class=""><nav><div class="innerpadding">';
                    wp_nav_menu( array( 'theme_location' => $nm ) );
                    echo '<div class="clr"></div></div></nav></div></div>';
                    $chk++;
                }
            }

        }else if( has_nav_menu( $menu ) ){

            // single menu
            echo '<div id="'.$menu.'menubox"><div id="'.$menu.'menu" class=""><nav><div class="innerpadding">';
            wp_nav_menu( array( 'theme_location' => $menu , 'menu_class' => 'nav-menu' ) );
            echo '<div class="clr"></div></div></nav></div></div>';
            $chk++;

        }

        if( $chk == 0 && $primary ){

            // default pages menu
            if( is_customize_preview() ){
            echo '<div id="area-default-menu" class="customizer-placeholder">Default menu</div>';
            }
            wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); // wp_page_menu();

        }

    }
}

// theme html output widget area's by type or default
function wp_main_theme_widgetarea_html( $id, $type = false ){


    if( is_customize_preview() ){
        echo '<div id="area-'.$id.'" class="customizer-placeholder">Area '.$id.'</div>';
    }
    if( isset($id) && $id != '' ){

        if( function_exists('dynamic_sidebar') && function_exists('is_sidebar_active') && is_sidebar_active( $id ) ){
            $class = 'widgetbox';
            if( isset($type) && $type != '' ){
                $class = 'widgetbox widget-'.$type;
                echo '<div id="'.$id.'" class="'.$class.'">';
            }else{
                echo '<div id="'.$id.'" class="'.$class.' columnbox colset'.is_sidebar_active( $id ).'">';
            }
            dynamic_sidebar( $id );
            echo '<div class="clr"></div></div>';
        }

    }
}

// theme html output widget area's by type or default
function wp_main_theme_loop_html(){
    if (have_posts()) :
		while (have_posts()) : the_post();

            echo '<div class="post-container">';

                // post title section
                $title_html = '<a href="'.get_the_permalink().'" target="_self" title="'.get_the_title().'">'.get_the_title().'</a>';

                echo '<div class="post-title">';
                if(is_page()){
                    echo '<h1>'.$title_html.'</h1>';
                }else if( is_single() ){
                    echo '<h1>'.$title_html.'</h1>';
                }else{
                    echo '<h2>'.$title_html.'</h2>';
                }
                echo '</div>';

                // post content section
                $excerpt_length = 24; // char count
                $post = get_post($post->id);
                $fulltext = $post->post_content;//  str_replace( '<!--more-->', '',);
                $content = apply_filters('the_content', $fulltext );
                $excerpt = truncate( $content, $excerpt_length, '', false, true );  // get_the_excerpt()

                if(is_page()){
                    echo '<div class="post-content">';
                    echo $content;
                    echo '</div>';
                }else if( is_single() ){
                    echo '<div class="post-content">';
                    echo $content;
                    echo '</div>';
                    previous_post_link('%link', __('previous', 'resource' ), TRUE);
                    next_post_link('%link', __('next', 'resource' ), TRUE);

                }else{
                    echo '<div class="post-content post-excerpt">';
                    echo $excerpt;
                    echo '</div>';
                }

            echo '</div>';

        endwhile;
    endif;
    wp_reset_query();
}


/**
 * Date display in tweet('time ago') format
 */
function wp_time_ago( $t ) {
	// https://codex.wordpress.org/Function_Reference/human_time_diff
	//get_the_time( 'U' )
	printf( _x( '%s ago', '%s = human-readable time difference', 'onepiece' ), human_time_diff( $t, current_time( 'timestamp' ) ) );

}

// image orient
function check_image_orientation($pid){
	$orient = 'square';
    $image = wp_get_attachment_image_src( get_post_thumbnail_id($pid), '');
    $image_w = $image[1];
    $image_h = $image[2];
			if ($image_w > $image_h) {
				$orient = 'landscape';
			}elseif ($image_w == $image_h) {
				$orient = 'square';
			}else {
				$orient = 'portrait';
			}
			return $orient;
}

// get categories
function get_categories_select(){
    $get_cats = get_categories();
    $results;
    $count = count($get_cats);
			for ($i=0; $i < $count; $i++) {
				if (isset($get_cats[$i]))
					$results[$get_cats[$i]->slug] = $get_cats[$i]->name;
				else
					$count++;
			}
    return $results;
}

// Category metabox Hierarchy
function onepiece_wp_terms_checklist_args( $args, $post_id ) {
   $args[ 'checked_ontop' ] = false;
   return $args;
}
add_filter( 'wp_terms_checklist_args', 'onepiece_wp_terms_checklist_args', 1, 2 );

// check active widgets
function is_sidebar_active( $sidebar_id ){
    $the_sidebars = wp_get_sidebars_widgets();
    if( !isset( $the_sidebars[$sidebar_id] ) )
        return false;
    else
        return count( $the_sidebars[$sidebar_id] );
}

// widget empty title content wrapper fix
function check_sidebar_params( $params ) {
    global $wp_registered_widgets;
    $settings_getter = $wp_registered_widgets[ $params[0]['widget_id'] ]['callback'][0];
    $settings = $settings_getter->get_settings();
    $settings = $settings[ $params[1]['number'] ];
    if ( $params[0][ 'after_widget' ] == '<div class="clr"></div></div>' && isset( $settings[ 'title' ] ) &&  empty( $settings[ 'title' ] ) ){
        $params[0][ 'before_widget' ] .= '<div class="widget-contentbox">';
    }
    return $params;
}
// Add widget param check for empty html correction
add_filter( 'dynamic_sidebar_params', 'check_sidebar_params' );

/**
* Truncates text.
*
* Cuts a string to the length of $length and replaces the last characters
* with the ending if the text is longer than length.
*
* @param string  $text String to truncate.
* @param integer $length Length of returned string, including ellipsis.
* @param string  $ending Ending to be appended to the trimmed string.
* @param boolean $exact If false, $text will not be cut mid-word
* @param boolean $considerHtml If true, HTML tags would be handled correctly
* @return string Trimmed string.
*/
function truncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {
    if ($considerHtml) {
        // if the plain text is shorter than the maximum length, return the whole text
        if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }
        // splits all html-tags to scanable lines
        preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags = array();
            $truncate = '';
        foreach ($lines as $line_matchings) {
            // if there is any html-tag in this line, handle it and add it (uncounted) to the output
            if (!empty($line_matchings[1])) {
                // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
                if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                    // do nothing
                // if tag is a closing tag (f.e. </b>)
                } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                    // delete tag from $open_tags list
                    $pos = array_search($tag_matchings[1], $open_tags);
                    if ($pos !== false) {
                        unset($open_tags[$pos]);
                    }
                // if tag is an opening tag (f.e. <b>)
                } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                    // add tag to the beginning of $open_tags list
                    array_unshift($open_tags, strtolower($tag_matchings[1]));
                }
                // add html-tag to $truncate'd text
                $truncate .= $line_matchings[1];
            }
            // calculate the length of the plain text part of the line; handle entities as one character
            $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
            if ($total_length+$content_length> $length) {
                // the number of characters which are left
                $left = $length - $total_length;
                $entities_length = 0;
                // search for html entities
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                    // calculate the real length of all entities in the legal range
                    foreach ($entities[0] as $entity) {
                        if ($entity[1]+1-$entities_length <= $left) {
                            $left--;
                            $entities_length += strlen($entity[0]);
                        } else {
                            // no more characters left
                            break;
                        }
                    }
                }
                $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
                // maximum lenght is reached, so get off the loop
                break;
            } else {
                $truncate .= $line_matchings[2];
                $total_length += $content_length;
            }
            // if the maximum length is reached, get off the loop
            if($total_length>= $length) {
                break;
            }
        }
    } else {
        if (strlen($text) <= $length) {
            return $text;
        } else {
            $truncate = substr($text, 0, $length - strlen($ending));
        }
    }
    // if the words shouldn't be cut in the middle...
    if (!$exact) {
        // ...search the last occurance of a space...
        $spacepos = strrpos($truncate, ' ');
        if (isset($spacepos)) {
            // ...and cut the text in this position
            $truncate = substr($truncate, 0, $spacepos);
        }
    }
    // add the defined ending to the text
    $truncate .= $ending;
    if($considerHtml) {
			$truncate .= '.. ';
        // close all unclosed html-tags
        foreach ($open_tags as $tag) {
            $truncate .= '</' . $tag . '>';
        }
    }
    return $truncate;
}



/***********************
* Remove unneeded code *
***********************/
/* Remove Emoji junk by Christine Cooper
 * Found on http://wordpress.stackexchange.com/questions/185577/disable-emojicons-introduced-with-wp-4-2
 */
function disable_wp_emojicons() {
  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' ); // filter to remove TinyMCE emojis
}
add_action( 'init', 'disable_wp_emojicons' );
function disable_emojicons_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}
/*
 * control (remove) gravatar
 */
function bp_remove_gravatar ($image, $params, $item_id, $avatar_dir, $css_id, $html_width, $html_height, $avatar_folder_url, $avatar_folder_dir) {
	$default = get_stylesheet_directory_uri() .'/images/avatar.png';
	if( $image && strpos( $image, "gravatar.com" ) ){
		return '<img src="' . $default . '" alt="avatar" class="avatar" ' . $html_width . $html_height . ' />';
	} else {
		return $image;
	}
}
add_filter('bp_core_fetch_avatar', 'bp_remove_gravatar', 1, 9 );
function remove_gravatar ($avatar, $id_or_email, $size, $default, $alt) {
	$default = get_stylesheet_directory_uri() .'/images/avatar.png';
	return "<img alt='{$alt}' src='{$default}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
}
add_filter('get_avatar', 'remove_gravatar', 1, 5);
function bp_remove_signup_gravatar ($image) {
	$default = get_stylesheet_directory_uri() .'/images/avatar.png';
	if( $image && strpos( $image, "gravatar.com" ) ){
		return '<img src="' . $default . '" alt="avatar" class="avatar" width="60" height="60" />';
	} else {
		return $image;
	}
}
add_filter('bp_get_signup_avatar', 'bp_remove_signup_gravatar', 1, 1 );

?>
