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
 * @package Badge_Factor_2_Courses
 *
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound
 */

namespace BadgeFactor2;

use BadgeFactor2\Admin\CMB2_Fields\Badge_Page_Course;
use BadgeFactor2\Admin\Courses_Admin;
use BadgeFactor2\Helpers\Constant;
use BadgeFactor2\Post_Types\BadgePage_Course_Extension;
use BadgeFactor2\Post_Types\Course;
use BadgeFactor2\Shortcodes\Courses;

/**
 * BadgeFactor 2 Courses Add-On class.
 */
class BF2_Courses {

	/**
	 * The single instance of the class.
	 *
	 * @var BF2_Courses
	 * @since 2.0.0-alpha
	 */
	protected static $_instance = null;

	/**
	 * Main BadgeFactor 2 Courses Add-On Instance.
	 *
	 * Ensures only one instance of BadgeFactor 2 Courses Add-On is loaded or can be loaded.
	 *
	 * @return BF2_Courses - Main instance.
	 * @since 1.0.0-alpha
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * BadgeFactor2 Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Badge Factor 2 Init Hooks.
	 *
	 * @return void
	 */
	public static function init_hooks() {
		BadgePage_Course_Extension::init_hooks();
		Course::init_hooks();
		Courses::init_hooks();
		Courses_Public::init_hooks();

		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			Badge_Page_Course::init_hooks();
			Courses_Admin::init_hooks();
		}
	}

	/**
	 * Define BadgeFactor2 Constants.
	 *
	 * @return void
	 */
	private function define_constants() {
		Constant::define( 'BF2_COURSES_DATA', get_plugin_data( BF2_COURSES_FILE ) );
	}

	/**
	 * Badge Factor 2 Includes.
	 *
	 * @return void
	 */
	public function includes() {
		require_once plugin_dir_path( __FILE__ ) . 'controllers/class-course-controller.php';
		require_once plugin_dir_path( __FILE__ ) . 'post-types/class-course.php';
		require_once plugin_dir_path( __FILE__ ) . 'post-types/class-badgepage-course-extension.php';
		require_once plugin_dir_path( __FILE__ ) . 'public/class-courses-public.php';
		require_once plugin_dir_path( __FILE__ ) . 'public/shortcodes/class-courses.php';

		// Admin / CLI classes.
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'admin/class-courses-admin.php';
			require_once plugin_dir_path( __FILE__ ) . 'admin/cmb2-fields/class-badge-page-course.php';
		}
	}

}
