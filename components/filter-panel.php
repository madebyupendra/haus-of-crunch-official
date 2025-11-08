<?php
/**
 * Filter Panel Component
 * 
 * Fully dynamic filter panel that automatically detects and displays
 * all product attributes, categories, and brands
 * Inspired by Undefeated.com design
 *
 * @package Haus_of_Crunch
 */

defined('ABSPATH') || exit;

// Get args from query vars (set by hoc_get_component) or direct $args
$component_args = get_query_var('component_args', []);
$args = !empty($args) ? $args : $component_args;

// Build base URL for filters
if (is_shop() || is_product_category() || is_product_tag()) {
    $current_url = home_url(add_query_arg(array(), $_SERVER['REQUEST_URI']));
} else {
    $current_url = wc_get_page_permalink('shop');
}

// Helper function to get all active filter params
function hoc_get_active_filters() {
    $filters = array();
    
    // Category
    if (isset($_GET['product_cat'])) {
        $filters['product_cat'] = sanitize_text_field($_GET['product_cat']);
    }
    
    // Brand (check both possible taxonomies)
    if (isset($_GET['product_brand'])) {
        $filters['product_brand'] = sanitize_text_field($_GET['product_brand']);
    } elseif (isset($_GET['filter_pa_brand'])) {
        $filters['filter_pa_brand'] = sanitize_text_field($_GET['filter_pa_brand']);
    }
    
    // All product attributes (pa_*)
    foreach ($_GET as $key => $value) {
        if (strpos($key, 'filter_pa_') === 0) {
            $filters[$key] = sanitize_text_field($value);
        }
    }
    
    return $filters;
}

// Helper function to build filter URL
function hoc_build_filter_url($base_url, $filter_key, $filter_value, $is_active = false) {
    $url = $base_url;
    $active_filters = hoc_get_active_filters();
    
    // Remove the specific filter key we're working with
    $url = remove_query_arg($filter_key, $url);
    
    // If not active, add it
    if (!$is_active && !empty($filter_value)) {
        $url = add_query_arg($filter_key, $filter_value, $url);
    }
    
    // Preserve all other active filters
    foreach ($active_filters as $key => $value) {
        if ($key !== $filter_key && !empty($value)) {
            $url = add_query_arg($key, $value, $url);
        }
    }
    
    return $url;
}

// Get product categories (COLLECTIONS)
$categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
    'parent' => 0,
));

// Get all WooCommerce product attributes dynamically
$product_attributes = array();
if (function_exists('wc_get_attribute_taxonomies')) {
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    
    foreach ($attribute_taxonomies as $attribute) {
        $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
        
        if (taxonomy_exists($taxonomy)) {
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
            ));
            
            if (!empty($terms) && !is_wp_error($terms)) {
                // Special handling for size - sort numerically
                if ($attribute->attribute_name === 'size') {
                    usort($terms, function($a, $b) {
                        $a_num = is_numeric($a->name) ? floatval($a->name) : PHP_INT_MAX;
                        $b_num = is_numeric($b->name) ? floatval($b->name) : PHP_INT_MAX;
                        
                        if ($a_num !== PHP_INT_MAX && $b_num !== PHP_INT_MAX) {
                            return $a_num <=> $b_num;
                        }
                        return strcmp($a->name, $b->name);
                    });
                } else {
                    // Sort alphabetically for other attributes
                    usort($terms, function($a, $b) {
                        return strcmp($a->name, $b->name);
                    });
                }
                
                $product_attributes[$attribute->attribute_name] = array(
                    'label' => $attribute->attribute_label,
                    'name' => $attribute->attribute_name,
                    'taxonomy' => $taxonomy,
                    'terms' => $terms,
                    'is_grid' => false, // All attributes use list layout
                );
            }
        }
    }
}

// Get brand terms (check both taxonomies)
$brand_terms = get_terms(array(
    'taxonomy' => 'product_brand',
    'hide_empty' => true,
    'orderby' => 'name',
));

if (is_wp_error($brand_terms) || empty($brand_terms)) {
    $brand_terms = get_terms(array(
        'taxonomy' => 'pa_brand',
        'hide_empty' => true,
        'orderby' => 'name',
    ));
    $brand_taxonomy = 'pa_brand';
    $brand_filter_key = 'filter_pa_brand';
} else {
    $brand_taxonomy = 'product_brand';
    $brand_filter_key = 'product_brand';
}

// Get all active filters
$active_filters = hoc_get_active_filters();
$has_active_filters = !empty($active_filters);

// Build clear URL
$clear_url = $current_url;
foreach (array_keys($active_filters) as $key) {
    $clear_url = remove_query_arg($key, $clear_url);
}
?>

<aside class="hoc-filter-panel" role="complementary" aria-label="<?php esc_attr_e('Product Filters', 'haus-of-crunch'); ?>">
    
    <?php // COLLECTIONS Section ?>
    <?php if (!empty($categories) && !is_wp_error($categories)) : ?>
        <div class="hoc-filter-panel__section">
            <h3 class="hoc-filter-panel__title"><?php esc_html_e('COLLECTIONS', 'haus-of-crunch'); ?></h3>
            <ul class="hoc-filter-panel__list">
                <?php 
                // "All" link - resets all filters
                // "All" is active when no filters are applied
                $is_all_active = !$has_active_filters;
                ?>
                <li class="hoc-filter-panel__item">
                    <a href="<?php echo esc_url($clear_url); ?>" 
                       class="hoc-filter-panel__link <?php echo $is_all_active ? 'is-active' : ''; ?>"
                       data-filter="all"
                       data-value="">
                        <?php esc_html_e('ALL', 'haus-of-crunch'); ?>
                    </a>
                </li>
                <?php foreach ($categories as $category) : 
                    $is_active = isset($active_filters['product_cat']) && $active_filters['product_cat'] === $category->slug;
                    $filter_url = hoc_build_filter_url($current_url, 'product_cat', $category->slug, $is_active);
                ?>
                    <li class="hoc-filter-panel__item">
                        <a href="<?php echo esc_url($filter_url); ?>" 
                           class="hoc-filter-panel__link <?php echo $is_active ? 'is-active' : ''; ?>"
                           data-filter="category"
                           data-value="<?php echo esc_attr($category->slug); ?>">
                            <?php echo esc_html(strtoupper($category->name)); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                
                <?php 
                // SALE category (highlighted in red)
                $sale_category = get_term_by('slug', 'sale', 'product_cat');
                if ($sale_category) : 
                    $is_active = isset($active_filters['product_cat']) && $active_filters['product_cat'] === 'sale';
                    $filter_url = hoc_build_filter_url($current_url, 'product_cat', 'sale', $is_active);
                ?>
                    <li class="hoc-filter-panel__item">
                        <a href="<?php echo esc_url($filter_url); ?>" 
                           class="hoc-filter-panel__link hoc-filter-panel__link--sale <?php echo $is_active ? 'is-active' : ''; ?>"
                           data-filter="category"
                           data-value="sale">
                            <?php esc_html_e('SALE', 'haus-of-crunch'); ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php // Dynamically display all product attributes ?>
    <?php foreach ($product_attributes as $attr_name => $attr_data) : 
        $filter_key = 'filter_' . $attr_data['taxonomy'];
        $current_value = isset($active_filters[$filter_key]) ? $active_filters[$filter_key] : '';
    ?>
        <div class="hoc-filter-panel__section">
            <h3 class="hoc-filter-panel__title"><?php echo esc_html(strtoupper($attr_data['label'])); ?></h3>
            
            <?php // List layout for all attributes ?>
            <ul class="hoc-filter-panel__list">
                <?php foreach ($attr_data['terms'] as $term) : 
                    $is_active = $current_value === $term->slug;
                    $filter_url = hoc_build_filter_url($current_url, $filter_key, $term->slug, $is_active);
                ?>
                    <li class="hoc-filter-panel__item">
                        <a href="<?php echo esc_url($filter_url); ?>" 
                           class="hoc-filter-panel__link <?php echo $is_active ? 'is-active' : ''; ?>"
                           data-filter="<?php echo esc_attr($attr_name); ?>"
                           data-value="<?php echo esc_attr($term->slug); ?>">
                            <?php echo esc_html(strtoupper($term->name)); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>

    <?php // BRAND Section ?>
    <?php if (!empty($brand_terms) && !is_wp_error($brand_terms)) : ?>
        <div class="hoc-filter-panel__section">
            <h3 class="hoc-filter-panel__title"><?php esc_html_e('BRAND', 'haus-of-crunch'); ?></h3>
            <ul class="hoc-filter-panel__list">
                <?php foreach ($brand_terms as $brand) : 
                    $is_active = isset($active_filters[$brand_filter_key]) && $active_filters[$brand_filter_key] === $brand->slug;
                    $filter_url = hoc_build_filter_url($current_url, $brand_filter_key, $brand->slug, $is_active);
                ?>
                    <li class="hoc-filter-panel__item">
                        <a href="<?php echo esc_url($filter_url); ?>" 
                           class="hoc-filter-panel__link <?php echo $is_active ? 'is-active' : ''; ?>"
                           data-filter="brand"
                           data-value="<?php echo esc_attr($brand->slug); ?>">
                            <?php echo esc_html(strtoupper($brand->name)); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php // Clear All Filters ?>
    <?php if ($has_active_filters) : ?>
        <div class="hoc-filter-panel__section">
            <a href="<?php echo esc_url($clear_url); ?>" class="hoc-filter-panel__clear">
                <?php esc_html_e('Clear All Filters', 'haus-of-crunch'); ?>
            </a>
        </div>
    <?php endif; ?>

</aside>
