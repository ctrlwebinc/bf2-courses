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
 */

namespace BadgeFactor2\Post_Types;


/**
 * Course post type.
 */
class Course {

	/**
	 * Custom post type's slug.
	 *
	 * @var string
	 */
	private static $slug = 'course';

	/**
	 * Custom post type's slug, pluralized.
	 *
	 * @var string
	 */
	private static $slug_plural = 'courses';


	/**
	 * Init hooks.
	 *
	 * @return void
	 */
	public static function init_hooks() {
		add_action( 'init', array( self::class, 'init' ), 10 );
		add_action( 'admin_init', array( self::class, 'add_capabilities' ), 11 );
		add_action( 'init', array( self::class, 'register_taxonomies' ), 10 );
		add_filter( 'post_updated_messages', array( self::class, 'updated_messages' ), 10 );
		add_action( 'cmb2_admin_init', array( self::class, 'register_cpt_metaboxes' ), 10 );

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_filter( 'product_type_selector', array( self::class, 'add_badge_product' ) );
		}
	}


	/**
	 * Registers the `badge_page` post type.
	 */
	public static function init() {

		// WooCommerce.
		require_once plugin_dir_path( __FILE__ ) . '../woocommerce/class-product-type-course.php';

		$plugin_data          = get_plugin_data( BF2_COURSES_FILE );
		$badgefactor2_options = get_option( 'bf2_courses_settings' );

		register_post_type(
			self::$slug,
			array(
				'labels'            => array(
					'name'                  => __( 'Courses', $plugin_data['TextDomain'] ),
					'singular_name'         => __( 'Course', $plugin_data['TextDomain'] ),
					'all_items'             => __( 'All Courses', $plugin_data['TextDomain'] ),
					'archives'              => __( 'Course Archives', $plugin_data['TextDomain'] ),
					'attributes'            => __( 'Course Attributes', $plugin_data['TextDomain'] ),
					'insert_into_item'      => __( 'Insert into Course', $plugin_data['TextDomain'] ),
					'uploaded_to_this_item' => __( 'Uploaded to this Course', $plugin_data['TextDomain'] ),
					'featured_image'        => _x( 'Featured Image', self::$slug, $plugin_data['TextDomain'] ),
					'set_featured_image'    => _x( 'Set featured image', self::$slug, $plugin_data['TextDomain'] ),
					'remove_featured_image' => _x( 'Remove featured image', self::$slug, $plugin_data['TextDomain'] ),
					'use_featured_image'    => _x( 'Use as featured image', self::$slug, $plugin_data['TextDomain'] ),
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
				'supports'          => array( 'title', 'editor', 'revisions' ),
				'has_archive'       => ( isset( $badgefactor2_options['bf2_courses_use_archive'] ) && 'on' === $badgefactor2_options['bf2_courses_use_archive'] ),
				'rewrite'           => array( 'slug' => 'courses' ),
				'query_var'         => true,
				'menu_position'     => 51,
				'menu_icon'         => BF2_BASEURL . 'assets/images/course.svg',
				'show_in_rest'      => false,
				'taxonomies'        => array( 'course-category', 'course-level', 'course-title' ),
				'capability_type'   => array( self::$slug, self::$slug_plural ),
				'map_meta_cap'      => true,
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

		$messages[ self::$slug ] = array(
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
	 * Add roles (capabilities) to custom post type.
	 *
	 * @return void
	 */
	public static function add_capabilities() {
		
		$capabilities = array(
			'read_' . self::$slug						=> array(
				'administrator',
			),
			'edit_' . self::$slug						=> array(
				'administrator',
			),
			'delete_' . self::$slug						=> array(
				'administrator',
			),
			'publish_' . self::$slug_plural				=> array(
				'administrator',
			),
			
			'edit_' . self::$slug_plural				=> array(
				'administrator',
			),
			'edit_others_' . self::$slug_plural			=> array(
				'administrator',
			),
			'edit_published_' . self::$slug_plural		=> array(
				'administrator',
			),
			'edit_private_' . self::$slug_plural		=> array(
				'administrator',
			),
			
			'read_private_' . self::$slug_plural		=> array(
				'administrator',
			),
			
			'delete_' . self::$slug_plural				=> array(
				'administrator',
			),
			'delete_others_' . self::$slug_plural		=> array(
				'administrator',
			),
			'delete_published_' . self::$slug_plural	=> array(
				'administrator',
			),
			'delete_private_' . self::$slug_plural		=> array(
				'administrator',
			),
			
			
		);

		foreach ( $capabilities as $capability => $roles ) {
			foreach ( $roles as $role ) {
				$role = get_role( $role );
				$role->add_cap( $capability, true );
			}
		}
		
	}


	/**
	 * Registers Add-On settings page.
	 */
	public static function register_cpt_metaboxes() {
		$plugin_data = get_plugin_data( BF2_COURSES_FILE );

		$cmb = new_cmb2_box(
			array(
				'id'           => 'course_links',
				'title'        => __( 'Links', $plugin_data['TextDomain'] ),
				'object_types' => array( self::$slug ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
				'capability'   => 'manage_badgr',
			)
		);

		$cmb->add_field(
			array(
				'id'         => 'course_badge_page',
				'name'       => __( 'Badge Page', $plugin_data['TextDomain'] ),
				'desc'       => __( 'Badge Page associated with this Course', $plugin_data['TextDomain'] ),
				'type'       => 'pw_select',
				'style'      => 'width: 200px',
				'options'    => BadgePage::select_options(),
				'attributes' => array(
					'required' => 'required',
				),
			)
		);

		if ( self::uses_duration() ) {
			$test = cmb2_get_metabox( 'course_info' );
			$cmb  = new_cmb2_box(
				array(
					'id'           => 'course_info',
					'title'        => __( 'Additional info', $plugin_data['TextDomain'] ),
					'object_types' => array( self::$slug ),
					'context'      => 'side',
					'priority'     => 'default',
					'show_names'   => true,
					'capability'   => 'manage_badgr',
				)
			);

			$cmb->add_field(
				array(
					'id'   => 'course_duration',
					'name' => __( 'Duration (in hours)', $plugin_data['TextDomain'] ),
					'type' => 'text_small',
					//'style'      => 'width: 200px',

				)
			);
		}
	}


	/**
	 * Register taxonomies.
	 *
	 * @return void
	 */
	public static function register_taxonomies() {
		$plugin_data = get_plugin_data( BF2_COURSES_FILE );

		register_taxonomy(
			'course-category',
			array( self::$slug ),
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
			array( self::$slug ),
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
			array( self::$slug ),
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
	}


	public static function all_by_category( $category = 0 ) {
		$args    = array(
			'post_type'   => self::$slug,
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

	/**
	 * Is the Course accessible by current user?
	 *
	 * @param int $course_id Course ID.
	 * @return boolean
	 */
	public static function is_accessible( $course_id ) {

		$user            = \wp_get_current_user();
		$product_id      = \get_post_meta( $course_id, 'course_product', true );
	   	$has_free_access = apply_filters( 'bf2_has_free_access', null );

		return $has_free_access || wc_customer_bought_product( $user->user_email, \get_current_user_id(), $product_id );
	}

	/**
	 * Is the Course purchasable by current user?
	 *
	 * @return boolean
	 */
	public static function is_purchasable( $course_id ) {

		return ! self::is_accessible( $course_id );
	}


	/**
	 * Undocumented function.
	 *
	 * @param array $types
	 * @return array
	 */
	public static function add_badge_product( $types ) {
		if ( is_array( $types ) ) {
			$plugin_data     = get_plugin_data( BF2_COURSES_FILE );
			$types['course'] = __( 'Course', $plugin_data['TextDomain'] );
		}

		return $types;
	}

	/**
	 * Are Courses using the Duration field?
	 *
	 * @return bool Whether or not Courses use the Duration field.
	 */
	public static function uses_duration() {
		$duration = cmb2_get_option( 'bf2_courses_settings', 'bf2_courses_duration_active' );
		return 'on' === $duration;
	}


	public static function get_by_badge_slug( $badge_slug ) {
		global $wpdb;

		$posts_table = $wpdb->prefix . 'posts';
		$postmeta_table = $wpdb->prefix . 'postmeta';

		$id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT p.ID 
				FROM {$posts_table} p 
				JOIN {$postmeta_table} pm 
				ON p.ID = pm.post_id 
				WHERE p.post_type = 'course' 
				AND pm.meta_key = 'course_badge_page'
				AND pm.meta_value IN (
					SELECT ID 
					FROM {$posts_table} p2
					JOIN {$postmeta_table} pm2 
					ON p2.ID = pm2.post_id
					WHERE p2.post_type = 'badge-page' 
					AND pm2.meta_key = 'badge'
					AND pm2.meta_value IN (
						SELECT pm3.meta_value 
						FROM {$posts_table} p3
						JOIN {$postmeta_table} pm3 
						ON p3.ID = pm3.post_id
						WHERE p3.post_name = %s
					)
				)
				LIMIT 1",
				$badge_slug
				
			)
		);
		return get_post( $id );

	}

	public static function select_options() {
		$args  = array(
			'post_type'   => self::$slug,
			'numberposts' => -1,
			'post_status' => 'publish',
		);

		$courses = get_posts( $args );

		$course_options = array();

		foreach ( $courses as $course ) {
			$course_options[ $course->ID ] = $course->post_title;
		}

		return $course_options;
	}
}
