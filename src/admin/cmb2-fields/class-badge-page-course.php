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

namespace BadgeFactor2\Admin\CMB2_Fields;

/**
 * CMB2 Badge Page Course Field.
 */
class Badge_Page_Course {

	/**
	 * Init Hooks.
	 *
	 * @return void
	 */
	public static function init_hooks() {
		add_filter( 'cmb2_render_badge_page_course', array( self::class, 'render_badge_page_course' ), 10, 5 );
	}


	/**
	 * Render Badge Page Course.
	 *
	 * @param CMB2_Field $field Field.
	 * @param string     $field_escaped_value Field escaped value.
	 * @param string     $field_object_id Field object id.
	 * @param string     $field_object_type Field object type.
	 * @param CMB2_Types $field_type_object Field Type Object.
	 * @return void
	 */
	public static function render_badge_page_course( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		$badge_page_course = $field_escaped_value;
		if ( ! $badge_page_course ) {
			// The field hasn't been automatically filled yet, show checkbox.

			echo $field_type_object->checkbox(
				array(
					'name'  => $field_type_object->_name(),
					'id'    => $field_type_object->_id(),
					'value' => 'on',
					'desc'  => __( 'Create course?', BF2_COURSES_DATA['TextDomain'] ),
				)
			);

		} else {
			// The field has been filled automatically, show link.

			$course = get_post( $badge_page_course );
			if ( 'on' !== $badge_page_course ) {
				echo sprintf( '<a href="/wp-admin/post.php?post=%d&action=edit">%s</a>', $badge_page_course, $course->post_title );
				echo sprintf( '<input type="hidden" name="course" value="%s">', $badge_page_course );
			} else {
				echo __( 'Link problem', BF2_COURSES_DATA['TextDomain'] );
			}
		}
	}
}
