<?php
/**
 * Badge Factor 2 - Courses Addon
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
 * Plugin Name: Badge Factor 2 - Courses Addon
 * Plugin URI: https://github.com/ctrlwebinc/badgefactor2-courses
 * GitHub Plugin URI: https://ctrlwebinc/badgefactor2-courses
 * Description: Adds Courses to Badge Factor 2
 * Author: ctrlweb
 * Version: 1.0.0
 * Author URI: https://badgefactor2.com/
 * License: GNU AGPL
 * Text Domain: bf2-courses
 * Domain Path: /languages
 *
 * @phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain
 */

use BadgeFactor2\Models\BadgeClass;
use BadgeFactor2\Models\Issuer;
use BadgeFactor2\Post_Types\Course;
use BadgeFactor2\Shortcodes\Courses;

if ( class_exists( 'BadgeFactor2\BadgeFactor2' ) && \BadgeFactor2\BadgeFactor2::is_initialized() ) {

	// Post Type.
	require_once plugin_dir_path( __FILE__ ) . '/src/post-types/class-course.php';
	Course::init_hooks();

	// Short Code.
	require_once plugin_dir_path( __FILE__ ) . '/src/public/shortcodes/class-courses.php';
	Courses::init_hooks();

	// Assets.
	add_action(
		'admin_enqueue_scripts',
		function () {
			$plugin_data = get_plugin_data( __FILE__ );
			wp_enqueue_script( 'bf2-courses-admin-js', plugin_dir_url( __FILE__ ) . '/assets/js/admin.js', array( 'jquery' ), $plugin_data['Version'], true );
			wp_enqueue_style( 'bf2-courses-admin-css', plugin_dir_url( __FILE__ ) . '/assets/css/admin.css', array(), $plugin_data['Version'], 'all' );
		}
	);

	// Taxonomies.
	add_action(
		'init',
		function () {
			$plugin_data = get_plugin_data( __FILE__ );

			register_taxonomy(
				'course-category',
				array( 'course' ),
				array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => __( 'Category', $plugin_data['TextDomain'] ),
						'singular_name'     => __( 'Category', $plugin_data['TextDomain'] ),
						'search_items'      => __( 'Search Categories', $plugin_data['TextDomain'] ),
						'all_items'         => __( 'All Categories', $plugin_data['TextDomain'] ),
						'parent_item'       => __( 'parent Category', $plugin_data['TextDomain'] ),
						'parent_item_colon' => __( 'parent Category:', $plugin_data['TextDomain'] ),
						'edit_item'         => __( 'Edit Category', $plugin_data['TextDomain'] ),
						'update_item'       => __( 'Update Category', $plugin_data['TextDomain'] ),
						'add_new_item'      => __( 'Add new Category', $plugin_data['TextDomain'] ),
						'new_item_name'     => __( 'new Category Name', $plugin_data['TextDomain'] ),
						'menu_name'         => __( 'Categories', $plugin_data['TextDomain'] ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'course-category' ),
				)
			);

			register_taxonomy(
				'course-title',
				array( 'course' ),
				array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => __( 'Title', $plugin_data['TextDomain'] ),
						'singular_name'     => __( 'Title', $plugin_data['TextDomain'] ),
						'search_items'      => __( 'Search Titles', $plugin_data['TextDomain'] ),
						'all_items'         => __( 'All Titles', $plugin_data['TextDomain'] ),
						'parent_item'       => __( 'parent Title', $plugin_data['TextDomain'] ),
						'parent_item_colon' => __( 'parent Title:', $plugin_data['TextDomain'] ),
						'edit_item'         => __( 'Edit Title', $plugin_data['TextDomain'] ),
						'update_item'       => __( 'Update Title', $plugin_data['TextDomain'] ),
						'add_new_item'      => __( 'Add new Title', $plugin_data['TextDomain'] ),
						'new_item_name'     => __( 'new Title Name', $plugin_data['TextDomain'] ),
						'menu_name'         => __( 'Titles', $plugin_data['TextDomain'] ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'course-title' ),
				)
			);

			register_taxonomy(
				'course-level',
				array( 'course' ),
				array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => __( 'Level', $plugin_data['TextDomain'] ),
						'singular_name'     => __( 'Level', $plugin_data['TextDomain'] ),
						'search_items'      => __( 'Search Levels', $plugin_data['TextDomain'] ),
						'all_items'         => __( 'All Levels', $plugin_data['TextDomain'] ),
						'parent_item'       => __( 'parent Level', $plugin_data['TextDomain'] ),
						'parent_item_colon' => __( 'parent Level:', $plugin_data['TextDomain'] ),
						'edit_item'         => __( 'Edit Level', $plugin_data['TextDomain'] ),
						'update_item'       => __( 'Update Level', $plugin_data['TextDomain'] ),
						'add_new_item'      => __( 'Add new Level', $plugin_data['TextDomain'] ),
						'new_item_name'     => __( 'new Level Name', $plugin_data['TextDomain'] ),
						'menu_name'         => __( 'Levels', $plugin_data['TextDomain'] ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => 'course-level' ),
				)
			);
		},
		1
	);

	// Templates management.
	add_action(
		'template_redirect',
		function() {
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
						if ( file_exists( get_template_directory() . '/badgefactor2/tpl.course.php' ) ) {
							return get_template_directory() . '/badgefactor2/tpl.course.php';
						} else {
							return plugin_dir_path( __FILE__ ) . '/templates/tpl.course.php';
						}
					}
				);
			}
			if ( is_post_type_archive( 'course' ) ) {
				add_filter(
					'template_include',
					function () {
						if ( file_exists( get_template_directory() . '/badgefactor2/tpl.courses.php' ) ) {
							return get_template_directory() . '/badgefactor2/tpl.courses.php';
						} else {
							return plugin_dir_path( __FILE__ ) . '/templates/tpl.courses.php';
						}
					}
				);
			}

		}
	);

} else {

	// Deactivate if BadgeFactor2 is not active.
	deactivate_plugins( plugin_dir_path( __FILE__ ) . '/bf2-courses.php' );
}


