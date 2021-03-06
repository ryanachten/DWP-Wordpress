<?php get_header(); ?>

	<div id="content">

		<header class="author-header archive-header medium-10 large-10 small-centered columns row" >
			<?php echo get_avatar( $post->post_author ); ?>
			<h1 class="author-name">
				<?php esc_html_e( the_author_meta( 'display_name', $post->post_author ), 'jointswp' ); ?>
			</h1>
			<div class="author-social-container">
					<?php
						$author_web = esc_url( get_the_author_meta( 'url', $post->post_author ) );
						if( !empty($author_web) ): ?>
							<a class="author-social-icon" href="<?php
							the_author_meta( 'url', $post->post_author ); ?>">
								<i class="fi-web"></i>
							</a>
					<?php endif; ?>
			</div>
			<div class="taxonomy-description">
				<?php esc_html_e( the_author_meta( 'description', $post->post_author ), 'jointswp' ); ?>
			</div>
		</header>

		<div id="inner-content">

		    <section id="featured-posts" class="featured-article archive-thumb-container" role="region">

					<h3 class="text-center">Featured Posts</h3>

					<?php	loop_custom_grid('featured_posts', true, 4); ?>

				</section>

				<hr>

				<section id="featured-projects" class="featured-article archive-thumb-container" role="region">

					<h3 class="text-center">Featured Projects</h3>

					<?php	loop_custom_grid('featured_projects', true, 4); ?>

				</section>

				<hr>

				<section id="featured-series" class="featured-article archive-thumb-container" role="region">

					<h3 class="text-center">Featured Series</h3>

					<?php	loop_custom_grid('featured_series', true, 4); ?>

				</section>
	    </div>
	</div>
	<!-- <p style="text-align: center;">Template: author.php</p> -->

<?php get_footer(); ?>
