<?php
// Theme support options
require_once(get_template_directory().'/assets/functions/theme-support.php');

// WP Head and other cleanup functions
require_once(get_template_directory().'/assets/functions/cleanup.php');

// Register scripts and stylesheets
require_once(get_template_directory().'/assets/functions/enqueue-scripts.php');

// Register custom menus and menu walkers
require_once(get_template_directory().'/assets/functions/menu.php');

// Register sidebars/widget areas
require_once(get_template_directory().'/assets/functions/sidebar.php');

// Makes WordPress comments suck less
require_once(get_template_directory().'/assets/functions/comments.php');

// Replace 'older/newer' post links with numbered navigation
require_once(get_template_directory().'/assets/functions/page-navi.php');

// Adds support for multiple languages
require_once(get_template_directory().'/assets/translation/translation.php');


// Remove 4.2 Emoji Support
// require_once(get_template_directory().'/assets/functions/disable-emoji.php');

// Adds site styles to the WordPress editor
require_once(get_template_directory().'/assets/functions/editor-styles.php');

// Related post function - no need to rely on plugins
// require_once(get_template_directory().'/assets/functions/related-posts.php');

// Use this as a template for custom post types
// require_once(get_template_directory().'/assets/functions/custom-post-type.php');

// Customize the WordPress login menu
require_once(get_template_directory().'/assets/functions/login.php');

// Customize the WordPress admin
// require_once(get_template_directory().'/assets/functions/admin.php');


// Change the default length of excerpts to be shorter
function custom_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );


// Add markdown support to custom post types (projects and series)
add_action( 'init', 'init_project_markdown_support' );
function init_project_markdown_support(){
	add_post_type_support( 'projects', 'wpcom-markdown' );
}
add_action( 'init', 'init_series_markdown_support' );
function init_series_markdown_support(){
	add_post_type_support( 'series', 'wpcom-markdown' );
}


// Change default query to include custom page types
add_action( 'pre_get_posts', function( $query )
{
		if(	!is_admin() // Only target front end queries
				&& $query->is_main_query() // Only target the main query
				&& !$query->is_post_type_archive([ 'projects', 'series' ]) // Don't apply modified query to CPT archives
				&& is_archive() // Restrict custom query to only archive pages
		 ){
			 $query->set( 'post_type', [ 'post', 'projects', 'series' ] );
		 }
});

/* Series Advanced Custom Field Relationship query
only make available posts / projects owned by the current user */
function series_relationship_query( $args, $field, $post_id ){

	$current_user = wp_get_current_user();
	// use user_login to get posts assigned via Co Author plugin
	$args[ 'author_name' ] = $current_user->user_login;

	return $args;
}
add_filter('acf/fields/relationship/query/name=series_parts', 'series_relationship_query', 10, 3);

function profile_relationship_query( $args, $field, $post_id ){

	$current_user = wp_get_current_user();
	// use user_login to get posts assigned via Co Author plugin
	$args[ 'author_name' ] = $current_user->user_login;
	// only make available projects which have a status of published
	$args[ 'post_status' ] = 'publish';

	return $args;
}
add_filter('acf/fields/relationship/query/name=featured_projects', 'profile_relationship_query', 10, 3);
add_filter('acf/fields/relationship/query/name=featured_series', 'profile_relationship_query', 10, 3);
add_filter('acf/fields/relationship/query/name=featured_posts', 'profile_relationship_query', 10, 3);


/* Equalised thumbnails, looped outside of The Loop for
ACF Relaitonship fields
@param string $field - field to pull from admin UI
@param boolean $user_field - whether or not this is a user field
@param int $grid_columns - number of columns for thumbnail grid */
function loop_custom_grid( $field, $user_field, $grid_columns ){
	global $post;
	// assign posts to the return from the ACF Relationship field type
	if( $user_field ){
		$posts = get_field( $field, 'user_'.$post->post_author );
	}
	else{
		$posts = get_field( $field );
	}

	if( $posts ){
		// Need custom index tracker since this doesn't work
		// work directly with wp_query->current_index
		// process below based on loop-archive-grid.php
		$current_index = 0;

		foreach ($posts as $post){

			// Check for the start of new row
			if( 0 === ( $current_index  ) % $grid_columns ){
				echo '<div class="row archive-grid" data-equalizer>';
			}

			setup_postdata($post);

			get_template_part( 'parts/loop', 'custom-grid' );

			// If the next post exceeds the grid_columns or at the end of the posts, close off the row
			if( 0 === ( $current_index + 1 ) % $grid_columns
				||  ( $current_index + 1 ) ===  $grid_columns ){
						echo '</div>';
				}

			$current_index++;
		}
		wp_reset_postdata();
	}
}


/* Courses custom taxonomy registration
majority of rules outputed via CPT UI with customisations for role permissions
interfaces with ACF 'Courses' Taxonomy field */
function register_courses_taxonomy() {

	$labels = array(
		"name" => __( "Courses", "" ),
		"singular_name" => __( "Course", "" ),
		"menu_name" => __( "Courses", "" ),
	);

	$args = array(
		"label" => __( "Courses", "" ),
		"labels" => $labels,
		"public" => false, //set to false to prevent default search field appearing
		"publicly_queryable" => true, //set to true to enable search access
		// Assign custom tax capabilties per role permissions
		'capabilities' => array(
      'manage_terms'=> 'manage_categories',
      'edit_terms'=> 'manage_categories',
      'delete_terms'=> 'manage_categories',
      'assign_terms' => 'edit_posts'
    ),
		"hierarchical" => false,
		"label" => "Courses",
		"show_ui" => false, //need to have this also set to false to prevent showing in UI
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'courses', 'with_front' => true,  'hierarchical' => true, ),
		"show_admin_column" => false,
		"show_in_rest" => false,
		"rest_base" => "",
		"show_in_quick_edit" => false,
	);
	register_taxonomy( "courses", array( "post", "projects", "series" ), $args );
}
add_action( 'init', 'register_courses_taxonomy' );


/* Restrict file types able to uploaded to the media manager
to only image files */
function allowed_media_upload_mimetypes( $mimes ){
	$allowed_mimes = array(
	    'jpg|jpeg|jpe' => 'image/jpeg',
	    'gif' => 'image/gif',
	    'png' => 'image/png',
	    'bmp' => 'image/bmp',
	    'tif|tiff' => 'image/tiff'
	);
	return $allowed_mimes;
}
add_filter( 'upload_mimes', 'allowed_media_upload_mimetypes' );

/* Overwrite media uploads file size cap to 5MB
for users other than admin */
function media_upload_filesize_cap( $size ){
	// Check user permissions (!=admin)
	if( !current_user_can( 'manage_options' ) ){
		// Size param needs to be in bytes
		// i.e. 5,242,880 bytes binary = 5MB
		$size = 1024 * 1024 * 5;
	}
	return $size;
}
add_filter( 'upload_size_limit', 'media_upload_filesize_cap' );


/* Remove Author role capability to publish their own posts
need to submit for review by Editor instead */
function remove_author_publish_cap(){
	// access author class instance
	$author = get_role( 'author' );
	// set publish_post capability to false
	$author->add_cap( 'publish_posts', false );
}
add_action( 'admin_init', 'remove_author_publish_cap' );


/* Remove 'Personal Options' section from user profile admin
i.e. visual editor, colour scheme, keyboard shortcuts, toolbar, language */
if ( ! function_exists( 'cor_remove_personal_options' ) ) {
	remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

	//Removes the leftover 'Visual Editor', 'Keyboard Shortcuts' and 'Toolbar' options.
	add_action( 'admin_head', function () {

			ob_start( function( $subject ) {

					$subject = preg_replace( '#<h[0-9]>'.__("Personal Options").'</h[0-9]>.+?/table>#s', '', $subject, 1 );
					return $subject;
			});
	});

	add_action( 'admin_footer', function(){

			ob_end_flush();
	});
}
add_action( 'admin_head-user-edit.php', 'cor_profile_subject_start' );
add_action( 'admin_footer-user-edit.php', 'cor_profile_subject_end' );


/* Restrict admin/dashboard menus (both top and side)
for simpler UI and efficient UX */
function dashboard_restrict_sidemenu(){
	// Check user permissions (restrictions not applied to admin)
	if( !current_user_can( 'manage_options' ) ){
		remove_menu_page( 'upload.php' ); //media option
		// Note: editors (lecturers/tutors lose ability for editing pages here)
		remove_menu_page( 'edit.php?post_type=page' ); //pages option
		remove_menu_page( 'edit-comments.php' ); //comments option
		remove_menu_page( 'tools.php' ); //tools option
	}
}
add_action( 'admin_menu', 'dashboard_restrict_sidemenu' );

function dashboard_restrict_topmenu( $wp_admin_bar ){
	// Check user permissions (restrictions not applied to admin)
	if( !current_user_can( 'manage_options' ) ){
		$wp_admin_bar->remove_node( 'comments' ); //comments option
		$wp_admin_bar->remove_node( 'wp-logo' ); //wp logo option
	}
}
add_action( 'admin_bar_menu', 'dashboard_restrict_topmenu', 999);


/* Enqueue main style sheet for access in admin pages */
function override_default_admin_styles(){
	wp_register_style( 'site-css', get_template_directory_uri() . '/assets/css/style.css', array(), time(), 'all' );
  wp_enqueue_style( 'site-css' );
}
add_action( 'admin_enqueue_scripts', 'override_default_admin_styles');


/* Override default icon set with branding where needed */
function override_admin_menu_icons() {
	$branding_asset_dir = get_template_directory_uri() . '/assets/images/branding-assets/';
	echo '<style>
   	.menu-icon-post div.wp-menu-image:before {
   		background-image: url( ' . $branding_asset_dir . 'dwp_bloglogo_bg.svg);
     }
		.menu-icon-projects div.wp-menu-image:before {
		 	background-image: url( ' . $branding_asset_dir . 'dwp_projectlogo_bg.svg);
			}
		.menu-icon-series div.wp-menu-image:before {
			background-image: url( ' . $branding_asset_dir . 'dwp_serieslogo_bg.svg);
		 }

		 #wpadminbar #wp-admin-bar-site-name > .ab-item:before {
 			background-image: url( ' . $branding_asset_dir . 'dwp_mainlogo.svg)!important;
 		 }
     </style>';
}
add_action( 'admin_head', 'override_admin_menu_icons', 999 );
