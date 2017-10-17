<?php
$grid_columns = 2; ?>


		<!--Item: -->
		<div class="large-6 medium-6 columns panel" data-equalizer-watch>

			<article id="post-<?php the_ID(); ?>" <?php post_class(''); ?> role="article">

				<!-- If post has a thumnail, add to section bg-img -->
				<?php if( has_post_thumbnail() ): ?>
					<section class="archive-grid featured-image" itemprop="articleBody" style="background-image: url('<?php
						echo esc_url( get_the_post_thumbnail_url($post->ID, 'medium') );
					?>');">
					</section> <!-- end article section -->
				<?php endif; ?>

				<header class="article-header">
					<h3 class="title">
						<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
					<?php get_template_part( 'parts/content', 'byline' ); ?>
				</header> <!-- end article header -->

				<section class="entry-content" itemprop="articleBody">
					<?php the_excerpt(); ?>
					<!-- '<button class="tiny">' . __( 'Read more...', 'jointswp' ) . '</button>' -->
				</section> <!-- end article section -->

			</article> <!-- end article -->

		</div>