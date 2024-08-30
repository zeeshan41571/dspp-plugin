<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (is_user_logged_in()) {
    header('Location: bits-profile');
}
get_header();
?>
<form action="#" id="registration-form" method="post">
    <div class="register-container">
        <h1><?php esc_html_e('Register', 'digital-service-provider-crm'); ?></h1>
        <p><?php esc_html_e('Please fill in this form to create an account.', 'digital-service-provider-crm'); ?></p>
        <hr>
        <table width="100%">
            <tr> 
                <td><input type="text" placeholder="User Name" name="username" id="username" required></td> 
            </tr> 
            <tr>
                <td><input type="email" placeholder="Email" name="email" id="email"required></td> 
            </tr> 
            <tr>
                <td><input type="password" placeholder="Password" name="psw" id="psw" required></td> 
            </tr> 
            <tr> 
                <td><input type="password" placeholder="Repeat Password" name="psw-repeat" id="psw-repeat" required></td> 
            </tr> 
        </table>
        <p class="register-message"></p>
        <button type="submit" class="registerbtn"><?php esc_html_e('Register', 'digital-service-provider-crm'); ?></button>
        <p><?php esc_html_e('Already have an account?', 'digital-service-provider-crm');?> <a href="<?php echo esc_url(site_url("/bits-login"))?>"><?php esc_html_e('Sign in', 'digital-service-provider-crm'); ?></a></p>
    </div>
</form>
<?php get_footer(); ?>