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
 */

namespace BadgeFactor2\Post_Types;

use BadgeFactor2\Models\BadgeClass;
use BadgeFactor2\Post_Types\Approver;

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
		add_action( 'cmb2_admin_init', array( Course::class, 'custom_meta_boxes' ), 10 );
	}


	/**
	 * Registers the `badge_page` post type.
	 */
	public static function init() {

		register_post_type(
			'course',
			array(
				'labels'            => array(
					'name'                  => __( 'Courses', 'bf2-courses' ),
					'singular_name'         => __( 'Course', 'bf2-courses' ),
					'all_items'             => __( 'All Courses', 'bf2-courses' ),
					'archives'              => __( 'Course Archives', 'bf2-courses' ),
					'attributes'            => __( 'Course Attributes', 'bf2-courses' ),
					'insert_into_item'      => __( 'Insert into Course', 'bf2-courses' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Course', 'bf2-courses' ),
					'featured_image'        => _x( 'Featured Image', 'course', 'bf2-courses' ),
					'set_featured_image'    => _x( 'Set featured image', 'course', 'bf2-courses' ),
					'remove_featured_image' => _x( 'Remove featured image', 'course', 'bf2-courses' ),
					'use_featured_image'    => _x( 'Use as featured image', 'course', 'bf2-courses' ),
					'filter_items_list'     => __( 'Filter Courses list', 'bf2-courses' ),
					'items_list_navigation' => __( 'Courses list navigation', 'bf2-courses' ),
					'items_list'            => __( 'Courses list', 'bf2-courses' ),
					'new_item'              => __( 'New Course', 'bf2-courses' ),
					'add_new'               => __( 'Add New', 'bf2-courses' ),
					'add_new_item'          => __( 'Add New Course', 'bf2-courses' ),
					'edit_item'             => __( 'Edit Course', 'bf2-courses' ),
					'view_item'             => __( 'View Course', 'bf2-courses' ),
					'view_items'            => __( 'View Courses', 'badgefactor2' ),
					'search_items'          => __( 'Search Courses', 'badgefactor2' ),
					'not_found'             => __( 'No Courses found', 'badgefactor2' ),
					'not_found_in_trash'    => __( 'No Courses found in trash', 'badgefactor2' ),
					'parent_item_colon'     => __( 'Parent Course:', 'badgefactor2' ),
					'menu_name'             => __( 'Courses', 'badgefactor2' ),
				),
				'public'            => true,
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'supports'          => array( 'title', 'editor' ),
				'has_archive'       => true,
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
	 * Custom meta boxes.
	 *
	 * @return void
	 */
	public static function custom_meta_boxes() {
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
