<?php
/**
 * Main header
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e('Skip to content', 'haus-of-crunch'); ?></a>
<div class="site">
  <?php
  // Announcement Bar (above header)
  get_template_part('components/announcement-bar');
  ?>
  <header class="hoc-header" role="banner">
    <div class="hoc-container hoc-header__inner">
      
      <!-- Site Branding -->
      <div class="hoc-header__branding">
        <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) : ?>
          <div class="hoc-header__logo-wrapper">
            <?php the_custom_logo(); ?>
          </div>
        <?php else : ?>
          <a href="<?php echo esc_url( home_url('/') ); ?>" class="hoc-header__title" rel="home">
            <?php bloginfo('name'); ?>
          </a>
        <?php endif; ?>
      </div>

      <!-- Desktop Navigation -->
      <nav class="hoc-header__nav" aria-label="<?php esc_attr_e('Main Navigation', 'haus-of-crunch'); ?>">
        <?php
        if ( has_nav_menu( 'primary' ) ) {
          wp_nav_menu( array(
            'theme_location' => 'primary',
            'container' => false,
            'menu_class' => 'hoc-header__menu',
            'items_wrap' => '<ul class="%2$s">%3$s</ul>',
            'fallback_cb' => false,
            'depth' => 2,
            'walker' => new HOC_Header_Menu_Walker(),
          ) );
        }
        ?>
      </nav>

      <!-- Header Actions (Cart & Mobile Menu Toggle) -->
      <div class="hoc-header__actions">
        <?php if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_cart_url' ) ) : ?>
          <!-- Shopping Cart -->
          <div class="hoc-header__cart">
            <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="hoc-header__cart-link" aria-label="<?php esc_attr_e('Shopping Cart', 'haus-of-crunch'); ?>">
              <svg class="hoc-header__cart-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7 4H5L3 20H21L19 4H17M7 4V2C7 1.44772 7.44772 1 8 1H16C16.5523 1 17 1.44772 17 2V4M7 4H17M9 8V12M15 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span class="hoc-header__cart-count-wrapper">
                <?php
                $cart_count = 0;
                if ( ! is_null( WC()->cart ) ) {
                  $cart_count = WC()->cart->get_cart_contents_count();
                }
                if ( $cart_count > 0 ) :
                  ?>
                  <span class="hoc-header__cart-count" aria-hidden="true">
                    <?php echo esc_html( $cart_count ); ?>
                  </span>
                  <span class="hoc-header__sr-only">
                    <?php 
                    /* translators: %d: number of items in cart */
                    printf( esc_html( _n( '%d item in cart', '%d items in cart', $cart_count, 'haus-of-crunch' ) ), $cart_count ); 
                    ?>
                  </span>
                  <?php
                endif;
                ?>
              </span>
            </a>
          </div>
        <?php endif; ?>

        <!-- Mobile Menu Toggle -->
        <button 
          class="hoc-header__menu-toggle" 
          aria-expanded="false" 
          aria-controls="hoc-mobile-menu"
          aria-label="<?php esc_attr_e('Toggle mobile menu', 'haus-of-crunch'); ?>"
        >
          <span class="hoc-header__menu-toggle-icon" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
          </span>
          <span class="hoc-header__sr-only"><?php esc_html_e('Menu', 'haus-of-crunch'); ?></span>
        </button>
      </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="hoc-header__mobile-menu" id="hoc-mobile-menu" aria-hidden="true">
      <div class="hoc-header__mobile-menu-inner">
        <?php
        if ( has_nav_menu( 'primary' ) ) {
          wp_nav_menu( array(
            'theme_location' => 'primary',
            'container' => false,
            'menu_class' => 'hoc-header__mobile-menu-list',
            'items_wrap' => '<ul class="%2$s">%3$s</ul>',
            'fallback_cb' => false,
            'depth' => 2,
            'walker' => new HOC_Mobile_Menu_Walker(),
          ) );
        }
        ?>
      </div>
    </div>
  </header>

  <main id="main-content" class="site-main" role="main">