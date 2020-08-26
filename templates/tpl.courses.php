<?php
/**
 * Badge Factor 2
 * Copyright (C) 2019 ctrlweb
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package Badge_Factor_2
 *
 * @phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

/*
 * You can override this template by copying it in your theme, in a
 * badgefactor2/ subdirectory, and modifying it there.
 */

get_header();

$customPostType 	  = 'course';
$customTaxonomy 	  = 'course-level';
$termsByCourseLevel = get_terms($customTaxonomy);

?>

<main <?php post_class(); ?> id="post-<?php the_ID(); ?> site-content" role="main">
	<div class="badges-page">
		
		<header class="section-inner">
			<h1 class="badges-title"><?php echo "Cours" ?></h1>
		</header>

		<?php

		foreach($termsByCourseLevel as $custom_term) {

			wp_reset_query();

			$args = array('post_type' => $customPostType,
				'tax_query' => array(
					array(
						'taxonomy' => $customTaxonomy,
						'field'    => 'slug',
						'terms'    => $custom_term->slug
					)
				)
			);
			$loop = new WP_Query($args);
					
			if($loop->have_posts()) { ?>


				<section class="badges-category section-inner">
					<h2 class="badges-category-title"><?php echo $custom_term->name; ?></h2>
					<p class="badges-category-description">
						<?php echo $custom_term->description;?>
					</p>
					<div class="badges-category-items">
						
						<?php
						while($loop->have_posts()) : $loop->the_post(); 
						
							$badge_page_id 	 = get_post_meta( $post->ID, 'course_badge_page', true ); 
							$badge_entity_id = get_post_meta($badge_page_id, 'badge', true);
							$badge           = BadgeFactor2\Models\BadgeClass::get( $badge_entity_id );

							?>
							
							<div class="badge">
								<a href="<?php echo get_permalink($post->ID); ?>" class="badge-inner">
									<img class="badge-image" src="<?php echo $badge->image; ?>" alt="<?php echo $badge->name; ?>">
									<h3 class="badge-title"><?php echo the_title(); ?></h3>
								</a>
							</div>
						<?php
						endwhile;
						?>
					</div>
				</section>

				<?php
			}
			?>
		<?php
		}
		?>
	</div>
</main>
<?php
get_footer();
