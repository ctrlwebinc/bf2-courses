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
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 */

use BadgeFactor2\BadgrProvider;
use BadgeFactor2\BadgrUser;
use BadgeFactor2\Helpers\Template;

$plugin_data = get_plugin_data( BF2_COURSES_FILE );
$options     = get_option( 'badgefactor2' );

$form_slug                = ! empty( $options['bf2_form_slug'] ) ? $options['bf2_form_slug'] : 'form';
$autoevaluation_form_slug = ! empty( $options['bf2_autoevaluation_form_slug'] ) ? $options['bf2_autoevaluation_form_slug'] : 'autoevaluation';

$current_user = wp_get_current_user();
$badgr_user   = new BadgrUser( $current_user );
$course       = $post;

// The WooCommerce add-on is installed.
if ( class_exists( 'BadgeFactor2\BF2_WooCommerce' ) ) {
	// A product is linked to the course.
	$product_id = get_post_meta( $post->ID, 'course_product', true );
	if ( $product_id ) {
		// The client has not purchased this product, redirect to the product page.
		if ( ! wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product_id ) ) {
			echo sprintf( '<script>window.location.replace("%s")</script>', get_permalink( $product_id ) );
			die;
		}
	}
}

$badge_page      = get_post( get_post_meta( $course->ID, 'course_badge_page', true ) );
$badge_entity_id = get_post_meta( $badge_page->ID, 'badgepage_badge', true );
$badge_page_id   = get_post_meta( $post->ID, 'course_badge_page', true );
$badge_entity_id = get_post_meta( $badge_page_id, 'badge', true );
$badge           = BadgeFactor2\Models\BadgeClass::get( $badge_entity_id );
$issuer          = BadgeFactor2\Models\Issuer::get( $badge->issuer );

$autoevaluation_form = is_plugin_active( 'bf2-gravityforms/bf2-gravityforms.php' ) ? get_post_meta( $badge_page->ID, 'autoevaluation_form_id', true ) : '';

$form_type = get_post_meta( $badge_page->ID, 'badge_request_form_type', true );

$backpack = BadgrProvider::get_all_assertions_from_user_backpack( $badgr_user );

$assertion         = null;
$issued            = false;
$revoked           = false;
$issued_on         = null;
$course_categories = wp_get_post_terms( $course->ID, 'course-category', array( 'fields' => 'all' ) );
$course_titles     = wp_get_post_terms( $course->ID, 'course-title', array( 'fields' => 'all' ) );
$course_level      = wp_get_post_terms( $course->ID, 'course-level', array( 'fields' => 'all' ) );


foreach ( $backpack as $item ) {
	if ( $item->badgeclass === $badge_entity_id ) {
		$assertion = $item;
		$issued    = true;
		$revoked   = $assertion->revoked;
		$issued_on = gmdate( 'Y-m-d', strtotime( $assertion->issuedOn ) );
		break;
	}
}

?>
<?php
/*
 * You can override this template by copying it in your theme, in a
 * badgefactor2/ subdirectory, and modifying it there.
 */

get_header();
?>

<main <?php post_class(); ?> id="post-<?php the_ID(); ?> site-content" role="main">
	<section id="primary" class="section-inner">
		<div id="badge" class="badge">
			<div class="content">
				<h1 class="badge-name">
					<span class="badge-title"><?php echo $post->post_title; ?></span>
				</h1>
				<div class="badge-container">
					<h3>Description</h3>
					<p class="badge-description">
						<?php echo $post->post_content; ?>
					</p>
				</div>
			</div>
			<div class="sidebar">
				<div class="badge-container">
					<img class="badge-image" src="<?php echo $badge->image; ?>" alt="<?php echo $badge->name; ?>">
					<div class="badge-issued">
						<h3 class="badge-issued-title">
							<?php echo __( 'Issued by', BF2_COURSES_DATA['TextDomain'] ); ?>
							<a target="_blank" href="<?php echo $issuer->url; ?>"><?php echo $issuer->name; ?></a>
						</h3>
					</div>
					<div class="badge-actions">
						<div class="badge-actions-course">
							<?php if ( false && $assertion && ! $assertion->revoked ) : ?>
								<?php include( Template::locate( 'partials/badge-received', null, 'bf2-courses' ) ); ?>
							<?php elseif ( $autoevaluation_form ) : ?>
								<a class="btn" href="<?php echo get_permalink( $badge_page->ID ) . $autoevaluation_form_slug . '/' . $form_slug; ?>"><?php echo __( 'Autoevaluation form', $plugin_data['TextDomain'] ); ?></a>
							<?php else : ?>
								<a class="btn" href="<?php echo get_permalink( $badge_page->ID ) . $form_slug; ?>"><?php echo __( 'Request this badge', $plugin_data['TextDomain'] ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
