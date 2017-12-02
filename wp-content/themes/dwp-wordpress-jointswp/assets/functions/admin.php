<?php
// This file handles the admin area and functions - You can use this file to make changes to the dashboard.

/************* DASHBOARD WIDGETS *****************/
// Disable default dashboard widgets
function remove_dashboard_widgets() {
	remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );   // Right Now
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );   // Activity box
	remove_action('welcome_panel', 'wp_welcome_panel'); //Welcome panel
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' ); // Recent Comments
	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );  // Incoming Links
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );   // Plugins
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );  // Quick Press
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );  // Recent Drafts
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );   // WordPress blog
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );   // Other WordPress News
	// use 'dashboard-network' as the second parameter to remove widgets from a network dashboard.
}
add_action( 'wp_dashboard_setup', 'remove_dashboard_widgets' );
/*
For more information on creating Dashboard Widgets, view:
http://digwp.com/2010/10/customize-wordpress-dashboard/
*/

/* Returns a list of recent posts based on type and status */
function get_dashboard_recentposts($post_type, $post_status){
	$args = array(
		'author' => get_the_author_meta('ID'), //TODO: account for multi author
		'post_type' => $post_type,
		'numberposts' => 3,
		'post_status' => $post_status
	);
	$blogposts = get_posts($args);

	foreach ($blogposts as $blogpost) {
		echo '<li><a href="'. get_the_permalink($blogpost->ID).'">'.$blogpost->post_title.'</a></li>';
	}
	echo '</ul>';
}

function dashboard_blogposts_widget(){

	// Blog Section Logo
	echo "<div class='dashboard-post-widget'>
					<img class='dashboard-post-widget-headerimg' src='".get_template_directory_uri()."/assets/images/branding-assets/dwp_bloglogo_bg.svg'>";

	echo '<div class="dashboard-post-recentsection">
	<h4>Recently Drafted</h4><ul>';
		get_dashboard_recentposts('post', 'draft');
	echo '</div>';

	echo '<div class="dashboard-post-recentsection">
	<h4>Recently Published</h4><ul>';
		get_dashboard_recentposts('post', 'publish');
	echo '</div>';

	echo "</div>";
}

function dashboard_project_widget(){

	// Blog Section Logo
	echo "<div class='dashboard-post-widget'>
					<img class='dashboard-post-widget-headerimg' src='".get_template_directory_uri()."/assets/images/branding-assets/dwp_projectlogo_bg.svg'>";

	echo '<div class="dashboard-post-recentsection">
	<h4>Recently Drafted</h4><ul>';
		get_dashboard_recentposts('projects', 'draft');
	echo '</div>';

	echo '<div class="dashboard-post-recentsection">
	<h4>Recently Submitted</h4><ul>';
		get_dashboard_recentposts('projects', 'pending');
	echo '</div>';

	echo '<div class="dashboard-post-recentsection">
	<h4>Recently Published</h4><ul>';
		get_dashboard_recentposts('projects', 'publish');
	echo '</div>';

	echo "</div>";
}

function dashboard_series_widget(){

	// Blog Section Logo
	echo "<div class='dashboard-post-widget'>
					<img class='dashboard-post-widget-headerimg' src='".get_template_directory_uri()."/assets/images/branding-assets/dwp_serieslogo_bg.svg'>";

	echo '<div class="dashboard-post-recentsection">
	<h4>Recently Drafted</h4><ul>';
		get_dashboard_recentposts('series', 'draft');
	echo '</div>';

	echo '<div class="dashboard-post-recentsection">
	<h4>Recently Submitted</h4><ul>';
		get_dashboard_recentposts('series', 'pending');
	echo '</div>';

	echo '<div class="dashboard-post-recentsection">
	<h4>Recently Published</h4><ul>';
		get_dashboard_recentposts('series', 'publish');
	echo '</div>';

	echo "</div>";
}

// Calling all custom dashboard widgets
function joints_custom_dashboard_widgets() {
	/*
	Be sure to drop any other created Dashboard Widgets
	in this function and they will all load.
	*/
	wp_add_dashboard_widget('dashboard_blogposts_widget', __('Blog Posts', 'jointswp'), 'dashboard_blogposts_widget');

	wp_add_dashboard_widget('dashboard_project_widget', __('Projects', 'jointswp'), 'dashboard_project_widget');

	wp_add_dashboard_widget('dashboard_series_widget', __('Series', 'jointswp'), 'dashboard_series_widget');
}
// removing the dashboard widgets
// adding any custom widgets
add_action('wp_dashboard_setup', 'joints_custom_dashboard_widgets');


/************* CUSTOMIZE ADMIN *******************/
// Custom Backend Footer
function joints_custom_admin_footer() {
	_e('<span id="footer-thankyou">Developed by <a href="#" target="_blank">Your Site Name</a></span>.', 'jointswp');
}

// adding it to the admin area
add_filter('admin_footer_text', 'joints_custom_admin_footer');



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

	// User to setup dashboard custom elements using jQuery
	wp_register_script( 'dashboard_custom_setup',  get_template_directory_uri() . '/assets/js/scripts/admin_customsetup.js', array(), time(), false);
	wp_enqueue_script('dashboard_custom_setup');

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

/* Add logo to dashboard splash header */
function add_dashboard_header_logo(){
	$branding_asset_dir = get_template_directory_uri() . '/assets/images/branding-assets/';
	echo '<style>
   	#dashboard_splashHeaderImg {
   		background-image: url( ' . $branding_asset_dir . 'dwp_mainlogo.svg);
     }
		 </style>';
}
add_action( 'admin_head', 'add_dashboard_header_logo');
