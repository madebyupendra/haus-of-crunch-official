<?php
/**
 * Haus of Crunch - functions.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Include Menu Walker Classes
 */
require_once get_template_directory() . '/inc/class-header-menu-walker.php';
require_once get_template_directory() . '/inc/class-mobile-menu-walker.php';

/**
 * Enqueue styles & scripts
 */
function hoc_enqueue_assets() {
    $theme_dir = get_template_directory_uri();

    // CSS
    wp_enqueue_style( 'hoc-fonts', $theme_dir . '/assets/css/fonts.css', [], '0.1' );
    wp_enqueue_style( 'hoc-tokens', $theme_dir . '/assets/css/tokens.css', [], '0.1' );
    wp_enqueue_style( 'hoc-typography', $theme_dir . '/assets/css/typography.css', ['hoc-fonts', 'hoc-tokens'], '0.1' );
    wp_enqueue_style( 'hoc-base', $theme_dir . '/assets/css/base.css', ['hoc-typography'], '0.1' );

    // Components
    wp_enqueue_style( 'hoc-button', $theme_dir . '/assets/css/components/button.css', ['hoc-tokens'], null );
    wp_enqueue_style( 'hoc-container', $theme_dir . '/assets/css/components/container.css', ['hoc-tokens'], null );
    wp_enqueue_style( 'hoc-filter-panel', $theme_dir . '/assets/css/components/filter-panel.css', ['hoc-tokens'], null );
    wp_enqueue_style( 'hoc-section', $theme_dir . '/assets/css/components/section.css', ['hoc-tokens'], null );
    wp_enqueue_style( 'hoc-shop', $theme_dir . '/assets/css/components/shop.css', ['hoc-tokens'], null );
    wp_enqueue_style( 'hoc-product-card', $theme_dir . '/assets/css/components/product-card.css', ['hoc-tokens'], null );
    wp_enqueue_style( 'hoc-product-grid', $theme_dir . '/assets/css/components/product-grid.css', ['hoc-tokens'], null );
    wp_enqueue_style( 'hoc-single-product', $theme_dir . '/assets/css/components/single-product.css', ['hoc-tokens'], null );
    wp_enqueue_style( 'hoc-variation-selector', $theme_dir . '/assets/css/components/variation-selector.css', ['hoc-tokens'], null );
    wp_enqueue_style( 'hoc-product-gallery', $theme_dir . '/assets/css/components/product-gallery.css', ['hoc-tokens'], null );
    wp_enqueue_style( 'hoc-product-accordion', $theme_dir . '/assets/css/components/product-accordion.css', ['hoc-tokens'], null );
    wp_enqueue_style( 'hoc-hero', $theme_dir . '/assets/css/components/hero.css', ['hoc-tokens', 'hoc-typography'], null );
    wp_enqueue_style( 'hoc-category-highlights', $theme_dir . '/assets/css/components/category-highlights.css', ['hoc-tokens', 'hoc-typography'], null );
    wp_enqueue_style( 'hoc-featured-products', $theme_dir . '/assets/css/components/featured-products.css', ['hoc-tokens', 'hoc-typography', 'hoc-product-grid'], null );
    wp_enqueue_style( 'hoc-header', $theme_dir . '/assets/css/components/header.css', ['hoc-tokens', 'hoc-typography', 'hoc-base'], null );
    wp_enqueue_style( 'hoc-announcement-bar', $theme_dir . '/assets/css/components/announcement-bar.css', ['hoc-tokens', 'hoc-typography', 'hoc-base'], null );

    // Main stylesheet
    wp_enqueue_style( 'hoc-style', get_stylesheet_uri(), ['hoc-base'], '0.1' );

    // JS
    wp_enqueue_script( 'hoc-main', $theme_dir . '/assets/js/main.js', ['jquery'], '0.1', true );
    
    // Featured products carousel (only on front page)
    if ( is_front_page() ) {
        wp_enqueue_script( 'hoc-featured-products-carousel', $theme_dir . '/assets/js/featured-products-carousel.js', ['jquery'], '0.1', true );
        // Also load lazy loading script for product images on front page
        wp_enqueue_script( 'hoc-product-card-lazy', $theme_dir . '/assets/js/product-card-lazy.js', [], '0.1', true );
    }
    
    // Product card lazy loading (only on shop/archive pages)
    if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
        wp_enqueue_script( 'hoc-product-card-lazy', $theme_dir . '/assets/js/product-card-lazy.js', [], '0.1', true );
    }
    
    // Variation selector JS (only on single product pages)
    if ( is_product() ) {
        // Depend on WooCommerce's variation script if available
        $deps = ['jquery'];
        if ( wp_script_is( 'wc-add-to-cart-variation', 'registered' ) ) {
            $deps[] = 'wc-add-to-cart-variation';
        }
        wp_enqueue_script( 'hoc-variation-selector', $theme_dir . '/assets/js/variation-selector.js', $deps, '0.1', true );
        wp_enqueue_script( 'hoc-product-gallery', $theme_dir . '/assets/js/product-gallery.js', ['jquery'], '0.1', true );
        wp_enqueue_script( 'hoc-product-accordion', $theme_dir . '/assets/js/product-accordion.js', ['jquery'], '0.1', true );
    }
}
add_action( 'wp_enqueue_scripts', 'hoc_enqueue_assets' );

/**
 * Theme supports
 */
function hoc_theme_setup() {
    add_theme_support( 'title-tag' );
    
    // Custom logo support with flexible dimensions
    add_theme_support( 'custom-logo', array(
        'height'      => 40,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption'] );
    
    // Register navigation menus
    register_nav_menus([
        'primary' => __( 'Primary Menu', 'haus-of-crunch' ),
        'footer'  => __( 'Footer Menu', 'haus-of-crunch' ),
    ]);
}
add_action( 'after_setup_theme', 'hoc_theme_setup' );

/**
 * Filter custom logo image attributes to ensure proper sizing
 * This is cleaner than using !important in CSS
 */
function hoc_custom_logo_image_attributes( $attr, $attachment, $size ) {
    // Only apply to custom logo
    if ( isset( $attr['class'] ) && strpos( $attr['class'], 'custom-logo' ) !== false ) {
        // Remove any inline width/height that WordPress might add
        unset( $attr['width'] );
        unset( $attr['height'] );
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'hoc_custom_logo_image_attributes', 10, 3 );

/**
 * Enable WooCommerce cart fragments for real-time cart updates
 */
function hoc_woocommerce_cart_fragments( $fragments ) {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return $fragments;
    }

    $cart_count = 0;
    if ( ! is_null( WC()->cart ) ) {
        $cart_count = WC()->cart->get_cart_contents_count();
    }

    // Update cart count wrapper in header
    ob_start();
    ?>
    <?php if ( $cart_count > 0 ) : ?>
        <span class="hoc-header__cart-count" aria-hidden="true">
            <?php echo esc_html( $cart_count ); ?>
        </span>
        <span class="hoc-header__sr-only">
            <?php 
            /* translators: %d: number of items in cart */
            printf( esc_html( _n( '%d item in cart', '%d items in cart', $cart_count, 'haus-of-crunch' ) ), $cart_count ); 
            ?>
        </span>
    <?php endif; ?>
    <?php
    $fragments['.hoc-header__cart-count-wrapper'] = ob_get_clean();

    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'hoc_woocommerce_cart_fragments' );

/**
 * Helper: Load component template
 * 
 * @param string $name Component name (without .php extension)
 * @param array  $args  Arguments to pass to component
 * @return string Component output
 */
function hoc_get_component($name, $args = []) {
    // Sanitize component name to prevent directory traversal
    $name = sanitize_file_name($name);
    
    ob_start();
    // Pass args directly to template via $args variable
    // Components should access via $args['key'] instead of extract()
    set_query_var('component_args', $args);
    get_template_part('components/' . $name);
    return ob_get_clean();
}


/**
 * Custom WooCommerce Shop Loop Wrapper
 * Outputs the product grid using Haus of Crunch markup
 */
function hoc_custom_shop_loop() {
    ob_start();

    if ( woocommerce_product_loop() ) {
        do_action( 'woocommerce_before_shop_loop' );

        echo '<ul class="hoc-product-grid">';

        while ( have_posts() ) {
            the_post();
            wc_get_template_part( 'content', 'product' );
        }

        echo '</ul>';

        do_action( 'woocommerce_after_shop_loop' );
    } else {
        do_action( 'woocommerce_no_products_found' );
    }

    return ob_get_clean();
}

/**
 * Remove WooCommerce result count and sorting panel from shop page
 * Creates a clean shop page without result count or sorting dropdown
 */
function hoc_remove_shop_result_count_and_sorting() {
    if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
    }
}
add_action( 'wp', 'hoc_remove_shop_result_count_and_sorting' );

/**
 * Remove WooCommerce default link wrappers from product loop
 * Since we're using our own custom product card structure
 */
function hoc_remove_woocommerce_product_link_wrappers() {
    remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
}
add_action( 'woocommerce_before_shop_loop', 'hoc_remove_woocommerce_product_link_wrappers', 5 );

/**
 * Use uncropped product thumbnails to preserve original aspect ratio
 */
function hoc_woocommerce_thumbnail_size( $size ) {
    // Use larger size to preserve quality and aspect ratio
    return 'woocommerce_single'; // Use single product image size (uncropped, larger)
}
add_filter( 'single_product_archive_thumbnail_size', 'hoc_woocommerce_thumbnail_size' );

/**
 * Override WooCommerce thumbnail to use uncropped images
 */
function hoc_woocommerce_get_image_size_thumbnail( $size ) {
    // Set to uncropped to preserve aspect ratio
    return array(
        'width'  => 600,
        'height' => 0, // 0 means no height constraint, preserves aspect ratio
        'crop'   => 0, // 0 means no cropping
    );
}
add_filter( 'woocommerce_get_image_size_thumbnail', 'hoc_woocommerce_get_image_size_thumbnail' );

/**
 * Add lazy loading to WooCommerce product images
 */
function hoc_add_lazy_loading_to_product_images( $attr, $attachment, $size ) {
    // Only add lazy loading to shop/archive pages
    // Front page featured products should load eagerly
    if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    } elseif ( is_front_page() ) {
        // Disable lazy loading on front page for featured products
        $attr['loading'] = 'eager';
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'hoc_add_lazy_loading_to_product_images', 10, 3 );

/**
 * Add lazy loading to WooCommerce product loop images
 * This ensures WooCommerce's product thumbnail function also gets lazy loading
 */
function hoc_add_lazy_loading_to_woocommerce_images( $html, $post_id ) {
    // Only add lazy loading to shop/archive pages
    if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
        // Add loading="lazy" if not already present
        if ( strpos( $html, 'loading=' ) === false ) {
            $html = str_replace( '<img', '<img loading="lazy" decoding="async"', $html );
        }
    }
    return $html;
}
add_filter( 'woocommerce_product_get_image', 'hoc_add_lazy_loading_to_woocommerce_images', 10, 2 );

/**
 * Enable WooCommerce layered nav filters for attribute filtering
 * This ensures filter_pa_* query vars are processed correctly
 */
function hoc_enable_woocommerce_attribute_filtering( $query ) {
    if ( ! is_admin() && $query->is_main_query() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
        // Set query var to enable WooCommerce product query processing
        $query->set( 'wc_query', 'product_query' );
        
        // Ensure attribute filters are processed
        foreach ( $_GET as $key => $value ) {
            if ( strpos( $key, 'filter_pa_' ) === 0 ) {
                $query->set( $key, sanitize_text_field( $value ) );
            }
        }
    }
}
add_action( 'pre_get_posts', 'hoc_enable_woocommerce_attribute_filtering', 20 );

/**
 * Add tax_query to filter products by attributes
 * This handles the actual filtering when filter_pa_* parameters are in the URL
 */
function hoc_filter_products_by_attributes( $query ) {
    if ( ! is_admin() && $query->is_main_query() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
        $attribute_filters = array();
        
        // Process all filter_pa_* parameters
        foreach ( $_GET as $key => $value ) {
            if ( strpos( $key, 'filter_pa_' ) === 0 ) {
                $taxonomy = str_replace( 'filter_', '', $key );
                
                if ( taxonomy_exists( $taxonomy ) && ! empty( $value ) ) {
                    // Handle comma-separated values (multiple selections)
                    $terms = explode( ',', sanitize_text_field( $value ) );
                    $terms = array_map( 'trim', $terms );
                    $terms = array_filter( $terms );
                    
                    if ( ! empty( $terms ) ) {
                        $attribute_filters[] = array(
                            'taxonomy' => $taxonomy,
                            'field'    => 'slug',
                            'terms'    => $terms,
                            'operator' => 'IN',
                        );
                    }
                }
            }
        }
        
        // Add attribute filters to tax_query if we have any
        if ( ! empty( $attribute_filters ) ) {
            $existing_tax_query = $query->get( 'tax_query' );
            
            if ( ! is_array( $existing_tax_query ) ) {
                $existing_tax_query = array();
            }
            
            // If there's an existing tax_query, we need to merge properly
            if ( ! empty( $existing_tax_query ) ) {
                // Check if relation is set
                $relation = isset( $existing_tax_query['relation'] ) ? $existing_tax_query['relation'] : 'AND';
                
                // Remove relation from array to get just the conditions
                $conditions = array();
                foreach ( $existing_tax_query as $key => $value ) {
                    if ( $key !== 'relation' && is_array( $value ) ) {
                        $conditions[] = $value;
                    }
                }
                
                // Merge with our attribute filters
                $all_conditions = array_merge( $conditions, $attribute_filters );
                
                // Build final tax_query with relation
                $tax_query = array(
                    'relation' => $relation,
                );
                $tax_query = array_merge( $tax_query, $all_conditions );
            } else {
                // No existing tax_query, just use our filters
                $tax_query = $attribute_filters;
            }
            
            $query->set( 'tax_query', $tax_query );
        }
    }
}
add_action( 'pre_get_posts', 'hoc_filter_products_by_attributes', 30 );

/**
 * Change currency symbol from රු to LKR
 */
function hoc_change_currency_symbol( $currency_symbol, $currency ) {
    if ( $currency === 'LKR' ) {
        return 'LKR';
    }
    return $currency_symbol;
}
add_filter( 'woocommerce_currency_symbol', 'hoc_change_currency_symbol', 10, 2 );

/**
 * Custom Variable Product Add to Cart
 * 
 * Replaces WooCommerce's default variable product template with our custom
 * button-based variation selector component
 * 
 * @package Haus_of_Crunch
 */
function hoc_custom_variable_add_to_cart() {
    global $product;

    if ( ! ( $product instanceof WC_Product ) || ! $product->is_type( 'variable' ) ) {
        return;
    }

    // Enqueue variation scripts (required for WooCommerce variation functionality)
    wp_enqueue_script( 'wc-add-to-cart-variation' );

    // Get available variations
    $get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
    $available_variations = $get_variations ? $product->get_available_variations() : false;
    $attributes = $product->get_variation_attributes();
    $selected_attributes = $product->get_default_attributes();

    // Prepare variation data for JavaScript (same as WooCommerce default)
    $attribute_keys = array_keys( $attributes );
    $variations_json = wp_json_encode( $available_variations );
    $variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

    do_action( 'woocommerce_before_add_to_cart_form' ); 
    ?>

    <form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
        <?php do_action( 'woocommerce_before_variations_form' ); ?>

        <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
            <p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
        <?php else : ?>
            <div class="variations" role="presentation">
                <?php foreach ( $attributes as $attribute_name => $options ) : ?>
                    <?php
                    // Get size guide URL if attribute is "pa_size" (can be customized)
                    $size_guide_url = '';
                    if ( $attribute_name === 'pa_size' || strpos( $attribute_name, 'size' ) !== false ) {
                        // You can customize this URL or get it from product meta
                        $size_guide_url = get_post_meta( $product->get_id(), '_size_guide_url', true );
                        if ( ! $size_guide_url ) {
                            // Default to a page or external link
                            // $size_guide_url = get_permalink( get_option( 'woocommerce_size_guide_page_id' ) );
                        }
                    }

                    // Use custom variation selector component
                    get_template_part( 'components/variation-selector', null, [
                        'attribute'      => $attribute_name,
                        'options'        => $options,
                        'product'        => $product,
                        'selected'       => false,
                        'label'          => '', // Will use attribute label by default
                        'size_guide_url' => $size_guide_url,
                    ] );
                    ?>

                    <?php
                    // Add reset link after last attribute (if needed)
                    if ( end( $attribute_keys ) === $attribute_name ) {
                        echo wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#" aria-label="' . esc_attr__( 'Clear options', 'woocommerce' ) . '">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) );
                    }
                    ?>
                <?php endforeach; ?>
            </div>
            <div class="reset_variations_alert screen-reader-text" role="alert" aria-live="polite" aria-relevant="all"></div>
            <?php do_action( 'woocommerce_after_variations_table' ); ?>

            <div class="single_variation_wrap">
                <?php
                /**
                 * Hook: woocommerce_before_single_variation.
                 */
                do_action( 'woocommerce_before_single_variation' );

                /**
                 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
                 *
                 * @since 2.4.0
                 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
                 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
                 */
                do_action( 'woocommerce_single_variation' );

                /**
                 * Hook: woocommerce_after_single_variation.
                 */
                do_action( 'woocommerce_after_single_variation' );
                ?>
            </div>
        <?php endif; ?>

        <?php do_action( 'woocommerce_after_variations_form' ); ?>
    </form>

    <?php
    do_action( 'woocommerce_after_add_to_cart_form' );
}

// Remove default WooCommerce function and add our custom one
remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
add_action( 'woocommerce_variable_add_to_cart', 'hoc_custom_variable_add_to_cart', 30 );

/**
 * Replace default WooCommerce product gallery with custom ProductGallery component
 */
function hoc_replace_product_gallery() {
    if ( is_product() ) {
        // Remove default WooCommerce product images
        remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
    }
}
add_action( 'wp', 'hoc_replace_product_gallery' );

/**
 * Hide quantity input and replace add to cart button with custom button component
 * Uses output buffering to intercept and replace the form content
 */
function hoc_customize_add_to_cart() {
    if ( ! is_product() ) {
        return;
    }

    // Replace quantity input with hidden field
    add_action( 'woocommerce_before_add_to_cart_quantity', 'hoc_replace_quantity_input', 5 );
    add_action( 'woocommerce_after_add_to_cart_quantity', 'hoc_output_hidden_quantity', 999 );
    
    // Replace button using output buffering
    add_action( 'woocommerce_before_add_to_cart_button', 'hoc_start_button_capture', 5 );
    add_action( 'woocommerce_after_add_to_cart_button', 'hoc_replace_button_output', 999 );
}

/**
 * Start capturing quantity input to replace it
 */
function hoc_replace_quantity_input() {
    ob_start();
}

/**
 * Replace captured quantity input with hidden field
 */
function hoc_output_hidden_quantity() {
    global $product;
    if ( ! $product ) {
        return;
    }
    
    // Discard captured quantity input
    ob_end_clean();
    
    // Output hidden quantity field
    $min_value = $product->get_min_purchase_quantity();
    $quantity = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $min_value;
    echo '<input type="hidden" name="quantity" value="' . esc_attr( $quantity ) . '" />';
}

/**
 * Start capturing button output
 */
function hoc_start_button_capture() {
    ob_start();
}

/**
 * Replace captured button with custom button component
 */
function hoc_replace_button_output() {
    global $product;
    if ( ! $product ) {
        return;
    }
    
    // Get and discard captured button HTML
    ob_get_clean();
    
    // Get product ID
    $product_id = $product->get_id();
    
    // Output our custom button component
    get_template_part( 'components/button', null, [
        'label'   => $product->single_add_to_cart_text(),
        'variant' => 'primary',
        'size'    => 'lg',
        'type'    => 'submit',
        'name'    => 'add-to-cart',
        'value'   => $product_id,
        'class'   => 'single_add_to_cart_button',
        'disabled'=> false,
    ] );
}
add_action( 'wp', 'hoc_customize_add_to_cart' );

/**
 * Remove default product meta from priority 40 and reposition it
 */
function hoc_reposition_product_meta() {
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    // Add product meta at priority 30, but it will run after shipping info since we register it later
    add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 30 );
}
add_action( 'init', 'hoc_reposition_product_meta', 20 );

/**
 * Add shipping information block after add to cart button
 * This runs before product meta since it's registered first
 */
function hoc_shipping_info_block() {
    if ( ! is_product() ) {
        return;
    }
    ?>
    <div class="hoc-shipping-info">
        <h3 class="hoc-shipping-info__heading">Shipping**</h3>
        <p class="hoc-shipping-info__text">You'll see our shipping options at checkout.</p>
    </div>
    <?php
}
add_action( 'woocommerce_single_product_summary', 'hoc_shipping_info_block', 30 );

/**
 * Add product accordion to single product summary
 * Displays after the product meta (priority 31)
 */
function hoc_product_accordion() {
    if ( ! is_product() ) {
        return;
    }
    get_template_part( 'components/product-accordion' );
}
add_action( 'woocommerce_single_product_summary', 'hoc_product_accordion', 31 );

/**
 * Add Shipping & Delivery custom field to WooCommerce product editor
 */
function hoc_add_shipping_delivery_field() {
    global $woocommerce, $post;
    
    echo '<div class="options_group">';
    
    woocommerce_wp_textarea_input(
        array(
            'id'          => '_shipping_delivery',
            'label'       => __('Shipping & Delivery', 'haus-of-crunch'),
            'placeholder' => __('Enter shipping and delivery information...', 'haus-of-crunch'),
            'desc_tip'    => true,
            'description' => __('This information will be displayed on the product page in an accordion section.', 'haus-of-crunch'),
        )
    );
    
    echo '</div>';
}
add_action('woocommerce_product_options_shipping_product_data', 'hoc_add_shipping_delivery_field');

/**
 * Save Shipping & Delivery custom field
 */
function hoc_save_shipping_delivery_field($post_id) {
    $shipping_delivery = isset($_POST['_shipping_delivery']) ? wp_kses_post($_POST['_shipping_delivery']) : '';
    update_post_meta($post_id, '_shipping_delivery', $shipping_delivery);
}
add_action('woocommerce_process_product_meta', 'hoc_save_shipping_delivery_field');

/**
 * Add shipping & delivery accordion to single product summary
 * Displays after the description accordion (priority 32)
 */
function hoc_product_shipping_accordion() {
    if ( ! is_product() ) {
        return;
    }
    get_template_part( 'components/product-shipping-accordion' );
}
add_action( 'woocommerce_single_product_summary', 'hoc_product_shipping_accordion', 32 );

/**
 * Remove WooCommerce default product tabs (Description, Additional Information, Reviews)
 * Since we're using accordions for Description and Shipping & Delivery
 */
function hoc_remove_product_tabs( $tabs ) {
    unset( $tabs['description'] );
    unset( $tabs['additional_information'] );
    unset( $tabs['reviews'] );
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'hoc_remove_product_tabs', 98 );

/**
 * Remove WooCommerce default brand output with "posted_in" class
 * We're displaying brand with bullet point in our custom meta template
 */
function hoc_remove_default_brand_output() {
    // Filter the brand output to empty string to prevent display
    add_filter( 'woocommerce_product_brands_output', '__return_empty_string', 999 );
}
add_action( 'init', 'hoc_remove_default_brand_output', 20 );

/**
 * WordPress Customizer - Home Page Hero Section
 */
function hoc_customize_register( $wp_customize ) {
    // Add "Home Page" panel
    $wp_customize->add_panel( 'hoc_home_page', array(
        'title'    => __( 'Home Page', 'haus-of-crunch' ),
        'priority' => 30,
    ) );

    // Add "Hero" section under Home Page panel
    $wp_customize->add_section( 'hoc_hero', array(
        'title'    => __( 'Hero', 'haus-of-crunch' ),
        'panel'    => 'hoc_home_page',
        'priority' => 10,
    ) );

    // Hero Image Setting
    $wp_customize->add_setting( 'hoc_hero_image', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );

    // Hero Image Control
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'hoc_hero_image', array(
        'label'       => __( 'Hero Image', 'haus-of-crunch' ),
        'section'     => 'hoc_hero',
        'mime_type'   => 'image',
        'priority'    => 10,
    ) ) );

    // Hero Title Setting
    $wp_customize->add_setting( 'hoc_hero_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );

    // Hero Title Control
    $wp_customize->add_control( 'hoc_hero_title', array(
        'label'    => __( 'Hero Text Title', 'haus-of-crunch' ),
        'section'  => 'hoc_hero',
        'type'     => 'text',
        'priority' => 20,
    ) );

    // Hero Link Setting
    $wp_customize->add_setting( 'hoc_hero_link', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ) );

    // Hero Link Control
    $wp_customize->add_control( 'hoc_hero_link', array(
        'label'       => __( 'Hero Title Link', 'haus-of-crunch' ),
        'section'     => 'hoc_hero',
        'type'        => 'url',
        'priority'    => 30,
        'description' => __( 'Optional: Add a URL to make the hero title clickable', 'haus-of-crunch' ),
    ) );

    // Add "Category Highlights" section under Home Page panel
    $wp_customize->add_section( 'hoc_category_highlights', array(
        'title'    => __( 'Category Highlights', 'haus-of-crunch' ),
        'panel'    => 'hoc_home_page',
        'priority' => 20,
    ) );

    // Get WooCommerce product categories for dropdown
    $product_categories = array( '' => __( '— Select Category —', 'haus-of-crunch' ) );
    if ( taxonomy_exists( 'product_cat' ) ) {
        $categories = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ) );
        if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
            foreach ( $categories as $category ) {
                $product_categories[ $category->term_id ] = $category->name;
            }
        }
    }

    // Column 1 Category Setting
    $wp_customize->add_setting( 'hoc_category_highlights_column1_category', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );

    // Column 1 Category Control
    $wp_customize->add_control( 'hoc_category_highlights_column1_category', array(
        'label'    => __( 'Column 1: Category', 'haus-of-crunch' ),
        'section'  => 'hoc_category_highlights',
        'type'     => 'select',
        'choices'  => $product_categories,
        'priority' => 10,
    ) );

    // Column 1 Title Setting
    $wp_customize->add_setting( 'hoc_category_highlights_column1_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );

    // Column 1 Title Control
    $wp_customize->add_control( 'hoc_category_highlights_column1_title', array(
        'label'    => __( 'Column 1: Text Title', 'haus-of-crunch' ),
        'section'  => 'hoc_category_highlights',
        'type'     => 'text',
        'priority' => 20,
    ) );

    // Column 2 Category Setting
    $wp_customize->add_setting( 'hoc_category_highlights_column2_category', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );

    // Column 2 Category Control
    $wp_customize->add_control( 'hoc_category_highlights_column2_category', array(
        'label'    => __( 'Column 2: Category', 'haus-of-crunch' ),
        'section'  => 'hoc_category_highlights',
        'type'     => 'select',
        'choices'  => $product_categories,
        'priority' => 30,
    ) );

    // Column 2 Title Setting
    $wp_customize->add_setting( 'hoc_category_highlights_column2_title', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );

    // Column 2 Title Control
    $wp_customize->add_control( 'hoc_category_highlights_column2_title', array(
        'label'    => __( 'Column 2: Text Title', 'haus-of-crunch' ),
        'section'  => 'hoc_category_highlights',
        'type'     => 'text',
        'priority' => 40,
    ) );

    // Add "Featured Products" section under Home Page panel
    $wp_customize->add_section( 'hoc_featured_products', array(
        'title'    => __( 'Featured Products', 'haus-of-crunch' ),
        'panel'    => 'hoc_home_page',
        'priority' => 30,
    ) );

    // Featured Products Title Setting
    $wp_customize->add_setting( 'hoc_featured_products_title', array(
        'default'           => 'Featured Products',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );

    // Featured Products Title Control
    $wp_customize->add_control( 'hoc_featured_products_title', array(
        'label'       => __( 'Section Title', 'haus-of-crunch' ),
        'section'     => 'hoc_featured_products',
        'type'        => 'text',
        'priority'    => 10,
        'description' => __( 'Title displayed above the featured products grid', 'haus-of-crunch' ),
    ) );

    // Featured Products Limit Setting
    $wp_customize->add_setting( 'hoc_featured_products_limit', array(
        'default'           => 8,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );

    // Featured Products Limit Control
    $wp_customize->add_control( 'hoc_featured_products_limit', array(
        'label'       => __( 'Number of Products', 'haus-of-crunch' ),
        'section'     => 'hoc_featured_products',
        'type'        => 'number',
        'priority'    => 20,
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 50,
            'step' => 1,
        ),
        'description' => __( 'Number of featured products to display (max 50)', 'haus-of-crunch' ),
    ) );

    // Add "Announcement Bar" section
    $wp_customize->add_section( 'hoc_announcement_bar', array(
        'title'    => __( 'Announcement Bar', 'haus-of-crunch' ),
        'priority' => 25,
    ) );

    // Announcement Bar Enabled Setting
    $wp_customize->add_setting( 'hoc_announcement_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ) );

    // Announcement Bar Enabled Control
    $wp_customize->add_control( 'hoc_announcement_enabled', array(
        'label'    => __( 'Enable Announcement Bar', 'haus-of-crunch' ),
        'section'  => 'hoc_announcement_bar',
        'type'     => 'checkbox',
        'priority' => 10,
    ) );

    // Announcement Bar Text Setting
    $wp_customize->add_setting( 'hoc_announcement_text', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );

    // Announcement Bar Text Control
    $wp_customize->add_control( 'hoc_announcement_text', array(
        'label'       => __( 'Announcement Text', 'haus-of-crunch' ),
        'section'     => 'hoc_announcement_bar',
        'type'        => 'text',
        'priority'    => 20,
        'description' => __( 'Enter the announcement message to display', 'haus-of-crunch' ),
    ) );

    // Announcement Bar Link Setting
    $wp_customize->add_setting( 'hoc_announcement_link', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ) );

    // Announcement Bar Link Control
    $wp_customize->add_control( 'hoc_announcement_link', array(
        'label'       => __( 'Announcement Link (Optional)', 'haus-of-crunch' ),
        'section'     => 'hoc_announcement_bar',
        'type'        => 'url',
        'priority'    => 30,
        'description' => __( 'Optional: Add a URL to make the announcement clickable', 'haus-of-crunch' ),
    ) );

    // Announcement Bar Dismissible Setting
    $wp_customize->add_setting( 'hoc_announcement_dismissible', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ) );

    // Announcement Bar Dismissible Control
    $wp_customize->add_control( 'hoc_announcement_dismissible', array(
        'label'       => __( 'Allow Users to Dismiss', 'haus-of-crunch' ),
        'section'     => 'hoc_announcement_bar',
        'type'        => 'checkbox',
        'priority'    => 40,
        'description' => __( 'Show a close button so users can dismiss the announcement', 'haus-of-crunch' ),
    ) );

    // Announcement Bar Background Color Setting
    $wp_customize->add_setting( 'hoc_announcement_bg_color', array(
        'default'           => '#111111',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ) );

    // Announcement Bar Background Color Control
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hoc_announcement_bg_color', array(
        'label'    => __( 'Background Color', 'haus-of-crunch' ),
        'section'  => 'hoc_announcement_bar',
        'priority' => 50,
    ) ) );

    // Announcement Bar Text Color Setting
    $wp_customize->add_setting( 'hoc_announcement_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ) );

    // Announcement Bar Text Color Control
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hoc_announcement_text_color', array(
        'label'    => __( 'Text Color', 'haus-of-crunch' ),
        'section'  => 'hoc_announcement_bar',
        'priority' => 60,
    ) ) );
}
add_action( 'customize_register', 'hoc_customize_register' );
