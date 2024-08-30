<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<h1 class="mb-5"><?php esc_html_e('Settings', 'digital-service-provider-crm'); ?></h1>
<?php wp_nonce_field('crm_settings_action', 'crm_settings_nonce'); ?>
<div class="row">
    <div class="col-sm-6">
        <h4 class="mb-4"><?php esc_html_e('Your business', 'digital-service-provider-crm'); ?></h4>
        <a href="<?php echo esc_url('?page=crm_settings&module=general'); ?>"><?php esc_html_e('Company', 'digital-service-provider-crm'); ?></a>
        <div class="mb-4 help-block"><?php esc_html_e('Set your brand name and colors, upload your logo, connect your domain.', 'digital-service-provider-crm'); ?></div>

        <a href="<?php echo esc_url('?page=payment_methods'); ?>"><?php esc_html_e('Payments', 'digital-service-provider-crm'); ?></a>
        <div class="mb-4 help-block"><?php esc_html_e('Connect your payment processors, set your currency, invoice and tax settings.', 'digital-service-provider-crm'); ?></div>
    </div>
    <div class="col-sm-6">
        <h4 class="mb-4"><?php esc_html_e('Your process', 'digital-service-provider-crm'); ?></h4>
        <a href="<?php echo esc_url('?page=crm_settings&module=orders'); ?>"><?php esc_html_e('Orders', 'digital-service-provider-crm'); ?></a>
        <div class="mb-4 help-block"><?php esc_html_e('Customize order statuses and how they\'re handled.', 'digital-service-provider-crm'); ?></div>

        <a href="<?php echo esc_url('?page=crm_settings&module=invoices'); ?>"><?php esc_html_e('Invoices', 'digital-service-provider-crm'); ?></a>
        <div class="mb-4 help-block"><?php esc_html_e('Customize Invoice statuses and how they\'re handled.', 'digital-service-provider-crm'); ?></div>
    </div>
</div>
