/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


jQuery(document).ready(function ($) {
    $('#generate_coupon_code_button').on('click', function (e) {
        e.preventDefault();
        var randomCode = Math.random().toString(36).substr(2, 8).toUpperCase();
        $('#title').val(randomCode);
        $('#title-prompt-text').hide(); // Hide the placeholder text
    });
});