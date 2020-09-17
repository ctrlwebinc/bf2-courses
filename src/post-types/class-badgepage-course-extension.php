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
 * @phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralContext
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace BadgeFactor2\Post_Types;

/**
 * Badge Page post type.
 */
class BadgePage_Course_Extension {

	/**
	 * Init hooks.
	 *
	 * @return void
	 */
	public static function init_hooks() {
		add_action( 'cmb2_admin_init', array( self::class, 'register_cpt_metaboxes' ), 20 );
		add_action( 'save_post', array( self::class, 'save_badge_page' ), 12, 3 );
	}

	/**
	 * Custom meta boxes.
	 *
	 * @return void
	 */
	public static function register_cpt_metaboxes() {

		// Course (if plugin active).

		$cmb = new_cmb2_box(
			array(
				'id'           => 'badgepage_course_info',
				'title'        => __( 'Course', BF2_COURSES_DATA['TextDomain'] ),
				'object_types' => array( 'badge-page' ),
				'context'      => 'side',
				'priority'     => 'default',
				'show_names'   => true,
			)
		);

		$cmb->add_field(
			array(
				'id'   => 'course',
				'type' => 'badge_page_course',
			)
		);
	}


	/**
	 * Create Badge Page hook.
	 *
	 * @param WP_Post $post Badge Page.
	 * @return void
	 */
	public static function save_badge_page( $post_id, $post, $update ) {
		if ( $update && 'badge-page' === $post->post_type ) {
			$create_course = get_post_meta( $post_id, 'course', true );
			if ( 'on' === $create_course ) {
				// insert the post and set the category
				$course_id = wp_insert_post(
					array(
						'post_type'      => 'course',
						'post_name'      => $post->post_name,
						'post_title'     => $post->post_title,
						'post_content'   => '',
						'post_status'    => 'draft',
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
					)
				);
				if ( $course_id ) {
					add_post_meta( $course_id, 'course_badge_page', $post_id );
					update_post_meta( $post_id, 'course', $course_id );
				}
			}
		}
	}
}
