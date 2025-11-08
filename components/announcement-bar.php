<?php
/**
 * Announcement Bar Component
 * -------------------------
 * Usage:
 * get_template_part('components/announcement-bar');
 * 
 * Settings are pulled from WordPress Customizer
 */

// Get announcement bar settings from customizer
$announcement_enabled = get_theme_mod('hoc_announcement_enabled', false);
$announcement_text = get_theme_mod('hoc_announcement_text', '');
$announcement_link = get_theme_mod('hoc_announcement_link', '');
$announcement_dismissible = get_theme_mod('hoc_announcement_dismissible', false);
$announcement_bg_color = get_theme_mod('hoc_announcement_bg_color', '#111111');
$announcement_text_color = get_theme_mod('hoc_announcement_text_color', '#ffffff');

// Convert boolean strings to actual booleans (WordPress Customizer returns '1' or '')
$announcement_enabled = filter_var($announcement_enabled, FILTER_VALIDATE_BOOLEAN);
$announcement_dismissible = filter_var($announcement_dismissible, FILTER_VALIDATE_BOOLEAN);

// Get args from query vars (set by hoc_get_component) or direct $args
$component_args = get_query_var('component_args', []);
$args = !empty($args) ? $args : $component_args;

// Allow override from args if provided
$announcement_enabled = $args['enabled'] ?? $announcement_enabled;
$announcement_text = $args['text'] ?? $announcement_text;
$announcement_link = $args['link'] ?? $announcement_link;
$announcement_dismissible = $args['dismissible'] ?? $announcement_dismissible;
$announcement_bg_color = $args['bg_color'] ?? $announcement_bg_color;
$announcement_text_color = $args['text_color'] ?? $announcement_text_color;

// Don't display if disabled or no text
if (!$announcement_enabled || empty($announcement_text)) {
    return;
}

// Check if announcement was dismissed (using localStorage via JS)
$dismissed_class = $announcement_dismissible ? 'hoc-announcement-bar--dismissible' : '';

// Generate unique ID for this announcement (based on text content)
$announcement_id = 'hoc-announcement-' . md5($announcement_text);
?>

<div class="hoc-announcement-bar <?php echo esc_attr($dismissed_class); ?>" 
     id="<?php echo esc_attr($announcement_id); ?>"
     data-announcement-id="<?php echo esc_attr($announcement_id); ?>"
     style="background-color: <?php echo esc_attr($announcement_bg_color); ?>; color: <?php echo esc_attr($announcement_text_color); ?>;">
    <div class="hoc-container hoc-announcement-bar__inner">
        <div class="hoc-announcement-bar__content">
            <?php if (!empty($announcement_link)) : ?>
                <a href="<?php echo esc_url($announcement_link); ?>" class="hoc-announcement-bar__link" style="color: <?php echo esc_attr($announcement_text_color); ?>;">
                    <?php echo esc_html($announcement_text); ?>
                </a>
            <?php else : ?>
                <span class="hoc-announcement-bar__text">
                    <?php echo esc_html($announcement_text); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <?php if ($announcement_dismissible) : ?>
            <button class="hoc-announcement-bar__close" 
                    aria-label="<?php esc_attr_e('Dismiss announcement', 'haus-of-crunch'); ?>"
                    style="color: <?php echo esc_attr($announcement_text_color); ?>;">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="hoc-announcement-bar__sr-only"><?php esc_html_e('Close', 'haus-of-crunch'); ?></span>
            </button>
        <?php endif; ?>
    </div>
</div>

