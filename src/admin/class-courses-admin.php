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
 * @phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain
 */

namespace BadgeFactor2\Admin;

use BadgeFactor2\Post_Types\BadgePage;

/**
 * Courses Admin Class.
 */
class Courses_Admin {

	/**
	 * Init Hooks.
	 *
	 * @return void
	 */
	public static function init_hooks() {
		add_action( 'admin_enqueue_scripts', array( self::class, 'admin_enqueue_scripts' ) );
		add_action( 'cmb2_admin_init', array( self::class, 'register_settings_metaboxes' ), 10 );

	}


	/**
	 * Enqueue admin scripts.
	 *
	 * @return void
	 */
	public static function admin_enqueue_scripts() {
		$plugin_data = get_plugin_data( BF2_COURSES_FILE );
		wp_enqueue_script( 'bf2-courses-admin-js', plugin_dir_url( BF2_COURSES_FILE ) . '/assets/js/admin.js', array( 'jquery' ), $plugin_data['Version'], true );
		wp_enqueue_style( 'bf2-courses-admin-css', plugin_dir_url( BF2_COURSES_FILE ) . '/assets/css/admin.css', array(), $plugin_data['Version'], 'all' );
	}

	/**
	 * Registers Add-On settings page.
	 */
	public static function register_settings_metaboxes() {
		$plugin_data = get_plugin_data( BF2_COURSES_FILE );

		$args = array(
			'id'           => 'bf2_courses_settings_page',
			'menu_title'   => __( 'Courses', $plugin_data['TextDomain'] ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'bf2_courses_settings',
			'parent_slug'  => 'badgefactor2',
			'tab_group'    => 'badgefactor2',
			'tab_title'    => __( 'Courses', $plugin_data['TextDomain'] ),
			'capability'   => 'manage_badgr',
		);

		// 'tab_group' property is supported in > 2.4.0.
		if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
			$args['display_cb'] = 'badgefactor2_options_display_with_tabs';
		}

		$plugins = new_cmb2_box( $args );

		$plugins->add_field(
			array(
				'name' => __( 'Use Courses Archive?', $plugin_data['TextDomain'] ),
				'desc' => __( 'If you enable this, a course archive will be automatically created. When you modify this, you need to flush your rewrite rules.', $plugin_data['TextDomain'] ),
				'id'   => 'bf2_courses_use_archive',
				'type' => 'checkbox',
			)
		);

		$plugins->add_field(
			array(
				'name' => __( 'Use Courses Duration?', $plugin_data['TextDomain'] ),
				'desc' => __( 'If you enable this, a course duration field will be added to Courses.', $plugin_data['TextDomain'] ),
				'id'   => 'bf2_courses_duration_active',
				'type' => 'checkbox',
			)
		);
	}

}
