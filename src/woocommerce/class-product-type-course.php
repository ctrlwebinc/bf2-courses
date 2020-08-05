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

/**
 * Course WooCommerce Product Type.
 */
class ProductTypeCourse extends \WC_Product {


	/**
	 * Constructor.
	 * @param int|WC_Product|object $product Product to init.
	 */
	public function __construct( $product ) {
		parent::__construct( $product );
		$this->product_type = $this->get_type();
	}

	/**
	 * Get internal type.
	 * @return string
	 */
	public function get_type() {
		return 'course';
	}
}
