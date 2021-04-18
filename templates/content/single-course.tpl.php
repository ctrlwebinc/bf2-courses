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

/*
 * You can override this template by copying it in your theme, in a
 * badgefactor2/ subdirectory, and modifying it there.
 */

use BadgeFactor2\Helpers\Template;

global $bf2_template;
$plugin_data = get_plugin_data( BF2_COURSES_FILE );
?>

<?php echo do_shortcode( $bf2_template->fields['course']->post_content ); ?>

<div class="badge-actions">
  <div class="course-actions">
    <?php if ( null !== $bf2_template->fields['assertion'] && ! $bf2_template->fields['assertion']->revoked ) : ?>
      <?php include( Template::locate( 'badge-received' ) ); ?>
    <?php elseif ( $bf2_template->fields['autoevaluation_form'] ) : ?>
      <a class="btn" href="<?php echo get_permalink( $bf2_template->fields['badge_page']->ID ) . $bf2_template->fields['autoevaluation_form_slug']; ?>"><?php echo __( 'Autoevaluation form', $plugin_data['TextDomain'] ); ?></a>
    <?php else : ?>
      <a class="btn" href="<?php echo get_permalink( $bf2_template->fields['badge_page']->ID ) . $bf2_template->fields['form_slug']; ?>"><?php echo __( 'Request this badge', $plugin_data['TextDomain'] ); ?></a>
    <?php endif; ?>
  </div>
</div>
