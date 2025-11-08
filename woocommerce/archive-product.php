<?php
/**
 * The Template for displaying product archives (Shop page)
 *
 * @package Haus_of_Crunch
 */

defined('ABSPATH') || exit;

get_header('shop');
?>

<div class="hoc-shop-page">
  <div class="hoc-container">
    <!-- Mobile Filter Toggle Link -->
    <a href="#" class="hoc-filter-toggle" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle filters', 'haus-of-crunch'); ?>">
      <span class="hoc-filter-toggle__text"><?php esc_html_e('SHOP-BY', 'haus-of-crunch'); ?></span>
      <span class="hoc-filter-toggle__icon" aria-hidden="true">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </span>
    </a>

    <div class="hoc-shop-layout">
      
      <!-- Filter Panel Sidebar -->
      <aside class="hoc-shop-sidebar">
        <?php echo hoc_get_component('filter-panel'); ?>
      </aside>

      <!-- Main Product Grid -->
      <main class="hoc-shop-main">
        <?php
        // Show WooCommerce notices
        wc_print_notices();
        
        // Output product grid directly (no section wrapper needed here)
        echo hoc_get_component('product-grid');
        ?>
      </main>

    </div>
  </div>
</div>

<?php get_footer('shop'); ?>
