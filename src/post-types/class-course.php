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
 */

namespace BadgeFactor2\Post_Types;


/**
 * Course post type.
 */
class Course {


	/**
	 * Init hooks.
	 *
	 * @return void
	 */
	public static function init_hooks() {
		add_action( 'init', array( Course::class, 'init' ), 99 );
		add_filter( 'post_updated_messages', array( Course::class, 'updated_messages' ), 10 );
		add_action( 'cmb2_admin_init', array( Course::class, 'register_settings_metaboxes' ), 10 );
		add_action( 'cmb2_admin_init', array( Course::class, 'register_cpt_metaboxes' ), 10 );
	}


	/**
	 * Registers the `badge_page` post type.
	 */
	public static function init() {

		$plugin_data          = get_plugin_data( __FILE__ );
		$badgefactor2_options = get_option( 'bf2_courses_settings' );

		register_post_type(
			'course',
			array(
				'labels'            => array(
					'name'                  => __( 'Courses', $plugin_data['TextDomain'] ),
					'singular_name'         => __( 'Course', $plugin_data['TextDomain'] ),
					'all_items'             => __( 'All Courses', $plugin_data['TextDomain'] ),
					'archives'              => __( 'Course Archives', $plugin_data['TextDomain'] ),
					'attributes'            => __( 'Course Attributes', $plugin_data['TextDomain'] ),
					'insert_into_item'      => __( 'Insert into Course', $plugin_data['TextDomain'] ),
					'uploaded_to_this_item' => __( 'Uploaded to this Course', $plugin_data['TextDomain'] ),
					'featured_image'        => _x( 'Featured Image', 'course', $plugin_data['TextDomain'] ),
					'set_featured_image'    => _x( 'Set featured image', 'course', $plugin_data['TextDomain'] ),
					'remove_featured_image' => _x( 'Remove featured image', 'course', $plugin_data['TextDomain'] ),
					'use_featured_image'    => _x( 'Use as featured image', 'course', $plugin_data['TextDomain'] ),
					'filter_items_list'     => __( 'Filter Courses list', $plugin_data['TextDomain'] ),
					'items_list_navigation' => __( 'Courses list navigation', $plugin_data['TextDomain'] ),
					'items_list'            => __( 'Courses list', $plugin_data['TextDomain'] ),
					'new_item'              => __( 'New Course', $plugin_data['TextDomain'] ),
					'add_new'               => __( 'Add New', $plugin_data['TextDomain'] ),
					'add_new_item'          => __( 'Add New Course', $plugin_data['TextDomain'] ),
					'edit_item'             => __( 'Edit Course', $plugin_data['TextDomain'] ),
					'view_item'             => __( 'View Course', $plugin_data['TextDomain'] ),
					'view_items'            => __( 'View Courses', $plugin_data['TextDomain'] ),
					'search_items'          => __( 'Search Courses', $plugin_data['TextDomain'] ),
					'not_found'             => __( 'No Courses found', $plugin_data['TextDomain'] ),
					'not_found_in_trash'    => __( 'No Courses found in trash', $plugin_data['TextDomain'] ),
					'parent_item_colon'     => __( 'Parent Course:', $plugin_data['TextDomain'] ),
					'menu_name'             => __( 'Courses', $plugin_data['TextDomain'] ),
				),
				'public'            => true,
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'supports'          => array( 'title', 'editor' ),
				'has_archive'       => ( isset( $badgefactor2_options['bf2_courses_use_archive'] ) && 'on' === $badgefactor2_options['bf2_courses_use_archive'] ),
				'rewrite'           => true,
				'query_var'         => true,
				'menu_position'     => null,
				'menu_icon'         => BF2_BASEURL . 'assets/images/course.svg',
				'show_in_rest'      => false,
				'taxonomies'        => array( 'course-category', 'course-level', 'course-title' ),
			)
		);

	}


	/**
	 * Sets the post updated messages for the `badge_page` post type.
	 *
	 * @param  array $messages Post updated messages.
	 * @return array Messages for the `badge_page` post type.
	 */
	public static function updated_messages( $messages ) {
		global $post;

		$permalink = get_permalink( $post );

		$messages['course'] = array(
			0  => '', // Unused. Messages start at index 1.
			/* translators: %s: post permalink */
			1  => sprintf( __( 'Course updated. <a target="_blank" href="%s">View Course</a>', 'badgefactor2' ), esc_url( $permalink ) ),
			2  => __( 'Custom field updated.', 'badgefactor2' ),
			3  => __( 'Custom field deleted.', 'badgefactor2' ),
			4  => __( 'Course updated.', 'badgefactor2' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Course restored to revision from %s', 'badgefactor2' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			/* translators: %s: post permalink */
			6  => sprintf( __( 'Course published. <a href="%s">View Course</a>', 'badgefactor2' ), esc_url( $permalink ) ),
			7  => __( 'Course saved.', 'badgefactor2' ),
			/* translators: %s: post permalink */
			8  => sprintf( __( 'Course submitted. <a target="_blank" href="%s">Preview Course</a>', 'badgefactor2' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			9  => sprintf(
				/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
				__( 'Course scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Course</a>', 'badgefactor2' ),
				date_i18n( __( 'M j, Y @ G:i', 'badgefactor2' ), strtotime( $post->post_date ) ),
				esc_url( $permalink )
			),
			/* translators: %s: post permalink */
			10 => sprintf( __( 'Course draft updated. <a target="_blank" href="%s">Preview Course</a>', 'badgefactor2' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		);

		return $messages;
	}


	/**
	 * Registers Add-On settings page.
	 */
	public static function register_settings_metaboxes() {
		$plugin_data = get_plugin_data( __FILE__ );

		$args = array(
			'id'           => 'bf2_courses_settings_page',
			'menu_title'   => __( 'Courses', $plugin_data['TextDomain'] ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'bf2_courses_settings',
			'parent_slug'  => 'badgefactor2',
			'tab_group'    => 'badgefactor2',
			'tab_title'    => __( 'Courses', $plugin_data['TextDomain'] ),
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
	}

	/**
	 * Registers Add-On settings page.
	 */
	public static function register_cpt_metaboxes() {
		$plugin_data = get_plugin_data( __FILE__ );

	}


	public static function all_by_category( $category = 0 ) {
		$args    = array(
			'post_type'   => 'course',
			'numberposts' => -1,
			'post_status' => 'publish',
			'category'    => $category,
		);
		$posts   = get_posts( $args );
		$courses = array();
		foreach ( $posts as $post ) {
			$cats = get_the_category( $post->ID );
			foreach ( $cats as $cat ) {
				if ( ! isset( $courses[ $cat->term_id ] ) ) {
					$courses[ $cat->term_id ] = array(
						'name'        => $cat->name,
						'slug'        => $cat->slug,
						'description' => $cat->description,
						'posts'       => array(),
					);
				}
				$courses[ $cat->term_id ]['posts'][] = $post;
				break;
			}
		}
		return $courses;
	}


}
