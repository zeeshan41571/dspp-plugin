<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (isset($_POST['action'] ) && isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['nonce'])) , 'dspcrm-order-forms-save' ))
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'sppcrm_order_form_types';
    
    // Sanitize form data before inserting into the database
    $form_type_name = isset($_POST['form_type_name']) ? sanitize_text_field($_POST['form_type_name']) : '';
    $form_type_description = isset($_POST['form_type_description']) ? sanitize_text_field($_POST['form_type_description']) : '';

    $data = array(
        'form_type_name' => $form_type_name,
        'form_type_description' => $form_type_description,
    );

    // Insert data into the database
    $wpdb->insert($table_name, $data);
} else {
    // Handle unauthorized access or display an error message
    echo 'Unauthorized access.';
}

?>
<!-- The Modal -->
<div id="edit-form-type-modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <h2>Edit Form Type</h2>
        <form id="edit-form-type-form" method="post" action="" class="validate">
            <?php wp_nonce_field('dspcrm-order-forms-save', 'nonce'); ?>
            <div class="form-field form-required term-name-wrap">
                <label for="form_type_name">Name</label>
                <input name="edit-form-type-name" id="edit-form-type-name" type="text" value="" size="40" aria-required="true" aria-describedby="name-description" required="required" />
                <p id="name-description">The name is how it appears on your site.</p>
            </div>
            <div class="form-field term-description-wrap">
                <label for="edit-form-type-description">Description</label>
                <textarea name="edit-form-type-description" id="edit-form-type-description" rows="5" cols="40" aria-describedby="description-description" required="required"></textarea>
                <p id="description-description">The description is not prominent by default; however, some themes may show it.</p>
            </div>
            <p class="submit">
                <input type="hidden" id="edit-form-type-id" name="edit-form-type-id">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
                <span class="spinner"></span>
            </p>
        </form>
    </div>

</div>
<div class="wrap">
    <h1 class="wp-heading-inline">Form Settings</h1>
    <hr class="wp-header-end">
    <div id="col-container" class="wp-clearfix">
        <div id="col-left">
            <div class="col-wrap">
                <div class="form-wrap">
                    <h2>Add New Form Type</h2>
                    <form id="addCRMStatuses" method="post" action="" class="validate">
                        <div class="form-field form-required term-name-wrap">
                            <label for="form_type_name">Name</label>
                            <input name="form_type_name" id="tag-name" type="text" value="" size="40" aria-required="true" aria-describedby="name-description" required="required" />
                            <p id="name-description">The name is how it appears on your site.</p>
                        </div>
                        <div class="form-field term-description-wrap">
                            <label for="form_type_description">Description</label>
                            <textarea name="form_type_description" id="tag-description" rows="5" cols="40" aria-describedby="description-description" required="required"></textarea>
                            <p id="description-description">The description is not prominent by default; however, some themes may show it.</p>
                        </div>
                        <p class="submit">
                            <input type="hidden" name="action" value="submit" />
                            <input type="submit" name="submit" id="submit" class="button button-primary" value="Add New Form Type" />
                            <span class="spinner"></span>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <!-- /col-left -->
        <div id="col-right">
            <div class="col-wrap">
                <form id="posts-filter" method="post">
                    <div class="tablenav top">
                        <div class="tablenav-pages one-page">
                            <span class="pagination-links">
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                                <span class="paging-input">
                                    <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                                    <input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging">
                                    <span class="tablenav-paging-text"> of <span class="total-pages">1</span>
                                    </span>
                                </span>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                            </span>
                        </div>
                        <br class="clear">
                    </div>
                    <h2 class="screen-reader-text">Form types list</h2>
                    <table class="wp-list-table widefat fixed striped table-view-list tags">
                        <caption class="screen-reader-text">Table ordered hierarchically. Ascending.</caption>
                        <thead>
                            <tr>
                                <th scope="col" class="manage-column column-name column-primary sorted asc" aria-sort="ascending" abbr="Name">
                                    <a href="#">
                                        <span>Name</span>
                                    </a>
                                </th>
                                <th scope="col" class="manage-column column-description sortable desc" abbr="Description">
                                    <a href="#">
                                        <span>Description</span>
                                    </a>
                                </th>
                                <th scope="col" id="action" class="manage-column column-description sortable desc" abbr="Description">
                                    <a href="#">
                                        <span>Actions</span>
                                    </a>
                                </th>                
                            </tr>
                        </thead>
                        <tbody id="the-list" data-wp-lists="list:tag">
                            <?php
                            global $wpdb;
                            $table_name = $wpdb->prefix . 'sppcrm_order_form_types';
                            $results = $wpdb->get_results("SELECT * FROM %s", $table_name);
                            if (!empty($results)) {
                                foreach ($results as $result) {
                                    ?>
                                    <tr id="tag-1" class="level-0">
                                        <td class="name column-name has-row-actions column-primary" data-colname="Name">
                                            <strong>
                                                <?php echo esc_html($result->form_type_name) ?>
                                            </strong>
                                        </td>
                                        <td class="description column-description" data-colname="Description">
                                            <span aria-hidden="true"><?php echo esc_html($result->form_type_description) ?></span>
                                        </td>
                                        <td class="">
                                            <a class="edit-form-type" id="edit-form-type-id" href="#" data-form-type-id="<?php echo esc_html($result->form_type_id); ?>"  aria-label="">
                                                Edit
                                            </a>
                                            | 
                                            <a class="delete-form-type" href="#" data-form-type-id="<?php echo esc_html($result->form_type_id); ?>" aria-label="">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr id="tag-1" class="level-0">
                                    <td>

                                    </td>
                                    <td>
                                        No records found! 
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th scope="col" class="manage-column column-name column-primary sorted asc" aria-sort="ascending" abbr="Name">
                                    <a href="#">
                                        <span>Name</span>
                                    </a>
                                </th>
                                <th scope="col" class="manage-column column-description sortable desc" abbr="Description">
                                    <a href="#">
                                        <span>Description</span>
                                    </a>
                                </th>
                                <th scope="col" id="action" class="manage-column column-description sortable desc" abbr="Description">
                                    <a href="#">
                                        <span>Actions</span>
                                    </a>
                                </th> 
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
        </div>
        <!-- /col-right -->
    </div>
</div>