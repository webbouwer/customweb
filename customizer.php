<?php
/**
 * Theme main customizer functions
 */

function wp_main_theme_register_theme_customizer( $wp_customize ) {

	$wp_customize->remove_control('display_header_text');
	//$wp_customize->remove_section('title_tagline'); // remove default title / site-identity
	$wp_customize->remove_control('header_textcolor'); // remove default colors
	$wp_customize->remove_control('background_color'); // remove default colors
	$wp_customize->remove_panel('colors'); // remove default colors

    $wp_customize->add_setting( 'wp_main_theme_identity_logo', array(
		'sanitize_callback' => 'wp_main_theme_sanitize_default',
    ));

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'wp_main_theme_identity_logo', array(
        'label'    => __( 'Site Logo Image', 'resource' ),
        'section'  => 'title_tagline',
        'settings' => 'wp_main_theme_identity_logo',
        'description' => __( 'Upload or select a medium sized image to use as site logo (replacing the site-title text on top).', 'resource' ),
        'priority' => 10,
    )));


}
add_action( 'customize_register', 'wp_main_theme_register_theme_customizer' );

// default sanitize functions
function wp_main_theme_sanitize_default($obj){
    	return $obj; //.. global sanitizer
}
function wp_main_theme_sanitize_array( $values ) {
    $multi_values = !is_array( $values ) ? explode( ',', $values ) : $values;
    return !empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : array();
}
?>
