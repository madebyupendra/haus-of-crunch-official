<?php
/**
 * Displayed when no products are found matching the current query
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/no-products-found.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="hoc-no-products-found">
	<p class="hoc-no-products-found__message">
		<?php esc_html_e( 'Nada! But don\'t worry, the fun doesn\'t stop here.', 'haus-of-crunch' ); ?>
	</p>
</div>

