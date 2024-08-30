<?php
if (!defined('ABSPATH'))
    exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'dspp_add_order_status')) {
    if (isset($_POST['action']) && sanitize_text_field($_POST['action']) == 'submit') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sppcrm_order_statuses';
        $data = array(
            'status_name' => sanitize_text_field($_POST['status_name']), // Replace with the actual status name you want to insert
            'status_description' => sanitize_text_field($_POST['status_description']), // Replace with the actual status name you want to insert
            'status_status' => "Active", // Replace with the actual status name you want to insert
        );
        $wpdb->insert($table_name, $data);
    }
}
?>
<!-- The Modal -->
<div id="edit-status-modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <h2><?php esc_html_e('Edit Status', 'digital-service-provider-crm'); ?></h2>
        <form id="edit-status-form" method="post" action="" class="validate">
            <div class="form-field form-required term-name-wrap">
                <label for="status_name"><?php esc_html_e('Name', 'digital-service-provider-crm'); ?></label>
                <input name="edit-status-name" id="edit-status-name" type="text" value="" size="40" aria-required="true" aria-describedby="name-description" required="required" />
                <p id="name-description"><?php esc_html_e('The name is how it appears on your site.', 'digital-service-provider-crm'); ?></p>
            </div>
            <div class="form-field term-description-wrap">
                <label for="edit-status-description"><?php esc_html_e('Description', 'digital-service-provider-crm'); ?></label>
                <textarea name="edit-status-description" id="edit-status-description" rows="5" cols="40" aria-describedby="description-description" required="required"></textarea>
                <p id="description-description"><?php esc_html_e('The description is not prominent by default; however, some themes may show it.', 'digital-service-provider-crm'); ?></p>
            </div>
            <p class="submit">
                <input type="hidden" id="edit-status-id" name="edit-status-id">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'digital-service-provider-crm'); ?>" />
                <span class="spinner"></span>
            </p>
        </form>
    </div>
</div>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Order Settings', 'digital-service-provider-crm'); ?></h1>
    <hr class="wp-header-end">
    <div id="col-container" class="wp-clearfix">
        <div id="col-left">
            <div class="col-wrap">
                <div class="form-wrap">
                    <h2><?php esc_html_e('Add New Status', 'digital-service-provider-crm'); ?></h2>
                    <form id="addCRMStatuses" method="post" action="" class="validate">
                        <?php wp_nonce_field('dspp_add_order_status', 'nonce'); ?>
                        <div class="form-field form-required term-name-wrap">
                            <label for="status_name"><?php esc_html_e('Name', 'digital-service-provider-crm'); ?></label>
                            <input name="status_name" id="tag-name" type="text" value="" size="40" aria-required="true" aria-describedby="name-description" required="required" />
                            <p id="name-description"><?php esc_html_e('The name is how it appears on your site.', 'digital-service-provider-crm'); ?></p>
                        </div>
                        <div class="form-field term-description-wrap">
                            <label for="status_description"><?php esc_html_e('Description', 'digital-service-provider-crm'); ?></label>
                            <textarea name="status_description" id="tag-description" rows="5" cols="40" aria-describedby="description-description" required="required"></textarea>
                            <p id="description-description"><?php esc_html_e('The description is not prominent by default; however, some themes may show it.' , 'digital-service-provider-crm'); ?></p>
                        </div>
                        <p class="submit">
                            <input type="hidden" name="action" value="submit" />
                            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Add New Status', 'digital-service-provider-crm'); ?>" />
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
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true"><?php esc_html_e('«' , 'digital-service-provider-crm'); ?></span>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true"><?php esc_html_e('‹', 'digital-service-provider-crm'); ?></span>
                                <span class="paging-input">
                                    <label for="current-page-selector" class="screen-reader-text"><?php esc_html_e('Current Page' , 'digital-service-provider-crm'); ?></label>
                                    <input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging">
                                    <span class="tablenav-paging-text"><?php esc_html_e(' of ', 'digital-service-provider-crm'); ?><span class="total-pages">1</span>
                                    </span>
                                </span>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true"><?php esc_html_e('›', 'digital-service-provider-crm'); ?></span>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true"><?php esc_html_e('»', 'digital-service-provider-crm'); ?></span>
                            </span>
                        </div>
                        <br class="clear">
                    </div>
                    <h2 class="screen-reader-text"><?php esc_html_e('Statuses list', 'digital-service-provider-crm'); ?></h2>
                    <table class="wp-list-table widefat fixed striped table-view-list tags">
                        <caption class="screen-reader-text"><?php esc_html_e('Table ordered hierarchically. Ascending.', 'digital-service-provider-crm'); ?></caption>
                        <thead>
                            <tr>
                                <th scope="col" class="manage-column column-name column-primary sorted asc" aria-sort="ascending" abbr="Name">
                                    <a href="#">
                                        <span><?php esc_html_e('Name', 'digital-service-provider-crm'); ?></span>
                                    </a>
                                </th>
                                <th scope="col" class="manage-column column-description sortable desc" abbr="Description">
                                    <a href="#">
                                        <span><?php esc_html_e('Description', 'digital-service-provider-crm'); ?></span>
                                    </a>
                                </th>
                                <th scope="col" id="action" class="manage-column column-description sortable desc" abbr="Description">
                                    <a href="#">
                                        <span><?php esc_html_e('Actions', 'digital-service-provider-crm'); ?></span>
                                    </a>
                                </th>                
                            </tr>
                        </thead>
                        <tbody id="the-list" data-wp-lists="list:tag">
                            <?php
                            global $wpdb;
                            $table_name = $wpdb->prefix . 'sppcrm_order_statuses';
                            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i", $table_name));
                            if (!empty($results)) {
                                foreach ($results as $result) {
                                    ?>
                                    <tr id="tag-1" class="level-0">
                                        <td class="name column-name has-row-actions column-primary" data-colname="Name">
                                            <strong>
                                                <?php echo esc_html($result->status_name); ?>
                                            </strong>
                                        </td>
                                        <td class="description column-description" data-colname="Description">
                                            <span aria-hidden="true"><?php echo esc_html($result->status_description); ?></span>
                                        </td>
                                        <td class="">
                                            <a class="edit-status" id="edit-status-id" href="#" data-status-id="<?php echo esc_attr($result->status_id); ?>"  aria-label="">
                                                <?php esc_html_e('Edit', 'digital-service-provider-crm'); ?>
                                            </a>
                                            | 
                                            <a class="delete-status" href="#" data-status-id="<?php echo esc_attr($result->status_id); ?>" aria-label="">
                                                <?php esc_html_e('Delete', 'digital-service-provider-crm'); ?>
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
                                        <?php esc_html_e('No records found!', 'digital-service-provider-crm'); ?> 
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
                                        <span><?php esc_html_e('Name', 'digital-service-provider-crm'); ?></span>
                                    </a>
                                </th>
                                <th scope="col" class="manage-column column-description sortable desc" abbr="Description">
                                    <a href="#">
                                        <span><?php esc_html_e('Description', 'digital-service-provider-crm'); ?></span>
                                    </a>
                                </th>
                                <th scope="col" id="action" class="manage-column column-description sortable desc" abbr="Description">
                                    <a href="#">
                                        <span><?php esc_html_e('Actions', 'digital-service-provider-crm'); ?></span>
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