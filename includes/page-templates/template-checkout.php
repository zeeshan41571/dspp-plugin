<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (isset( $_POST['checkout_page_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['checkout_page_nonce'])) , 'checkout_page_action' ))
{
    $invoice_id = sanitize_key($_GET['invoice_id']);
} else {
    $invoice_id = sanitize_key($_GET['invoice_id']);
}
$user_id_wp = get_current_user_id();
$billing_address = dspp_get_billing_details_by_user_id($user_id_wp);
$user_details = wp_get_current_user();
$invoice_details = dspp_get_invoice_details($invoice_id);
$decoded_details = json_decode($invoice_details[0]['invoice_items'], true);
$cart_data = json_decode($invoice_details[0]['invoice_items'], true);
$json_data = wp_json_encode($cart_data);
$cart_total = 0;
foreach ($cart_data as $item) {
    if (isset($item['price'])) {
        $cart_total += $item['price'] * $item['quantity'];
    }
}
if (isset($cart_data['coupon_code'])) {
    $coupon_code = $cart_data['coupon_code'];
    $coupon_value = $cart_data['coupon_discount_type'] == 'fixed_discount' ? $cart_data['coupon_value'] : (($cart_data['coupon_value'] / 100) * $cart_total);
    $cart_total = $cart_total - $coupon_value;
}
?>
<div class="container-fluid row">
    <h2><a href="<?php echo esc_url(site_url("/bits-cart")); ?>"><i class="fa fa-arrow-left" aria-hidden="true"></i></a> <?php echo esc_html_e('Checkout', 'digital-service-provider-crm'); ?></h2>
    <div class="row-left col-sm-8">

        <a class="btn btn-primary add_new_address">Add New</a>
        <?php if (isset($billing_address) && !empty($billing_address)) : ?>
            <div class="old_addresses">
                <div class="row">
                    <?php
                    $counter = 1;
                    foreach ($billing_address as $address) :
                        ?>
                        <div class="col-md-4 col-lg-4 col-sm-4" style="clear: both;">
                            <label>
                                <input type="radio" name="selected_billing_address" selected class="card-input-element" value="<?php echo esc_attr($address['billing_id']); ?>" />
                                <div class="card card-default card-input">
                                    <div class="card-header"><?php esc_html('Billing Address') ?> <?php echo esc_html($counter); ?></div>
                                    <div class="card-body">
                                        <p class="rich-text-component css-1peebnr e1wnkr790">
                                            <span class="css-ejignk eu4oa1w0"><?php echo esc_html($user_details->user_nicename); ?></span><br>
                                            <span class="css-ejignk eu4oa1w0"><?php echo esc_html($address['billing_address'] . ', ' . $address['billing_city']); ?></span><br>
                                            <span class="css-ejignk eu4oa1w0"><?php echo esc_html($address['billing_state'] . ', ' . $address['billing_country'] . ' ' . $address['billing_post_code']); ?></span>
                                        </p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <?php
                        $counter++;
                    endforeach;
                    ?>
                </div>
                <a class="btn btn-primary continue_to_payment_old_address" href=""><?php esc_html_e('Continue to Payment', 'digital-service-provider-crm'); ?></a>
            </div>
        <?php endif; ?>
        <div class="row new_billing_address_form col-sm-12" <?php echo!empty($billing_address) ? 'style="display:none"' : ''; ?>>
            <form action="#" method="post" class="formbuilder-form" id="billing-form">
                <div>
                    <br>
                    <label for="address"><?php esc_html_e('Billing address', 'digital-service-provider-crm'); ?></label>
                    <div class="form-row">
                        <div class="form-group col-sm-12">
                            <input type="text" class="form-control" id="address_1" name="address_1" required="required" placeholder="<?php esc_attr_e('Address Line 1', 'digital-service-provider-crm'); ?>" />
                        </div>
                        <label for="address"><?php esc_html_e('City', 'digital-service-provider-crm'); ?></label>
                        <div class="form-group col-sm-12">
                            <input type="text" class="form-control" id="address_city" name="address_city" required="required" placeholder="<?php esc_attr_e('City', 'digital-service-provider-crm'); ?>" />
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="address"><?php esc_html_e('Country', 'digital-service-provider-crm'); ?></label>
                        <div class="form-group col-sm-12">
                            <select name="address_country" required="required" class="form-control" id="address_country" data-live-search="true" autocomplete="billing country" data-live-search-style="startsWith"><option selected disabled value>Please select...</option><option value="AF" >Afghanistan</option><option value="AX" >Åland Islands</option><option value="AL" >Albania</option><option value="DZ" >Algeria</option><option value="AS" >American Samoa</option><option value="AD" >Andorra</option><option value="AO" >Angola</option><option value="AI" >Anguilla</option><option value="AQ" >Antarctica</option><option value="AG" >Antigua and Barbuda</option><option value="AR" >Argentina</option><option value="AM" >Armenia</option><option value="AW" >Aruba</option><option value="AU" >Australia</option><option value="AT" >Austria</option><option value="AZ" >Azerbaijan</option><option value="BS" >Bahamas</option><option value="BH" >Bahrain</option><option value="BD" >Bangladesh</option><option value="BB" >Barbados</option><option value="BY" >Belarus</option><option value="BE" >Belgium</option><option value="BZ" >Belize</option><option value="BJ" >Benin</option><option value="BM" >Bermuda</option><option value="BT" >Bhutan</option><option value="BO" >Bolivia</option><option value="BA" >Bosnia and Herzegovina</option><option value="BW" >Botswana</option><option value="BV" >Bouvet Island</option><option value="BR" >Brazil</option><option value="IO" >British Indian Ocean Territory</option><option value="BN" >Brunei Darussalam</option><option value="BG" >Bulgaria</option><option value="BF" >Burkina Faso</option><option value="BI" >Burundi</option><option value="KH" >Cambodia</option><option value="CM" >Cameroon</option><option value="CA" >Canada</option><option value="CV" >Cape Verde</option><option value="CW" >Curaçao</option><option value="KY" >Cayman Islands</option><option value="CF" >Central African Republic</option><option value="TD" >Chad</option><option value="CL" >Chile</option><option value="CN" >China</option><option value="CX" >Christmas Island</option><option value="CC" >Cocos (Keeling) Islands</option><option value="CO" >Colombia</option><option value="KM" >Comoros</option><option value="CG" >Congo</option><option value="CD" >Congo, The Democratic Republic of The</option><option value="CK" >Cook Islands</option><option value="CR" >Costa Rica</option><option value="CI" >Cote D'ivoire</option><option value="HR" >Croatia</option><option value="CU" >Cuba</option><option value="CY" >Cyprus</option><option value="CZ" >Czech Republic</option><option value="DK" >Denmark</option><option value="DJ" >Djibouti</option><option value="DM" >Dominica</option><option value="DO" >Dominican Republic</option><option value="EC" >Ecuador</option><option value="EG" >Egypt</option><option value="SV" >El Salvador</option><option value="GQ" >Equatorial Guinea</option><option value="ER" >Eritrea</option><option value="EE" >Estonia</option><option value="ET" >Ethiopia</option><option value="FK" >Falkland Islands (Malvinas)</option><option value="FO" >Faroe Islands</option><option value="FJ" >Fiji</option><option value="FI" >Finland</option><option value="FR" >France</option><option value="GF" >French Guiana</option><option value="PF" >French Polynesia</option><option value="TF" >French Southern Territories</option><option value="GA" >Gabon</option><option value="GM" >Gambia</option><option value="GE" >Georgia</option><option value="DE" >Germany</option><option value="GH" >Ghana</option><option value="GI" >Gibraltar</option><option value="GR" >Greece</option><option value="GL" >Greenland</option><option value="GD" >Grenada</option><option value="GP" >Guadeloupe</option><option value="GU" >Guam</option><option value="GT" >Guatemala</option><option value="GG" >Guernsey</option><option value="GN" >Guinea</option><option value="GW" >Guinea-bissau</option><option value="GY" >Guyana</option><option value="HT" >Haiti</option><option value="HM" >Heard Island and Mcdonald Islands</option><option value="VA" >Holy See (Vatican City State)</option><option value="HN" >Honduras</option><option value="HK" >Hong Kong</option><option value="HU" >Hungary</option><option value="IS" >Iceland</option><option value="IN" >India</option><option value="ID" >Indonesia</option><option value="IR" >Iran, Islamic Republic of</option><option value="IQ" >Iraq</option><option value="IE" >Ireland</option><option value="IM" >Isle of Man</option><option value="IL" >Israel</option><option value="IT" >Italy</option><option value="JM" >Jamaica</option><option value="JP" >Japan</option><option value="JE" >Jersey</option><option value="JO" >Jordan</option><option value="KZ" >Kazakhstan</option><option value="KE" >Kenya</option><option value="KI" >Kiribati</option><option value="XK" >Kosovo</option><option value="KP" >Korea, Democratic People's Republic of</option><option value="KR" >Korea, Republic of</option><option value="KW" >Kuwait</option><option value="KG" >Kyrgyzstan</option><option value="LA" >Lao People's Democratic Republic</option><option value="LV" >Latvia</option><option value="LB" >Lebanon</option><option value="LS" >Lesotho</option><option value="LR" >Liberia</option><option value="LY" >Libyan Arab Jamahiriya</option><option value="LI" >Liechtenstein</option><option value="LT" >Lithuania</option><option value="LU" >Luxembourg</option><option value="MO" >Macao</option><option value="MK" >North Macedonia</option><option value="MG" >Madagascar</option><option value="MW" >Malawi</option><option value="MY" >Malaysia</option><option value="MV" >Maldives</option><option value="ML" >Mali</option><option value="MT" >Malta</option><option value="MH" >Marshall Islands</option><option value="MQ" >Martinique</option><option value="MR" >Mauritania</option><option value="MU" >Mauritius</option><option value="YT" >Mayotte</option><option value="MX" >Mexico</option><option value="FM" >Micronesia, Federated States of</option><option value="MD" >Moldova, Republic of</option><option value="MC" >Monaco</option><option value="MN" >Mongolia</option><option value="ME" >Montenegro</option><option value="MS" >Montserrat</option><option value="MA" >Morocco</option><option value="MZ" >Mozambique</option><option value="MM" >Myanmar</option><option value="NA" >Namibia</option><option value="NR" >Nauru</option><option value="NP" >Nepal</option><option value="NL" >Netherlands</option><option value="AN" >Netherlands Antilles</option><option value="NC" >New Caledonia</option><option value="NZ" >New Zealand</option><option value="NI" >Nicaragua</option><option value="NE" >Niger</option><option value="NG" >Nigeria</option><option value="NU" >Niue</option><option value="NF" >Norfolk Island</option><option value="MP" >Northern Mariana Islands</option><option value="NO" >Norway</option><option value="OM" >Oman</option><option value="PK" selected>Pakistan</option><option value="PW" >Palau</option><option value="PS" >Palestinian Territory, Occupied</option><option value="PA" >Panama</option><option value="PG" >Papua New Guinea</option><option value="PY" >Paraguay</option><option value="PE" >Peru</option><option value="PH" >Philippines</option><option value="PN" >Pitcairn</option><option value="PL" >Poland</option><option value="PT" >Portugal</option><option value="PR" >Puerto Rico</option><option value="QA" >Qatar</option><option value="RE" >Reunion</option><option value="RO" >Romania</option><option value="RU" >Russian Federation</option><option value="RW" >Rwanda</option><option value="SH" >Saint Helena</option><option value="KN" >Saint Kitts and Nevis</option><option value="LC" >Saint Lucia</option><option value="PM" >Saint Pierre and Miquelon</option><option value="VC" >Saint Vincent and The Grenadines</option><option value="WS" >Samoa</option><option value="SM" >San Marino</option><option value="ST" >Sao Tome and Principe</option><option value="SA" >Saudi Arabia</option><option value="SN" >Senegal</option><option value="RS" >Serbia</option><option value="SC" >Seychelles</option><option value="SL" >Sierra Leone</option><option value="SG" >Singapore</option><option value="SK" >Slovakia</option><option value="SI" >Slovenia</option><option value="SB" >Solomon Islands</option><option value="SO" >Somalia</option><option value="ZA" >South Africa</option><option value="GS" >South Georgia and The South Sandwich Islands</option><option value="ES" >Spain</option><option value="LK" >Sri Lanka</option><option value="SD" >Sudan</option><option value="SR" >Suriname</option><option value="SJ" >Svalbard and Jan Mayen</option><option value="SZ" >Swaziland</option><option value="SE" >Sweden</option><option value="CH" >Switzerland</option><option value="SX" >St-Martin</option><option value="SY" >Syrian Arab Republic</option><option value="TW" >Taiwan</option><option value="TJ" >Tajikistan</option><option value="TZ" >Tanzania, United Republic of</option><option value="TH" >Thailand</option><option value="TL" >Timor-leste</option><option value="TG" >Togo</option><option value="TK" >Tokelau</option><option value="TO" >Tonga</option><option value="TT" >Trinidad and Tobago</option><option value="TN" >Tunisia</option><option value="TR" >Turkey</option><option value="TM" >Turkmenistan</option><option value="TC" >Turks and Caicos Islands</option><option value="TV" >Tuvalu</option><option value="UG" >Uganda</option><option value="UA" >Ukraine</option><option value="AE" >United Arab Emirates</option><option value="GB" >United Kingdom</option><option value="US" >United States</option><option value="UM" >United States Minor Outlying Islands</option><option value="UY" >Uruguay</option><option value="UZ" >Uzbekistan</option><option value="VU" >Vanuatu</option><option value="VE" >Venezuela</option><option value="VN" >Viet Nam</option><option value="VG" >Virgin Islands, British</option><option value="VI" >Virgin Islands, U.S.</option><option value="WF" >Wallis and Futuna</option><option value="EH" >Western Sahara</option><option value="YE" >Yemen</option><option value="ZM" >Zambia</option><option value="ZW" >Zimbabwe</option></select>
                        </div>
                        <label for="address_state"><?php esc_html_e('State', 'digital-service-provider-crm'); ?></label>
                        <div class="form-group col-sm-12">
                            <input type="text" class="form-control" id="address_state" name="address_state" required="required" placeholder="<?php esc_attr_e('State', 'digital-service-provider-crm'); ?>" />
                        </div>
                        <label for="address_postcode"><?php esc_html_e('Zip Code', 'digital-service-provider-crm'); ?></label>
                        <div class="form-group col-sm-12">
                            <input type="text" class="form-control" id="address_postcode" name="address_postcode" required="required" placeholder="<?php esc_attr_e('Zip Code', 'digital-service-provider-crm'); ?>" />
                        </div>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input add-company" name="company" id="company"/>
                        <label for="company" class="mb-3 custom-control-label"><?php esc_html_e("I'm purchasing for a company", 'digital-service-provider-crm'); ?></label>
                        <div class="company-fields">
                            <div class="form-row">
                                <label for="company_name"><?php esc_html_e('Company Name', 'digital-service-provider-crm'); ?></label>
                                <div class="form-group col-sm-12">
                                    <input type="text" class="form-control" name="company_name" id="company_name"/>
                                </div>
                                <label for="tax_id"><?php esc_html_e('Tax ID', 'digital-service-provider-crm'); ?></label>
                                <div class="form-group col-sm-12">
                                    <input type="text" class="form-control" id="tax_id" name="tax_id"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <input type="submit" class="btn btn-primary" value="<?php esc_attr_e('Continue to payment', 'digital-service-provider-crm'); ?>">
                </div>
            </form>
        </div>
    </div>
    <aside class="mr-auto checkout-right col-sm-4">
        <div class="sticky">
            <div class="invoice-items" id="preview">
                <h2 class="mb-4"><?php esc_html_e('Summary', 'digital-service-provider-crm'); ?></h2>
                <div class="cart-contents">
                    <?php foreach ($cart_data as $cart_items) : ?>
                        <?php if (!is_array($cart_items)) {
                            continue;
                        } ?>
                        <div class="mb-4 d-flex justify-content-between">
                            <div>
                                <div class="text-500"><?php echo esc_html($cart_items['site']); ?></div>
                                <div>
                                    <span class="mr-1 text-muted"><?php esc_html_e('Qty', 'digital-service-provider-crm'); ?></span> <?php echo esc_html($cart_items['quantity']); ?>
                                </div>
                            </div>
                            <div class="text-right text-500">
                                $<?php echo esc_html($cart_items['price']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <hr />
<?php if (isset($cart_data['coupon_code'])) : ?>
                        <div class="mb-4 d-flex justify-content-between">
                            <div>
                                <!--Translators: %s is the placeholder representing a variable.-->
                                <div class="text-500"><?php printf('Discount (%s)', esc_html($cart_data['coupon_code'])); ?></div>
                                <div class="text-muted"><?php esc_html('USD'); ?></div>
                            </div>
                            <div class="text-right">
                                <h2 class="margin-0">
                                    $<?php echo ($coupon_value == 0) ? 0 : esc_html($coupon_value); ?>
                                </h2>
                            </div>
                        </div>
<?php endif; ?>

                    <!-- Show total and payment terms -->
                    <div class="mb-4 d-flex justify-content-between">
                        <div>
                            <div class="text-500"><?php esc_html_e('Total', 'digital-service-provider-crm'); ?></div>
                            <div class="text-muted"><?php esc_html_e('USD', 'digital-service-provider-crm'); ?></div>
                        </div>
                        <div class="text-right">
                            <h2 class="margin-0">
                                $<?php echo esc_html($cart_total); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>