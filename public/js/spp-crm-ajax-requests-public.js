/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


$ = jQuery;
$(document).ready(function () {
    $('#forgot-password-form').submit(function (e) {
        e.preventDefault();
        var userLogin = $('#user_login').val();
        var nonce = dspp_nonces.custom_forgot_password;
        var ajaxUrl = dspp_nonces.ajax_url;
        $.ajax({
            type: 'post',
            url: ajaxUrl,
            data: {
                action: 'dspp_custom_forgot_password',
                nonce: nonce,
                user_login: userLogin
            },
            success: function (response) {
                $('#reset-message').html(response);
            }
        });
    });
    $(document).on('click', '.btncart', function () {
        var site = $(this).data("site"); // Change 1 to the index of the column you want to copy
        var price = parseFloat($(this).data("price")); // Change 1 to the index of the column you want to copy
        var user_id = parseInt($(this).data("user-id")); // Change 1 to the index of the column you want to copy
        var service_id = parseInt($(this).data("post-id")); // Change 1 to the index of the column you want to copy
        var nonce = dspp_nonces.add_to_session_nonce;
        var ajaxUrl = dspp_nonces.ajax_url;
        var website_data = {
            site: site,
            price: price,
            user_id: user_id,
            post_id: service_id
        };
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'dspp_add_to_session',
                nonce: nonce,
                site: site,
                price: price,
                user_id: user_id,
                post_id: service_id,
                data: website_data
            },
            success: function (response) {
                var responseObj = JSON.parse(response);
                if (responseObj.status === true) {
                    var cartCountElement = $('#cartCount');
                    var currentCount = parseInt(cartCountElement.text(), 10);
                    var newCount = currentCount + 1;
                    cartCountElement.text(newCount);
                    var notification = $('.view-cart');
                    notification.fadeIn();
                    setTimeout(function () {
                        notification.fadeOut();
                    }, 2000);
                } else {
                    alert(responseObj.message);
                }
            },
            error: function (errorThrown) {
                console.log('Error:', errorThrown);
            }
        });
    });
    $('#user-profile-form').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var nonce = dspp_nonces.update_user_profile;
        var ajaxUrl = dspp_nonces.ajax_url;
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'dspp_update_user_profile',
                nonce: nonce,
                data: formData
            },
            success: function (response) {
                var responseObj = JSON.parse(response);
                if (responseObj.status === 'success') {
                    var message = '<p class="sucess-message alert alert-success">' + responseObj.message + '</p>';
                    $("#message-box").html(message);
                    $('#message-box').fadeIn(2000);
                    $('#message-box').delay(2000).fadeOut(2000);
                } else {
                    var message = '<p class="error-message alert alert-danger">' + responseObj.message + '</p>';
                    $("#message-box").html(message);
                    $('#message-box').fadeIn(2000);
                    $('#message-box').delay(2000).fadeOut(2000);
                }
            },
            error: function (errorThrown) {
                console.log('Error:', errorThrown);
            }
        });
    });
    $('#registration-form').on('submit', function (e) {
        e.preventDefault();
        var username = $('#username').val();
        var email = $('#email').val();
        var password = $('#psw').val();
        var confirmPassword = $('#psw-repeat').val();
        var usernameRegex = /^[a-zA-Z0-9_]+$/;
        var nonce = dspp_nonces.register_user;
        var ajaxUrl = dspp_nonces.ajax_url;
        if (!usernameRegex.test(username)) {
            $(".register-message").html("<span class='error'>Invalid username format. Use only letters, numbers, and underscores!</span>");
            $('#username').focus();
            return false;
        } else {
            $(".register-message").html();
        }
        if (password !== confirmPassword) {
            $(".register-message").html("<span class='error'>Password and Confirm Must be same!</span>");
            $('#psw-repeat').focus();
            return false; // Prevent form submission
        } else {
            $(".register-message").html();
        }
        var registrationData = {
            'action': 'dspp_register_user',
            'nonce': nonce,
            'username': username,
            'email': email,
            'password': password
        };
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            data: registrationData,
            success: function (response) {
                var parsed = JSON.parse(response);
                if (parsed.status == "error") {
                    $(".register-message").html("<span class='error'>" + parsed.message + "</span>");
                } else {
                    window.location.href = parsed.redirect;
                }
            }
        });
        return false;
    });
    $('#check_coupon_button').on('click', function (e) {
        e.preventDefault();
        var user_id = parseInt($(this).data("user-id"));
        var couponCode = $('#orderCoupon').val();
        var totalInvoiceElement = document.getElementById('total-invoice');
        var currentPriceText = totalInvoiceElement.textContent;
        var currentPriceSplitted = currentPriceText.split('$');
        var currentPrice = parseInt(currentPriceSplitted[1]);
        var nonce = dspp_nonces.check_coupon;
        var ajaxUrl = dspp_nonces.ajax_url;
        if (currentPrice > 0) {
            if (couponCode) {
                // Make an Ajax request
                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'dspp_check_coupon',
                        nonce: nonce,
                        coupon_code: couponCode,
                        user_id: user_id
                    },
                    success: function (response) {
//                    var parsed = JSON.parse(response);
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        }else {
                            alert(response.message);
                        }
                    },
                    error: function (errorThrown) {
                        console.log('Error:', errorThrown);
                    }
                });
            } else {
                alert('Error! Please enter coupon code.');
            }
        } else {
            alert('Error! You can not apply coupon to zero cart value.');
        }
    });
    $('.view_cart_button').on('click', function (e) {
        e.preventDefault();
        console.log(dspp_nonces);
        var nonce = dspp_nonces.dspp_view_cart_button;
        var ajaxUrl = dspp_nonces.ajax_url;
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'dspp_view_cart_button',
                nonce: nonce
            },
            success: function (response) {
                if (response.success) {
                    location.href = dspp_nonces.site_url+"/bits-cart";
                } else {
                    alert(response.message);
                }
            },
            error: function (errorThrown) {
                console.log('Error:', errorThrown);
            }
        });
    });
    
    $(".continue_to_payment_old_address").click(function (e) {
        e.preventDefault();
        var url = window.location.href;
        var urlObj = new URL(url);
        // Get the 'invoice_id' parameter from the URL
        var invoiceId = urlObj.searchParams.get("invoice_id");
        var billing_id = $("input[name='selected_billing_address']:checked").val();
        if (typeof billing_id === 'undefined') {
            alert('Select address or add new');
            return;
        }
        var url = dspp_nonces.site_url+ "/bits-payment?invoice_id="+invoiceId+"&billing_id=" + billing_id;
        window.location.href = url;
    });
    var formSubmitted = false; // Flag to track form submission
    $('#billing-form').on('submit', function (e) {
        e.preventDefault();
        var url = window.location.href;
        var urlObj = new URL(url);
        var invoiceId = urlObj.searchParams.get("invoice_id");
        var nonce = dspp_nonces.check_coupon;
        var ajaxUrl = dspp_nonces.ajax_url;
        if (!formSubmitted) { // Check if form hasn't been submitted already
            var form_data = $(this).serialize();
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'dspp_save_billing_details_ajax',
                    nonce: nonce,
                    data: form_data
                },
                success: function (response) {
                    if (response.status) {
                        var url = dspp_nonces.site_url+ "/bits-payment?invoice_id="+invoiceId+"&billing_id=" + response.billing_id;
                        window.location.href = url;
                    }
                },
                error: function (errorThrown) {
                    console.log('Error:', errorThrown);
                }
            });
        }
    });
    $('#purchaseButton').on('click', function (e) {
        e.preventDefault();
        var nonce = dspp_nonces.dspp_bits_generate_invoice;
        var ajaxUrl = dspp_nonces.ajax_url;
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'dspp_bits_generate_invoice',
                nonce: nonce
            },
            success: function (response) {
                console.log(response);
                if (response.success) {
                    location.href = dspp_nonces.site_url+"/bits-checkout?invoice_id="+response.invoice_id;
                } else {
                    alert(response.message);
                }
            },
            error: function (errorThrown) {
                console.log('Error:', errorThrown);
            }
        });
    });
    $('#ajax-login-form').on('submit', function (e) {
        var nonce = dspp_nonces.dspp_ajax_user_login;
        var ajaxUrl = dspp_nonces.ajax_url;
        e.preventDefault();
        var loginData = {
            'action': 'dspp_ajax_user_login',
            'username': $('#username').val(),
            'password': $('#password').val(),
            'nonce': nonce
        };
        $.ajax({
            type: 'POST',
            url: ajaxUrl,
            
            data: loginData,
            success: function (response) {
                var parsed = JSON.parse(response);
                if (parsed.status == "error") {
                    $(".login-message").html(parsed.message);
                } else {
                    window.location.href = parsed.redirect;
                }
            }
        });
        return false;
    });
});

function dspp_updateQuantity(e) {
    var value = e.value;
    if (value <= 0) {
        remove_row(e);
        return;
    }
    var product_id = parseInt(e.getAttribute('data-product-id'));
    var userId = parseInt(e.getAttribute('data-user-id'));
    var site = e.getAttribute('data-site');
    var price = parseFloat(e.getAttribute('data-price'));
    var nonce = dspp_nonces.update_quantity_in_session;
    var ajaxUrl = dspp_nonces.ajax_url;
    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'dspp_update_quantity_in_session',
            nonce: nonce,
            site: site,
            price: price,
            user_id: userId,
            post_id: product_id,
            value: value
        },
        success: function (response) {
            var parsed = JSON.parse(response);
            if (parsed.status) {
                location.reload();
            }
        },
        error: function (errorThrown) {
            console.log('Error:', errorThrown);
        }
    });
}
function remove_row(e) {
    var row = e.closest('tr');
    var userId = e.getAttribute('data-user-id');
    var site_url = e.getAttribute('data-site');
    var price = e.getAttribute('data-price');
    var totalInvoiceElement = document.getElementById('total-invoice');
    var currentPriceText = totalInvoiceElement.textContent;
    var currentPriceSplitted = currentPriceText.split('$');
    var currentPrice = parseInt(currentPriceSplitted[1]);
    var nonce = dspp_nonces.remove_from_session;
    var ajaxUrl = dspp_nonces.ajax_url;
    var newPrice = currentPrice - price;
    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'dspp_remove_from_session',
            nonce: nonce,
            website_url: site_url,
            user_id: userId
        },
        success: function (response) {
            var parsed = JSON.parse(response);
            if (parsed.status) {
                location.reload();
            }
//            if (newPrice <= 0) {
//                newPrice = 0;
//                jQuery('#purchaseButton').attr('disabled', 'disabled');
//                totalInvoiceElement.textContent = '$0';
//            } else {
//                totalInvoiceElement.textContent = '$' + newPrice;
//            }
//            row.remove();
        },
        error: function (errorThrown) {
            console.log('Error:', errorThrown);
        }
    });
}
