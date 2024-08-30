<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (isset($_POST['save_changes'] ) && isset( $_POST['save_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['save_settings_nonce'])) , 'save_settings_action' )) {
    $wp_load_path = ABSPATH . DIRECTORY_SEPARATOR . 'wp-load.php';
    require_once($wp_load_path);
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    $general_settings = dspp_display_uploaded_settings();
    $logo = (isset($_FILES['logo']) && !empty(sanitize_text_field($_FILES['logo']['name']))) ? dspp_handle_logo_upload('logo') : $general_settings->logo;
    $sidebarLogo = (isset($_FILES['logo-inverse']) && !empty(sanitize_text_field($_FILES['logo-inverse']['name']))) ? dspp_handle_logo_upload('logo-inverse') : $general_settings->sidebar_logo;
    $sidebarColor = (isset($_POST['sidebar-bg'])) ? sanitize_hex_color($_POST['sidebar-bg']) : "#000";
    $accentColor = (isset($_POST['accent-color'])) ? sanitize_hex_color($_POST['accent-color']) : "#000";
    dspp_save_settings($logo, $sidebarLogo, $sidebarColor, $accentColor);
}
$general_settings = dspp_display_uploaded_settings();
//print_r($general_settings);
if (isset($general_settings)) {
    $logo = $general_settings->logo;
    $sidebar_logo = $general_settings->sidebar_logo;
    $sidebar_color = $general_settings->sidebar_color;
    $accent_color = $general_settings->accent_color;
}
?>
<h1><?php esc_html_e('General Settings', 'digital-service-provider-crm'); ?></h1>
<form method="post" action="" autocomplete="off" id="logoColorForm" enctype="multipart/form-data">
    <?php wp_nonce_field( 'save_settings_action', 'save_settings_nonce' ); ?>
    <div class="form-group">
        <h2 class="mt-4"><?php esc_html_e('Logo Settings', 'digital-service-provider-crm'); ?></h2>
        <div class="align-items-end">
            <div>
                <div class="mt-2 mb-3">
                    <img src="<?php echo esc_url($logo); ?>" class="mr-4" style="max-width: 170px; max-height: 80px">
                </div>
                <div class="mb-2 btn btn-secondary mb-sm-0" data-toggle="file" data-target="#logo" data-status="#status-1">
                    <input type="file" name="logo" id="logo_image" accept="image/*">
                    <?php esc_html_e('Upload logo', 'digital-service-provider-crm'); ?>
                </div>
            </div>
            <br>
            <br>
            <div class="ml-4">
                <div class="mt-2 mb-3">
                    <img src="<?php echo esc_url($sidebar_logo); ?>" class="mr-4" style="max-width: 170px; max-height: 80px">
                </div>
                <div class="mb-2 btn btn-secondary mb-sm-0" data-toggle="file" data-target="#logo-inverse" data-status="#status-2">
                    <input type="file" name="logo-inverse" id="logo_inverse_image" accept="image/*">
                    <?php esc_html_e('Upload sidebar logo', 'digital-service-provider-crm'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4 text-right">
        <input type="submit" name="save_changes" class="btn btn-primary" value="<?php esc_attr_e('Save changes', 'digital-service-provider-crm'); ?>" />
    </div>
</form>
