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
 * Plugin URI: https://github.com/ctrlwebinc/bf2-courses
 * GitHub Plugin URI: https://github.com/ctrlwebinc/bf2-courses
 * Description: Adds Courses to Badge Factor 2, and if WooCommerce is installed, adds Courses to WooCommerce as well.
 * Author: ctrlweb
 * Version: 1.0.0
 * Author URI: https://badgefactor2.com/
 * License: GNU AGPL
 * Text Domain: bf2-courses
 * Domain Path: /languages
 *
 * @phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain
 */

use BadgeFactor2\BF2_Courses;

defined( 'ABSPATH' ) || exit;

load_plugin_textdomain( 'bf2-courses', false, basename( dirname( __FILE__ ) ) . '/languages' );

// Define BF2_FILE.
if ( ! defined( 'BF2_COURSES_FILE' ) ) {
	define( 'BF2_COURSES_FILE', __FILE__ );
}

// Deactivate if BadgeFactor2 is not active.
if ( ! class_exists( 'BadgeFactor2\BadgeFactor2' ) || ! \BadgeFactor2\BadgeFactor2::is_initialized() ) {
	deactivate_plugins( plugin_dir_path( __FILE__ ) . '/bf2-courses.php' );
	die( __( 'This plugin requires Badge Factor 2.', 'bf2-courses' ) );
}

// Include the main BF2 Courses class.
if ( ! class_exists( 'BF2_Courses' ) ) {
	require_once dirname( __FILE__ ) . '/src/class-bf2-courses.php';
}

/**
 * Returns the main instance of BadgeFactor2 Courses Add-On.
 *
 * @since  2.0.0-alpha
 * @return BF2_Courses
 */
function bf2_courses() {
	return BF2_Courses::instance();
}

// Global for backwards compatibility.
$GLOBALS['badgefactor2']->courses = bf2_courses();

