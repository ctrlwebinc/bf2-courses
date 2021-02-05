<?php
/**
 * Badge Factor 2
 * Copyright (C) 2020 ctrlweb
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
 * @package Badge_Factor_2_Courses
 *
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 * @phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain
 */

namespace BadgeFactor2;

use BadgeFactor2\Controllers\Course_Controller;
use BadgeFactor2\Helpers\Template;

/**
 * Courses Public Class.
 */
class Courses_Public {

	public static function init_hooks() {	
		add_action( 'wp_enqueue_scripts', array( self::class, 'wp_euqueue_scripts' ) );
		add_filter( 'archive_template', array( Course_Controller::class, 'archive' ) );
		add_filter( 'single_template', array( Course_Controller::class, 'single' ) );
	}

	/**
	 * Euqueue scripts.
	 *
	 * @return void
	 */
	public static function wp_euqueue_scripts() {
		$plugin_data = get_plugin_data( BF2_COURSES_FILE );
		wp_enqueue_style( 'bf2-courses-public-css', plugin_dir_url( BF2_COURSES_FILE ) . '/assets/css/public.css', array(), $plugin_data['Version'], 'all' );
	}


	/**
	 * Template redirect.
	 *
	 * @return void
	 */
	public static function template_redirect() {
		if ( is_singular( 'course' ) ) {

			$course          = get_post();
			$badge_entity_id = get_post_meta( $course->ID, 'badgr_badge', true );
			if ( $badge_entity_id ) {
				$badge_entity_id = $badge_entity_id;
				$badge           = BadgeClass::get( $badge_entity_id );
				$course->badge   = $badge;

				$issuer_entity_id      = $badge->issuer;
				$issuer                = Issuer::get( $issuer_entity_id );
				$course->badge->issuer = $issuer;
			} else {
				$course->badge = null;
			}

			add_filter(
				'template_include',
				function () {
					return Template::locate( 'tpl.course' );
				}
			);
		}
		if ( is_post_type_archive( 'course' ) ) {
			add_filter(
				'template_include',
				function () {
					return Template::locate( 'tpl.courses' );
				}
			);
		}
	}
}
