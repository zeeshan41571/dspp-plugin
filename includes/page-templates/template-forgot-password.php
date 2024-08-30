<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (is_user_logged_in()) {
    wp_logout();
}
get_header();
?>
<form action="#" method="post" id="forgot-password-form">
    <div class="forgot-container">
        <h1><?php esc_html_e('Reset Password', 'digital-service-provider-crm'); ?></h1>
        <table width="100%">
            <tr>
                <td><input type="text" name="user_login" id="user_login" placeholder="Email"/></td> 
            </tr>
        </table>
        <div id="reset-message"></div>
        <button type="submit" class="btn_login"><?php esc_html_e('Reset Password', 'digital-service-provider-crm'); ?></button>
        <p><?php esc_html_e('Already have an account?', 'digital-service-provider-crm'); ?> <a href="<?php echo esc_url(site_url("/bits-login")) ?>"><?php esc_html_e('Sign in', 'digital-service-provider-crm'); ?></a></p>
        <!--<input type="submit" value="Reset Password" />-->
    </div>
</form>
<?php get_footer(); ?>