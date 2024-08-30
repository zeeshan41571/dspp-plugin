(function ($) {
    'use strict';
    new DataTable("#order_forms");
    new DataTable("#customers");

//    $('.delete_order_form').on('click', function () {
//        var formId = $(this).data('form-id');
//        if (confirm('Are you sure you want to delete this form?')) {
//            // Send AJAX request to update form_status
//            $.ajax({
//                type: 'POST',
//                url: ajaxurl, // WordPress AJAX URL
//                data: {
//                    action: 'delete_order_form', // AJAX action hook
//                    nonce: 'delete_order_form', // AJAX action hook
//                    form_id: formId // Form ID to delete
//                },
//                success: function (response) {
//                    var result = JSON.parse(response);
//                    if (result.success) {
//                        location.reload();
//                    } else {
//                        alert('Failed to delete form. Please try again.');
//                    }
//                },
//                error: function (error) {
//                    // Handle errors (if any)
//                    console.error(error);
//                }
//            });
//        }
//    });
//    $('.change_status_order_form').on('click', function () {
//        var formId = $(this).data('form-id');
//        var newStatus = $(this).data('new-status');
//        if (confirm('Are you sure you want to ' + newStatus.toLowerCase() + ' this form?')) {
//            $.ajax({
//                type: 'POST',
//                url: ajaxurl,
//                data: {
//                    action: 'change_status_order_form', // AJAX action hook
//                    nonce: 'change_status_order_form', // AJAX action hook
//                    form_id: formId, // Form ID to update
//                    new_status: newStatus // New status for the form
//                },
//                success: function (response) {
//                    var result = JSON.parse(response);
//                    if (result.success) {
//                        location.reload();
//                    } else {
//                        alert('Failed to change the form status. Please try again.');
//                    }
//                },
//                error: function (error) {
//                    // Handle errors (if any)
//                    console.error(error);
//                }
//            });
//        }
//    });
//    $('.order-form-type').change(function () {
//        var selectedText = $(this).find(':selected').text();
//        var text = selectedText.toLowerCase().split(" ").join("");
//        if (text === 'intakeforms') {
//            $('.order-form-services-list').show();
//        } else {
//            $('.order-form-services-list').hide();
//        }
//    });
//    $('.order-form-type').change();
})(jQuery);
//jQuery($ => {
//    var services;
//    get_services(function (response) {
//        const fbTemplate = document.getElementById('build-wrap');
//        if (response.success) {
//            services = response.data;
//            var fields = [
//                {
//                    "type": "select",
//                    "required": false,
//                    "label": "Services",
//                    "className": "form-control",
//                    "name": "select-1702874177457-0",
//                    "access": true,
//                    "input": true,
//                    "multiple": true,
//                    "class": "order-form-services-list",
//                    "multiselect-search": true,
//                    "multiselect-select-all": true,
//                    "multiselect-max-items": 3,
//                    "id": "demo-multiple-select",
//                    "values": services.map(service => {
//                        return {
//                            "label": service.label,
//                            "value": service.value,
//                            "selected": service.selected
//                        };
//                    })
//                }
//            ];
//            
//            var formBuilder = $(fbTemplate).formBuilder({fields});
//            formBuilder.promise.then(function (fb) {
//                console.log(formBuilder.actions.addField);
//            });
//        } else {
//            var formBuilder = $(fbTemplate).formBuilder();
//        }
//        $('#add-new-order-form').submit(function () {
//            // Prevent default form submission
//            event.preventDefault();
//            // Serialize form data
//            var selectedValues = [];
//            var selectElement = document.getElementById('demo-multiple-select');
//
//            for (var i = 0; i < selectElement.options.length; i++) {
//                if (selectElement.options[i].selected) {
//                    selectedValues.push(selectElement.options[i].value);
//                }
//            }
//            console.log(selectedValues);
//
//            var formData = $(this).serialize();
//            var buildWrapContent = formBuilder.actions.getData('json', true);
//            formData += '&buildWrapContent=' + buildWrapContent;
//            // Use AJAX to send data to the server
//            $.ajax({
//                type: 'post',
//                url: ajaxurl, // This is defined in functions.php
//                data: {
//                    action: 'save_order_form',
//                    nonce: 'save_order_form',
//                    formData: formData,
//                    selectedValues: selectedValues
//                },
//                success: function (response) {
//                    console.log(response.success);
//                    var redirectUrl = '?page=spp_order_forms';
//                    if (response.success) {
//                        window.location.href = redirectUrl;
//                    }
//                }
//            });
//        });
//    });
//    function get_services(callback) {
//        $.ajax({
//            type: 'post',
//            url: ajaxurl,
//            data: {
//                action: 'get_all_services'
//            },
//            success: function (response) {
//                // Execute the callback function with the response
//                callback(response);
//            }
//        });
//    }
//    function get_payment_methods(callback) {
//        $.ajax({
//            type: 'post',
//            url: ajaxurl,
//            data: {
//                action: 'get_all_payment_methods'
//                nonce: 'get_all_payment_methods'
//            },
//            success: function (response) {
//                // Execute the callback function with the response
//                callback(response);
//            }
//        });
//    }
//});

jQuery(document).ready(function ($) {
    // Hide all tab contents
//    $('.payment-method-tab-content').addClass('hidden');

    // Show the active tab content
    var activeTab = $('.payment-method-tab.active');
    if (activeTab.length) {
        var selectedTab = activeTab.data('tab');
        $('#' + selectedTab + '_tab').removeClass('hidden');
    }

    // Attach click event to tabs
    $('.payment-method-tab').click(function () {
        var selectedTab = $(this).data('tab');
        // Hide all tab contents
        $('.payment-method-tab').removeClass('active');
        $('.payment-method-tab-content').addClass('hidden');
        // Show the selected tab content
        $(this).addClass('active');
        $('#' + selectedTab + '_tab').removeClass('hidden');
    });
});

//code from order_form_settings
//        $('.delete-form-type').on('click', function (e) {
//            e.preventDefault();
//            var form_type_id = $(this).data('form-type-id');
//            console.log(form_type_id);
//            // Show confirmation dialog
//            if (confirm('Are you sure you want to delete this status?')) {
//                // AJAX request to delete the status
//                $.ajax({
//                    url: ajaxurl,
//                    type: 'POST',
//                    data: {
//                        action: 'dspp_delete_form_type_action',
//                        form_type_id: form_type_id
//                    },
//                    success: function (response) {
//                        if (response.success) {
//                            location.reload();
//                        } else {
//                            alert('Failed to delete status. Please try again.');
//                        }
//                    },
//                    error: function () {
//                        alert('Error during the AJAX request.');
//                    }
//                });
//            }
//        });
//        $('.edit-form-type').on('click', function (e) {
//            e.preventDefault();
//            var form_type_id = $(this).data('form-type-id');
//            // AJAX request to retrieve status details
//            $.ajax({
//                url: ajaxurl,
//                type: 'POST',
//                data: {
//                    action: 'dspp_get_form_type_details_action',
//                    form_type_id: form_type_id
//                },
//                success: function (response) {
//                    if (response.success) {
//                        // Populate the edit form with retrieved data
//                        $('#edit-form-type-name').val(response.data.form_type_name);
//                        $('#edit-form-type-description').val(response.data.form_type_description);
//                        $('#edit-form-type-id').val(form_type_id);
//
//                        // Show the edit form
//                        $('#edit-form-type-modal').show();
//                    } else {
//                        alert('Failed to retrieve status details. Please try again.');
//                    }
//                },
//                error: function () {
//                    alert('Error during the AJAX request.');
//                }
//            });
//        });

//        $('#edit-form-type-form').on('submit', function (e) {
//            e.preventDefault();
//            // AJAX request to update status details
//            $.ajax({
//                url: ajaxurl,
//                type: 'POST',
//                data: {
//                    action: 'dspp_edit_form_type_action',
//                    form_type_id: $('#edit-form-type-id').val(),
//                    form_type_name: $('#edit-form-type-name').val(),
//                    form_type_description: $('#edit-form-type-description').val(),
//                },
//                success: function (response) {
//                    if (response.success) {
//                        // Close the modal or perform any other actions
//                        $('#edit-form-type-modal').hide();
//                        location.reload();
//                    } else {
//                        alert('Failed to update status. Please try again.');
//                    }
//                },
//                error: function () {
//                    alert('Error during the AJAX request.');
//                }
//            });
//        });

// code from order_form_details
//jQuery($ => {
//        const fbTemplate = document.getElementById('build-wrap-update');
//        var formBuilder = $(fbTemplate).formBuilder();
//        formBuilder.promise.then(function (fb) {
//            var formData = <?php // echo esc_html($validJsonString); ?>;
//            fb.actions.setData(formData);
//        });
//        $('#update-order-form').submit(function () {//
//            event.preventDefault();
//            var formData = $(this).serialize();
//            var selectedValues = [];
//            var selectElement = document.getElementById('demo-multiple-select');
//            for (var i = 0; i < selectElement.options.length; i++) {
//                if (selectElement.options[i].selected) {
//                    selectedValues.push(selectElement.options[i].value);
//                }
//            }
//            var buildWrapContent = formBuilder.actions.getData('json', true);
//            formData += '&buildWrapContent=' + buildWrapContent;
//            $.ajax({
//                type: 'post',
//                url: ajaxurl,
//                data: {
//                    action: 'dspp_update_order_form',
//                    nonce: 'dspp_update_order_form',
//                    formData: formData,
//                    selectedValues: selectedValues
//                },
//                success: function (response) {
//                    var redirectUrl = '?page=spp_order_forms';
//                    if (response.success) {
//                        window.location.href = redirectUrl;
//                    }
//                }
//            });
//        });
//    });