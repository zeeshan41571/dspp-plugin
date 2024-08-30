<?php
if (!defined('ABSPATH'))
    exit;
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
$form_id = '';
if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'order_details_by_id')) {
    $form_id = isset($_GET['form_id']) ? sanitize_key($_GET['form_id']) : '';
}
$form_details = array_shift(dspp_get_custom_forms_by_id($form_id));
$service_ids = array();
if (isset($form_details['service_ids']) && $form_details['service_ids'] !== "null") {
    $service_ids = json_decode($form_details['service_ids']);
}
//$service_ids = (count($form_details['service_ids'])) > 0 ? : array();
//var_dump($service_ids);
//exit;
$jsonString = stripslashes($form_details['form_details']);
$decodedArray = json_decode($jsonString, true);
$validJsonString = wp_json_encode($decodedArray, JSON_PRETTY_PRINT);
$services_list = dspp_get_all_services_list();
$form_types = dspp_get_all_form_types();
//echo "<pre>";
//print_r($validJsonString);
//exit;
?>
<form id="update-order-form">
    <h1>
        Update form
    </h1>
    <label>Form name</label>
    <br>
    <input type="text" name="form-name" placeholder="For your reference" style="width: 74%;" value="<?php echo esc_html($form_details['form_title']); ?>"/>
    <br>
    <label>Form information</label>
    <br>
    <textarea name="form-information" style="width: 74%;"><?php echo esc_html($form_details['form_information']); ?>
    </textarea>
    <br>
    <label>Form Type</label><br>
    <select class="order-form-type" name="form-type" style="width: 74%;min-width: 74%;">
        <?php foreach ($form_types as $type) {
            ?><option value="<?php echo esc_html($type['form_type_id']); ?>" <?php echo ($type['form_type_id'] == $form_details['form_type_id']) ? "Selected" : "" ?>><?php echo esc_html($type['form_type_name']); ?></option><?php }
        ?>
    </select>
    <br><br>
    <div class="order-form-services-list">
        <label>Select Services for Intake Form</label><br><br>
        <select class="order-form-services-list" name="field2" id="demo-multiple-select" multiple multiselect-search="true" multiselect-select-all="true" multiselect-max-items="3" onchange="console.log(this.selectedOptions)">
            <?php foreach ($services_list as $type) {
                ?><option value="<?php echo esc_html($type['value']); ?>" <?php echo (in_array($type['value'], $service_ids, TRUE)) ? "" : "Selected"; ?>><?php echo esc_html($type['label']); ?></option><?php }
            ?>
        </select>
    </div>
    <br><br>
    <div id="build-wrap-update">
        <!--$form_details['form_details']-->
    </div>
    <br>
    <input type="submit" value="Update Form">
    <input type="hidden" name="form_id" value="<?php echo esc_html($form_id); ?>"/>
</form>