<?php
if (!defined('ABSPATH'))
    exit;
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
//echo '<pre>';
$form_types = dspp_get_all_form_types();
$services_list = dspp_get_all_services_list();
//print_r($services_list);
//exit;
?>
<form id="add-new-order-form">
    <h1>
        Add form
    </h1>
    <label>Form name</label>
    <br>
    <input type="text" name="form-name" placeholder="For your reference" style="width: 74%;" required="required"/>
    <br>
    <label>Form information</label>
    <br>
    <textarea name="form-information" style="width: 74%;" required="required">
    </textarea>
    <br>
    <label>Form Type</label><br>
    <select class="order-form-type" name="form-type" style="width: 74%;min-width: 74%;">
        <?php foreach ($form_types as $type) {
            ?><option value="<?php echo esc_html($type['form_type_id']); ?>"><?php echo esc_html($type['form_type_name']); ?></option><?php }
        ?>
    </select>
    <br><br>
    <div mbsc-page class="order-form-services-list">
        <div style="height:100%">
            <label>Select Services for Intake Form</label><br><br>
            <select class="order-form-services-list" id= "demo-multiple-select" name="services_list" multiple multiselect-search="true" multiselect-select-all="true" multiselect-max-items="3" onchange="console.log(this.selectedOptions)">
                <?php foreach ($services_list as $type) {
                    ?><option value="<?php echo esc_html($type['value']); ?>"><?php echo esc_html($type['label']); ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
    <br><br>
    <div id="build-wrap"></div>
    <br>
    <input type="submit" value="Save Form">
</form>