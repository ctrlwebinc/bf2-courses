<?php
/**
 * Badge Factor 2
 * Copyright (C) 2021 ctrlweb
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

namespace BadgeFactor2\Controllers;

use BadgeFactor2\BadgrProvider;
use BadgeFactor2\BadgrUser;
use BadgeFactor2\Models\BadgeClass;
use BadgeFactor2\Models\Issuer;
use BadgeFactor2\Page_Controller;


/**
 * Course Controller Class.
 */
class Course_Controller extends Page_Controller {

	/**
	 * Post Type.
	 *
	 * @var string
	 */
	protected static $post_type = 'course';

	/**
	 * Returns or outputs archive template with $fields array.
	 *
	 * @param string $default_template Default template (for filter hook).
	 * @return void|string
	 */
	public static function archive( $default_template = null ) {

		return parent::archive( $default_template );
	}


	/**
	 * Outputs single template with $fields array.
	 *
	 * @param string $default_template Default template (for filter hook).
	 * @return void|string
	 */
	public static function single( $default_template = null ) {

		global $post;

		if ( static::$post_type === $post->post_type ) {

			$current_user = wp_get_current_user();

			// The WooCommerce add-on is installed.
			if ( class_exists( 'BadgeFactor2\BF2_WooCommerce' ) ) {
				// A product is linked to the course.
				$product_id = get_post_meta( $post->ID, 'course_product', true );
				if ( $product_id ) {
					// The client has not purchased this product, redirect to the product page.
					if ( ! wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product_id ) ) {
						echo sprintf( '<script>window.location.replace("%s")</script>', get_permalink( $product_id ) );
						die;
					}
				}
			}
			
			$options     = get_option( 'badgefactor2' );

			$fields = array();

			$fields['form_slug']                = ! empty( $options['bf2_form_slug'] ) ? $options['bf2_form_slug'] : 'form';
			$fields['autoevaluation_form_slug'] = ! empty( $options['bf2_autoevaluation_form_slug'] ) ? $options['bf2_autoevaluation_form_slug'] : 'autoevaluation';
			$fields['current_user']             = $current_user;
			$fields['badgr_user']               = new BadgrUser( $fields['current_user'] );
			$fields['course']                   = $post;
			$fields['badge_page']               = get_post( get_post_meta( $fields['course']->ID, 'course_badge_page', true ) );
			$fields['badge_entity_id']          = get_post_meta( $fields['badge_page']->ID, 'badgepage_badge', true );
			$fields['badge_page_id']            = get_post_meta( $post->ID, 'course_badge_page', true );
			$fields['badge_entity_id']          = get_post_meta( $fields['badge_page_id'], 'badge', true );
			$fields['badge']                    = BadgeClass::get( $fields['badge_entity_id'] );
			$fields['issuer']                   = $fields['badge'] ? Issuer::get( $fields['badge']->issuer ) : '';
			$fields['autoevaluation_form']      = is_plugin_active( 'bf2-gravityforms/bf2-gravityforms.php' ) ? get_post_meta( $fields['badge_page']->ID, 'autoevaluation_form_id', true ) : '';
			$fields['form_type']                = get_post_meta( $fields['badge_page']->ID, 'badge_request_form_type', true );
			$fields['backpack']                 = BadgrProvider::get_all_assertions_from_user_backpack( $fields['badgr_user'] );
			$fields['assertion']                = null;
			$fields['issued']                   = false;
			$fields['revoked']                  = false;
			$fields['issued_on']                = null;
			$fields['course_categories']        = wp_get_post_terms( $fields['course']->ID, 'course-category', array( 'fields' => 'all' ) );
			$fields['course_titles']            = wp_get_post_terms( $fields['course']->ID, 'course-title', array( 'fields' => 'all' ) );
			$fields['course_level']             = wp_get_post_terms( $fields['course']->ID, 'course-level', array( 'fields' => 'all' ) );


			foreach ( $fields['backpack'] as $item ) {
				if ( $item->badgeclass === $fields['badge_entity_id'] ) {
					$fields['assertion'] = $item;
					$fields['issued']    = true;
					$fields['revoked']   = $fields['assertion']->revoked;
					$fields['issued_on'] = gmdate( 'Y-m-d', strtotime( $fields['assertion']->issuedOn ) );
					break;
				}
			}

			global $bf2_template;
			$bf2_template         = new \stdClass();
			$bf2_template->fields = $fields;
		}

		return parent::single( $default_template );
	}

}
