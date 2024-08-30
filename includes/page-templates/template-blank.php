<?php
/*
  Template Name: BITS Blank Page
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (!is_user_logged_in()) {
    wp_logout();
    $redirect_url = site_url('/bits-login');
    wp_redirect($redirect_url);
    exit();
}
get_header();
$user_id = get_current_user_id();
$user_data = get_userdata($user_id);
$avatar_id = get_user_meta($user_id, 'avatar', true);
$avatar_url = wp_get_attachment_url($avatar_id);
$user_image = !empty($avatar_url) ? $avatar_url : esc_url(plugin_dir_url(__FILE__))."images/dummy.png";

?>
<div id="primary" class="content-area">
    <?php require_once 'template-menu.php'; ?>
    <nav style="background: #fafafa">
        <ul>
            <li>
                <img src="<?php echo esc_url($user_image)?>" class="profile" />
                <ul>
                    <a href="<?php echo esc_url(site_url('/bits-profile'));?>">
                        <li class="sub-item">
                            <p><?php esc_html_e('Profile', 'digital-service-provider-crm'); ?></p>
                        </li>
                    </a>
                    <a href="<?php echo esc_url(site_url('/bits-login')); ?>">
                        <li class="sub-item">
                            <p><?php esc_html_e('Logout', 'digital-service-provider-crm'); ?></p>
                        </li>
                    </a>
                </ul>
            </li>
        </ul>
    </nav>
    <main id="main" class="bits-content">

        <?php
        // Output the content of the page (shortcode content)
        while (have_posts()) :
            the_post();
            the_content();
        endwhile;
        ?>

    </main><!-- #main -->
</div><!-- #primary -->
<?php
get_footer();