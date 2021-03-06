<!-- Note: contrary to it's name, this template is used for the
Blog archive page (as per WP template architecture), not the front-page
(which is named accordingly) -->

<?php get_header(); ?>

	<div id="content">

		<header class="archive-header medium-10 large-10 small-centered columns" >
			<img src="<?php echo get_template_directory_uri() . '/assets/images/branding-assets/dwp_bloglogo_bg.svg'?>" alt="blog page">
			<h3 class="page-title"><?php esc_html_e( wp_title( '', true, ''), 'jointswp');?></h3>
			<?php esc_html_e( the_archive_description('<div class="taxonomy-description">', '</div>'), 'jointswp');?>
			<p> <?php the_excerpt(); ?></p>
		</header>

		<?php get_search_form(); ?>

		<div id="inner-content">

		    <main id="main" class="archive-thumb-container" role="main">

					<?php
					$first_post = true;
					if (have_posts()) : while (have_posts()) : the_post(); ?>

					<?php if ($first_post){
						// Apply splash thumbnail template if first post
						get_template_part( 'parts/loop', 'archive-splash' );
						$first_post = false;
					}else{
						// For test, apply general thumbnail template
						get_template_part( 'parts/loop', 'archive-grid' );
					} ?>

				<?php endwhile; ?>

					<?php joints_page_navi(); ?>

				<?php else : ?>

					<?php get_template_part( 'parts/content', 'missing' ); ?>

				<?php endif; ?>

			</main>
	  </div>
	</div>
	<!-- <p style="text-align: center;">Template: home.php</p> -->

<?php get_footer(); ?>
