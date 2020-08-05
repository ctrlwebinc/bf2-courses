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

$options   = get_option( 'badgefactor2' );
$form_slug = $options['bf2_form_slug'];

$current_user = wp_get_current_user();
$badgr_user   = new BadgrUser( $current_user );

$course          = $post;
$badge_page      = get_post( get_post_meta( $course->ID, 'course_badge_page', true ) );
$form_type       = get_post_meta( $badge_page->ID, 'badge_request_form_type', true );
$badge_entity_id = get_post_meta( $badge_page->ID, 'badgepage_badge', true );
$backpack        = BadgrProvider::get_all_assertions_from_user_backpack( $badgr_user );
$assertion       = null;
$issued          = false;
$revoked         = false;
$issued_on       = null;
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
<main id="site-content" role="main">
	<article <?php post_class(); ?> id="post-<?php echo $post->ID; ?>">
	<h1><?php echo $post->post_title; ?></h1>
	<?php echo $post->post_content; ?>
	</article>
	<?php if ( false && $assertion && ! $assertion->revoked ) : ?>
		<?php include( Template::locate( 'partials/badge-received', null, 'bf2-courses' ) ); ?>
	<?php else : ?>
		<a href="<?php echo get_permalink( $badge_page->ID ) . $form_slug; ?>"><?php echo __( 'Request this badge', $plugin_data['TextDomain'] ); ?>
	<?php endif; ?>
</main>
<?php
get_footer();
