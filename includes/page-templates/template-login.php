<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (is_user_logged_in()) {
    wp_logout();
//    header('Location: '.site_url().'/bits-profile');
}
get_header();
?>
<form action="#" method="post" id="ajax-login-form">
    <div class="login-container">
        <h1><?php esc_html_e('Sign in', 'digital-service-provider-crm');?></h1>
        <table width="100%"> 
            <tr>
                <td><input type="text" id="username" placeholder="Enter Username" name="uname" required></td>
            </tr>
            <tr>
                <td><input type="password" id="password" placeholder="Enter Password" name="psw" required></td>
            </tr>
        </table>
        <p class="login-message"></p>
        <button type="submit" class="btn_login"><?php esc_html_e('Sign in', 'digital-service-provider-crm'); ?></button>
        <div class="links">
            <div class="text-center text-muted text-sm">
                <?php esc_html_e('Don\'t have an account?', 'digital-service-provider-crm'); ?>
                <a href="<?php echo esc_url(site_url("/bits-register"));?>">
                    <?php esc_html_e('Sign up', 'digital-service-provider-crm'); ?>
                </a>
            </div>
            <span class="psw text-muted"><?php esc_html_e('Forgot', 'digital-service-provider-crm'); ?> <a href="<?php echo esc_url(site_url("/bits-forgot-password"));?>"><?php esc_html_e('password', 'digital-service-provider-crm'); ?></a>?</span>
        </div>

    </div>
</form>

<?php get_footer(); ?>