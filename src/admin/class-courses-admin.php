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


}
