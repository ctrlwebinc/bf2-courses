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
 * @phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain
 * @phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralText
 */

namespace BadgeFactor2\Shortcodes;

use BadgeFactor2\Post_Types\Course;
use stdClass;

/**
 * Shortcodes Class.
 */
class Courses {

	/**
	 * Courses Shortcode Init.
	 *
	 * @return void
	 */
	public static function init_hooks() {
		add_action( 'init', array( Courses::class, 'init' ) );
	}

	/**
	 * Init hook.
	 *
	 * @return void
	 */
	public static function init() {
		add_shortcode( 'bf2-courses', array( Courses::class, 'list' ) );
	}


	/**
	 * List.
	 *
	 * @param array  $atts Attributes.
	 * @param string $content Content.
	 * @param string $tag Tag.
	 * @return string
	 */
	public static function list( $atts = array(), $content = null, string $tag ) {

		$plugin_data = get_plugin_data( BF2_COURSES_FILE );

		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		// Override default attributes with user attributes.
		$list_atts = shortcode_atts(
			array(
				'display-filter'           => 'true',
				'filter-title-tag'         => 'label',
				'filter-title'             => __( 'Filter by type', $tag ),
				'category-title-tag'       => 'h2',
				'category-description-tag' => 'p',
				'display-badge-image'      => 'true',
				'badge-image-size'         => 'full',
				'course-title-tag'         => 'h3',
			),
			$atts,
			$tag
		);

		$course_categories = get_terms(
			array(
				'taxonomy' => 'course-category',
				'orderby'  => 'name',
				'order'    => 'ASC',
			)
		);
		$selected_category = 'true' === $list_atts['display-filter'] && isset( $_GET['course-cat'] ) ? $_GET['course-cat'] : null;
		$courses           = Course::all_by_category( $selected_category );

		// Start output.
		$o = '';

		// Start container.
		$o .= '<section class="bf2-courses-section">';

		// Start filter section.
		if ( 'true' === $list_atts['display-filter'] ) {

			$filter_for = 'label' === $list_atts['filter-title-tag'] ? ' for="course-category"' : '';

			$o .= '<div class="course-filter">';
			$o .= sprintf( '<%s class="course-category-filter-title"%s>%s</%s>', esc_html__( $list_atts['filter-title-tag'], $tag ), $filter_for, esc_html__( $list_atts['filter-title'], $tag ), esc_html__( $list_atts['filter-title-tag'], $tag ) );

			// Filter.
			$o .= '<form action="" method="get">';
			$o .= '<select name="course-cat" class="course-filter-select" onchange="this.form.submit()">';
			$o .= sprintf( '<option class="course-category-filter-option" value="">%s</option>', __( 'All', $plugin_data['TextDomain'] ) );
			foreach ( $course_categories as $course_category ) {
				$selected = isset( $_GET['course-cat'] ) && intval( $_GET['course-cat'] ) === intval( $course_category->term_id ) ? ' selected' : '';
				$o       .= sprintf( '<option class="course-category-filter-option" value="%s"%s>%s</option>', $course_category->term_id, $selected, $course_category->name );
			}
			$o .= '</select>';
			$o .= '</form>';

			// End filter section.
			$o .= sprintf( '</div>' );
		}

		if ( ! $courses ) {

			$msg = isset( $_GET['course-cat'] ) ? __( 'There are no courses currently available in this category.', $plugin_data['TextDomain'] ) : __( 'There are no courses currently available.', $plugin_data['TextDomain'] );
			$o  .= sprintf( '<p>%s</p>', $msg );

		} else {

			// Loop Course Categories.
			foreach ( $courses as $course_category ) {

				$course_category_class_suffix = $course_category['slug'] ? esc_html__( $course_category['slug'], $tag ) : 'all';

				$o .= sprintf( '<div class="course-category course-category-%s">', $course_category_class_suffix );
				if ( $course_category['name'] ) {
					$o .= sprintf( '<%s class="course-category-title">%s</%s>', esc_html__( $list_atts['category-title-tag'], $tag ), esc_html__( $course_category['name'], $tag ), esc_html__( $list_atts['category-title-tag'], $tag ) );
				}
				if ( $course_category['description'] ) {
					$o .= sprintf( '<%s class="course-category-description">%s</%s>', esc_html__( $list_atts['category-description-tag'], $tag ), esc_html__( $course_category['description'], $tag ), esc_html__( $list_atts['category-description-tag'], $tag ) );
				}

				// Start Courses.
				$o .= '<ul class="courses-list">';

				// Loop Courses.
				foreach ( $course_category['posts'] as $course ) {

					$o .= '<li class="course">';

					// Badge.
					if ( 'true' === $list_atts['display-badge-image'] ) {
						$badge_id = get_post_meta( $course->ID, 'badge', true );
						if ( $badge_id ) {
							$badge       = get_post( $badge_id );
							$badge_image = wp_get_attachment_image_src( get_post_thumbnail_id( $badge_id ), $list_atts['badge-image-size'] );
						} else {
							$badge             = new stdClass();
							$badge->post_title = __( 'No badge issued for this course', $plugin_data['TextDomain'] );
							$badge_image       = BF2_BASEURL . '/assets/images/no-badge.svg';
						}

						$o .= '<figure>';
						$o .= sprintf( '<a href="%s" class="course-link-image">', get_permalink( $course ) );
						$o .= sprintf( '<img class="badge-image" src="%s" alt="%s">', $badge_image, $badge->post_title );
						$o .= sprintf( '<figcaption class="badge-name">%s</figcaption>', $badge->post_title );
						$o .= '</a>';
						$o .= '</figure>';
					}

					// Course.
					$o .= sprintf( '<a href="%s" class="course-link-title"><%s class="course-title">%s</%s></a>', get_permalink( $course ), esc_html__( $list_atts['course-title-tag'], $tag ), $course->post_title, esc_html__( $list_atts['course-title-tag'], $tag ) );
					$o .= sprintf( '<div class="course-excerpt">%s</div>', get_the_excerpt( $course ) );
					$o .= '</li>';
				}

				// End Courses.
				$o .= '</ul>';
			}

			// End container.
			$o .= '</section>';
		}

		return $o;
	}
}
