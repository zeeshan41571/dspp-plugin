/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function($) {
    $('#post').submit(function() {
        var hasError = false;
        if (!$('#titlewrap input').val()) {
            $('#titlewrap input').addClass('error');
            hasError = true;
        } else {
            $('#titlewrap input').removeClass('error');
        }
        if (!$('#coupon_value').val()) {
            $('#coupon_value').addClass('error');
            hasError = true;
        } else {
            $('#coupon_value').removeClass('error');
        }
        if (!$('#coupon_expiry_date').val()) {
            $('#coupon_expiry_date').addClass('error');
            hasError = true;
        } else {
            $('#coupon_expiry_date').removeClass('error');
        }
        if (hasError) {
            alert('Please fill in all required fields.');
            return false;
        }
    });
});
