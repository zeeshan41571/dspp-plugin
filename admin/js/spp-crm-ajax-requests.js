/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


$ = jQuery;
$(document).ready(function () {
    $(".bank_save").click(function () {
        var enable_bank = $("#enable_bank").is(":checked");
        var stripe_publishable_key = $("#stripe_publishable_key").val();
        var method_id = $("#method_id").val();
        var nonce = dspp_nonces.add_payment_methode;
        var ajaxUrl = dspp_nonces.ajax_url;
        if(method_id === "") {
            method_id = 0;
        }
        if (stripe_publishable_key === "") {
            alert("Bank details are required.");
        } else {
            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "dspp_add_payment_methode", // Corrected action hook
                    nonce: nonce,
                    enable: enable_bank,
                    user_plublish: stripe_publishable_key,
                    methode: "Bank",
                    method_id: method_id
                },
                success: function (response) {
                    if (response.success) {
                        $(".success").text(response.data);
                    } else {
                        $(".success").text(response.data);
                    }
                    location.reload();
                }
            });
        }
    });
    $(".stripe_save").click(function () {
        var enable_stripe = $("#enable_stripe").is(":checked");
        var stripe_publishable_key = $("#stripe_publishable_key").val();
        var stripe_secret_key = $("#stripe_secret_key").val();
        var method_id = $("#method_id").val();
        var nonce = dspp_nonces.add_payment_methode;
        var ajaxUrl = dspp_nonces.ajax_url;
        if (stripe_publishable_key === "" || stripe_secret_key === "") {
            alert("Publishable key and secret are required fields.");
        } else {
            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "dspp_add_payment_methode", // Corrected action hook
                    nonce: nonce,
                    enable: enable_stripe,
                    user_plublish: stripe_publishable_key,
                    secret_key: stripe_secret_key,
                    methode: "Strip",
                    method_id: method_id
                },
                success: function (response) {
                    if (response.success) {
                        $(".success").text(response.data);
                    } else {
                        $(".success").text(response.data);
                    }
                    location.reload();
                }
            });
        }
    });
    $(".paypal_save").click(function () {
        $(".paypal_save").prop("disabled");
        var enable_paypal = $("#enable_paypal").is(":checked");
        var stripe_publishable_key = $("#stripe_publishable_key").val();
        var stripe_secret_key = $("#stripe_secret_key").val();
        var method_id = $("#method_id").val();
        var nonce = dspp_nonces.add_payment_methode;
        var ajaxUrl = dspp_nonces.ajax_url;
        if (stripe_publishable_key === "" || stripe_secret_key === "") {
            alert("User ID and Secret are required fields.");
        } else {
            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "dspp_add_payment_methode", // Corrected action hook
                    nonce: nonce,
                    enable: enable_paypal,
                    user_plublish: stripe_publishable_key,
                    secret_key: stripe_secret_key,
                    methode: "Paypal",
                    method_id: method_id
                },
                success: function (response) {
                    if (response.success) {
                        $(".success").text(response.data);
                    } else {
                        $(".success").text(response.data);
                    }
                    location.reload();
                }
            });
        }
    });
    $('.delete-status').on('click', function (e) {
        e.preventDefault();
        var statusId = $(this).data('status-id');
        var nonce = dspp_nonces.delete_status_action;
        var ajaxUrl = dspp_nonces.ajax_url;
        console.log(statusId);
        // Show confirmation dialog
        if (confirm('Are you sure you want to delete this status?')) {
            // AJAX request to delete the status
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'dspp_delete_status_action',
                    nonce: nonce,
                    status_id: statusId
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete status. Please try again.');
                    }
                },
                error: function () {
                    alert('Error during the AJAX request.');
                }
            });
        }
    });
    $('.edit-status').on('click', function (e) {
        e.preventDefault();
        var statusId = $(this).data('status-id');
        var nonce = dspp_nonces.get_status_details_action;
        var ajaxUrl = dspp_nonces.ajax_url;
        // AJAX request to retrieve status details
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'dspp_get_status_details_action',
                nonce: nonce,
                status_id: statusId
            },
            success: function (response) {
                if (response.success) {
                    // Populate the edit form with retrieved data
                    $('#edit-status-name').val(response.data.status_name);
                    $('#edit-status-description').val(response.data.status_description);
                    $('#edit-status-id').val(statusId);
                    $('#edit-status-modal').show();
                } else {
                    alert('Failed to retrieve status details. Please try again.');
                }
            },
            error: function () {
                alert('Error during the AJAX request.');
            }
        });
    });

    $('#edit-status-form').on('submit', function (e) {
        e.preventDefault();
        var nonce = dspp_nonces.edit_status_action;
        var ajaxUrl = dspp_nonces.ajax_url;
        // AJAX request to update status details
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'dspp_edit_status_action',
                nonce: nonce,
                status_id: $('#edit-status-id').val(),
                status_name: $('#edit-status-name').val(),
                status_description: $('#edit-status-description').val(),
            },
            success: function (response) {
                if (response.success) {
                    // Close the modal or perform any other actions
                    $('#edit-status-modal').hide();
                    location.reload();
                } else {
                    alert('Failed to update status. Please try again.');
                }
            },
            error: function () {
                alert('Error during the AJAX request.');
            }
        });
    });
    $('#update-order-status-admin').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var nonce = dspp_nonces.update_order_status_admin;
        var ajaxUrl = dspp_nonces.ajax_url;
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: {
                action: 'dspp_update_order_status_admin',
                nonce: nonce,
                formData: formData
            },
            success: function (response) {
                if (response.success) {
                    var message = '<p class="sucess-message alert alert-success">' + response.data + '</p>';
                    $("#message-box").html(message);
                    $('#message-box').fadeIn(2000);
                    $('#message-box').delay(2000).fadeOut(2000);
                } else {
                    var message = '<p class="error-message alert alert-danger">' + response.data + '</p>';
                    $("#message-box").html(message);
                    $('#message-box').fadeIn(2000);
                    $('#message-box').delay(2000).fadeOut(2000);
                }
            }
        });
    });
    $('#update-invoice-status-admin').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var nonce = dspp_nonces.update_invoice_status_admin;
        var ajaxUrl = dspp_nonces.ajax_url;
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: {
                action: 'dspp_update_invoice_status_admin',
                nonce: nonce,
                formData: formData
            },
            success: function (response) {
                if (response.success) {
                    var message = '<p class="sucess-message alert alert-success">' + response.data + '</p>';
                    $("#message-box").html(message);
                    $('#message-box').fadeIn(2000);
                    $('#message-box').delay(2000).fadeOut(2000);
                } else {
                    var message = '<p class="error-message alert alert-danger">' + response.data + '</p>';
                    $("#message-box").html(message);
                    $('#message-box').fadeIn(2000);
                    $('#message-box').delay(2000).fadeOut(2000);
                }
            }
        });
    });
    $('.delete-invoice-status').on('click', function (e) {
        e.preventDefault();
        var nonce = dspp_nonces.delete_invoice_status_action;
        var ajaxUrl = dspp_nonces.ajax_url;
        var statusId = $(this).data('status-id');
        // Show confirmation dialog
        if (confirm('Are you sure you want to delete this status?')) {
            // AJAX request to delete the status
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'dspp_delete_invoice_status_action',
                    nonce: nonce,
                    status_id: statusId
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete status. Please try again.');
                    }
                },
                error: function () {
                    alert('Error during the AJAX request.');
                }
            });
        }
    });
    $('.edit-invoice-status').on('click', function (e) {
        e.preventDefault();
        var nonce = dspp_nonces.get_invoice_status_details_action;
        var ajaxUrl = dspp_nonces.ajax_url;
        var statusId = $(this).data('status-id');
        console.log(nonce);
        // AJAX request to retrieve status details
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'dspp_get_invoice_status_details_action',
                nonce: nonce,
                status_id: statusId
            },
            success: function (response) {
                if (response.success) {
                    // Populate the edit form with retrieved data
                    $('#edit-status-name').val(response.data.status_name);
                    $('#edit-status-description').val(response.data.status_description);
                    $('#edit-status-id').val(statusId);

                    // Show the edit form
                    $('#edit-status-modal').show();
                } else {
                    alert('Failed to retrieve status details. Please try again.');
                }
            },
            error: function () {
                alert('Error during the AJAX request.');
            }
        });
    });
    $('#edit-invoice-status-form').on('submit', function (e) {
        e.preventDefault();
        var nonce = dspp_nonces.edit_invoice_status_action;
        var ajaxUrl = dspp_nonces.ajax_url;
        // AJAX request to update status details
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'dspp_edit_invoice_status_action',
                nonce: nonce,
                status_id: $('#edit-status-id').val(),
                status_name: $('#edit-status-name').val(),
                status_description: $('#edit-status-description').val(),
            },
            success: function (response) {
                if (response.success) {
                    // Close the modal or perform any other actions
                    $('#edit-status-modal').hide();
                    location.reload();
                } else {
                    alert('Failed to update status. Please try again.');
                }
            },
            error: function () {
                alert('Error during the AJAX request.');
            }
        });
    });
});
