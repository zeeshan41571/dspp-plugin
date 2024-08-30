$ = jQuery;
(function ($) {
    'use strict';
    new DataTable("#user_orders");
    new DataTable("#user_invoices_dashboard", {
        info: false,
        paging: false,
        bFilter: false
    });
    new DataTable("#user_orders_dashboard", {
        info: false,
        paging: false,
        bFilter: false
    });
    $(".add_new_address").click(function () {
        $(".old_addresses").hide();
        $(".new_billing_address_form").show();
    });
    $('.submit_cart').on('click', function (e) {
        e.preventDefault();
        $("#payment-form").submit();
    });
    $("#payment-form").on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        console.log(formData);
        var nonce = dspp_nonces.dspp_bits_payment_processor;
        var ajaxUrl = dspp_nonces.ajax_url;
        $.ajax({
            type: 'post',
            url: ajaxUrl,
            data: {
                action: 'dspp_bits_payment_processor',
                nonce: nonce,
                formData: formData,
            },
            success: function (response) {
                if (response.success) {
                    location.href = response.url;
                } else {
                    alert('We are unable to process your order right now! Please try again later.');
                }
                console.log(response);
            }
        });
    });
})(jQuery);
function dspp_submitForm() {
    document.getElementById('update-user-profile-image').submit();
}