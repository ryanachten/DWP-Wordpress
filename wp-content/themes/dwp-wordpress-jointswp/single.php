<?php get_header(); ?>

<div id="content">

	<?php //get_sidebar(); ?>

	<div id="inner-content">

		<main id="main" class="small-12 small-centered" role="main">

		    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		    	<?php get_template_part( 'parts/loop', 'single' ); ?>

					<!-- Check to see if any Series are associated with Post/Project
					via ACF Relationship reverse query -->
					<?php $series = get_posts( array(
						'post_type' => 'series',
						'meta_query' => array(
							array(
								'key' => 'series_parts', //name of the ACF Relantionship
								'value' => '"' . get_the_ID() . '"', //get ID of post as string
								'compare' => 'LIKE' // use meta_query LIKE to to match ID against db value in serialized array
							)
						)
					)); ?>

		    <?php endwhile; else : ?>

		   		<?php get_template_part( 'parts/content', 'missing' ); ?>

		    <?php endif; ?>

		</main> <!-- end #main -->

	</div> <!-- end #inner-content -->

</div> <!-- end #content -->

<hr>

<!-- Article metadata section -->
<section class="article-footer small-12 medium-10 large-8 small-centered">

	<!-- Article pagigination using the nextpage quicktag -->
	<?php wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'jointswp' ), 'after'  => '</div>' ) ); ?>

	<!-- Article tags -->
	<p class="tags">
		<?php the_tags('<h6 class="tags-title"><strong>' . __( 'Tagged with:', 'jointswp' ) . '</strong></h6> ', ', ', ''); ?>
	</p>

	<!-- Article categories -->
	<p class="categories">
		<?php $article_categories = get_the_category();
			// Prevent empty strings or simply 'Uncategorised' presenting category section
			if( !empty( $article_categories && $article_categories[0]->name !== 'Uncategorised' ) ):?>
				<h6 class="categories-title"><strong>Categorised under:</strong></h6>
				<?php the_category(', '); ?>
			<?php endif; ?>
	</p>

	<!-- Article courses -->
	<p class="courses">
		<?php $article_courses = get_the_terms( $post->ID, 'courses' ); //access ACF Taxonomy field
			 if( !empty( $article_courses ) ): ?>
				<h6 class="courses-title"><strong>Produced as part of the following courses:</strong></h6>
				<?php the_terms($post->ID, 'courses'); ?>
			<?php endif; ?>
	</p>

	<!-- If the Post/Project is associated with a Series
	link to the Series -->
	<section id="article-relatedseries-container" class="small-10 small-centered">
		<?php if( $series ): ?>
			<h4>Featured in the following Series:</h4>
				<?php foreach( $series as $series_post ): ?>
					<a href="<?php echo get_permalink( $series_post->ID ); ?>">
						<div class="article-relatedseries-thumb" style="background-image: url(<?php echo get_the_post_thumbnail_url($series_post->ID); ?>);">

							<h5 class="article-relatedseries-title"><?php echo get_the_title( $series_post->ID ); ?></h5>

						</div>

					</a>
				<?php endforeach; ?>
		<?php endif; ?>
	</section>

	<p style="text-align: center;">Template: single.php</p>

</section>

<!-- Comments not allowed on frontend of site - uncomment if requirement changes -->
<!-- <?php comments_template(); ?> -->

<?php get_footer(); ?>
