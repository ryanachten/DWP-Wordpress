<?php
function site_scripts() {
  global $wp_styles; // Call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way

    // Load What-Input files in footer
    wp_enqueue_script( 'what-input', get_template_directory_uri() . '/vendor/what-input/dist/what-input.min.js', array(), '', true );

    // Adding Foundation scripts file in the footer
    wp_enqueue_script( 'foundation-js', get_template_directory_uri() . '/vendor/foundation-sites/dist/js/foundation.min.js', array( 'jquery' ), '6.3.1', true );

    // Adding scripts file in the footer
    wp_enqueue_script( 'site-js', get_template_directory_uri() . '/assets/js/scripts.js', array( 'jquery' ), '', true );

     // Register Motion-UI
    wp_enqueue_style( 'motion-ui-css', get_template_directory_uri() . '/vendor/motion-ui/dist/motion-ui.min.css', array(), '', 'all' );

	// Select which grid system you want to use (Foundation Grid by default)
    wp_enqueue_style( 'foundation-css', get_template_directory_uri() . '/vendor/foundation-sites/dist/css/foundation.min.css', array(), '', 'all' );
     //wp_enqueue_style( 'foundation-css', get_template_directory_uri() . '/vendor/foundation-sites/dist/foundation-flex.min.css', array(), '', 'all' );

    //  Load Google Fonts
    wp_enqueue_style('google-fonts', "https://fonts.googleapis.com/css?family=Exo+2|Roboto|Roboto+Slab|Signika", array(), time(), 'all');

    // Register main stylesheet
    wp_enqueue_style( 'site-css', get_template_directory_uri() . '/assets/css/style.css', array('google-fonts'), time(), 'all' );

    // Add Foundation icon set
    wp_enqueue_style( 'foundation-icons-css', get_template_directory_uri() . '/assets/css/foundation-icons/foundation-icons.css', array(), 3, 'all' );


    // Comment reply script for threaded comments
    if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
      wp_enqueue_script( 'comment-reply' );
    }
}
add_action('wp_enqueue_scripts', 'site_scripts', 999);
