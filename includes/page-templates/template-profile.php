<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
if (!is_user_logged_in()) {
    header('Location: ' . site_url() . '/bits-login');
}
$user_id = get_current_user_id();
if (isset($_POST['update_user_profile_image_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['update_user_profile_image_nonce'])), 'update_user_profile_image')) {
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === 0) {
        $wp_load_path = ABSPATH . DIRECTORY_SEPARATOR . 'wp-load.php';
        require_once($wp_load_path);
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        $file = array();
        if (!empty(sanitize_file_name($_FILES['fileToUpload']['name']))) {
            $file['name'] = sanitize_file_name($_FILES['fileToUpload']['name']);
            $file['full_path'] = sanitize_text_field($_FILES['fileToUpload']['full_path']);
            $file['type'] = sanitize_text_field($_FILES['fileToUpload']['type']);
            $file['tmp_name'] = sanitize_text_field($_FILES['fileToUpload']['tmp_name']);
            $file['error'] = sanitize_key($_FILES['fileToUpload']['error']);
            $file['size'] = sanitize_key($_FILES['fileToUpload']['size']);
        }
        $upload_dir = wp_upload_dir();
        $uploaded_file = wp_handle_upload(
                $file,
                array('test_form' => false)
        );
        $attachment_id = wp_insert_attachment(
                array(
                    'guid' => $uploaded_file['url'],
                    'post_mime_type' => $uploaded_file['type'],
                    'post_title' => basename($uploaded_file['file']),
                    'post_content' => '',
                    'post_status' => 'inherit',
                ),
                $uploaded_file['file']
        );
        if (!isset($uploaded_file['error'])) {
            update_user_meta($user_id, 'avatar', $attachment_id);
        } else {
            echo 'Error uploading file: ' . esc_html($uploaded_file['error']);
        }
    }
}
$user_data = get_userdata($user_id);
$avatar_id = get_user_meta($user_id, 'avatar', true);
$avatar_url = wp_get_attachment_url($avatar_id);
if ($user_data) {
    $email = $user_data->user_email;
    $first_name = $user_data->first_name;
    $last_name = $user_data->last_name;
}
//print_r($avatar_url);
//exit;
?>
<h2><?php echo esc_html("Edit your details") ?></h2>
<br>
<section>
    <div id="message-box"></div>
    <div class="profile-left">
        <form id="update-user-profile-image" method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('update_user_profile_image', 'update_user_profile_image_nonce'); ?>
            <label for="fileToUpload">
                <div class="profile-pic" style="background-image: url('<?php echo esc_url($avatar_url); ?>')">
                    <span class="glyphicon glyphicon-camera"></span>
                    <span><?php echo esc_html("Change Image") ?></span>
                </div>
            </label>
            <input type="File" name="fileToUpload" id="fileToUpload" onchange="dspp_submitForm()">
        </form>
    </div>
    <div class="profile-right">
        <form id="user-profile-form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="email"><?php echo esc_html("Email") ?></label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo esc_html($email); ?>">
            </div>
            <div class="form-group">
                <label><?php echo esc_html("Password") ?></label>
                <div class="d-flex">
                    <input type="password" class="form-control" id="password" name="password">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="name_f"><?php echo esc_html("First name") ?></label>
                    <input type="text" class="form-control" id="name_f" name="name_f" value="<?php echo esc_html($first_name); ?>">
                </div>
                <div class="form-group col-sm-6">
                    <label for="name_l"><?php echo esc_html("Last name") ?></label>
                    <input type="text" class="form-control" id="name_l" name="name_l" value="<?php echo esc_html($last_name); ?>">
                </div>
            </div>
            <div class="mt-3 text-right">
                <button type="submit" class="btn btn-primary">
                    <?php echo esc_html("Save changes") ?>
                </button>
            </div>
        </form>
    </div>
</section>