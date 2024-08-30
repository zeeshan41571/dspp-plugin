<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$menu = wp_get_nav_menu_items("bits-crm-menu");
$general_settings = dspp_display_uploaded_settings();
$site_title = get_bloginfo('name');
?>
<div class="bits-sidebar">
    <div class="crm-logo">
        <?php if (!empty($general_settings->logo)) {
            ?>
        <a href="<?php echo esc_url(site_url()); ?>" class="" data-turbo-frame="_top">
                <img src="<?php echo esc_url($general_settings->logo) ?>" alt="TWH Test" class="w-full rounded" style="max-width: 160px;">
            </a>
        <?php }
        ?>
        <?php if (empty($general_settings->logo)) {
            ?>
            <div class="flex items-center p-3">
                <a href="<?php echo esc_url(site_url()); ?>" class="block overflow-hidden mr-3 flex-grow-1 collapsed-hide " data-turbo-frame="_top">
                    <span class="font-bold text-blue-50 text-xl leading-none whitespace-nowrap">
                        <?php echo esc_html($site_title) ?>
                    </span>
                </a>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    if (is_single() || is_page()) {
        $current_page = get_queried_object();
        $current_page_slug = $current_page->post_name;
    }
    foreach ($menu as $item) {
        $classes = '';
        foreach ($item->classes as $class) {
            $classes .= ' ' . $class;
        }
        $shifted = array_shift($item->classes);
        if ($shifted == 'crm-menu-item-heading') {
            ?><p class="crm-menu-item-heading"><?php echo esc_html($item->title);?></p><?php
        } else {
            ?><a class="crm-menu-item" href="<?php echo esc_url($item->url);?>"><i class="<?php echo esc_html($classes);?>"></i> <?php echo esc_html($item->title);?></a><?php
        }
    }
    ?>
</div>