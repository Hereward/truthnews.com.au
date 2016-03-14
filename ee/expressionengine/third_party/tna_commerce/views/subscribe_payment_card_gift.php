<? $this->view('errors'); ?>

<? if (count($errors) == 0) {  ?>

<div class="panel panel-default">
    <div class="panel-heading">
       <h3 class="panel-title">Your Gift Details</h3>
    </div>
    <div class="panel-body">
        You are purchasing a one year subscription for <strong><?=$r_first_name?> <?=$r_last_name?></strong>.<br>
        <strong>Email:</strong> <?=$r_email?>.<br>
        <strong>Street Address:</strong> <?=$r_address?> <?=$r_address_2?> <?=$r_suburb?> <?=$r_state?> <?=$r_postal_code?> <?=$countrylist[$r_country]?>. <br>
        <strong>T-shirt Size:</strong> <?=$r_tshirt_size?>. <br>
       
       <div class="text-right" style="max-width:200px; margin-bottom:10px; margin-top:10px;">
           Subscription : AUD $<?=$subscription_details->aud_price?><br>
           <? if($subscription_type == 'yearly') { ?>
           Postage: <span id='postage_cost_label'></span><br>
              
           <?}?>

           <strong>Total Cost: AUD $<span id='total_cost_label'></span></strong><br>
       </div>
       

    </div>
    
   <div class="panel-footer"><a id="go_back" href="#"> &laquo; Go Back / Change Details</a></div>
</div>

<? } ?>



<form id="cc_form" name="cc_form" method="post">

    <input type="hidden" name="member_id" value="<?= $member_id ?>" />
    <input type="hidden" name="email" value="<?= $email ?>" />
    <input type="hidden" name="subscription_type" value="<?= $subscription_type ?>" />
    <input type="hidden" name="tshirt_size" value="<?= $tshirt_size ?>" />
    <input type="hidden" name="RebillCustomerID" value="<?= $RebillCustomerID ?>" />
    <input type="hidden" id="postage_cost" name="postage_cost" value="<?= $postage_cost ?>" />
    <input type="hidden" id="aud_price" name="aud_price" value="<?= $subscription_details->aud_price ?>" />
    
    <input type="hidden" id="total_cost" name="total_cost" value="<?= $total_cost ?>" />
    <input type="hidden" id="include_extras" name="include_extras" value="<?= $include_extras?>" />
    
    <input type="hidden" id='r_tshirt_size' name='r_tshirt_size' value="<?= $r_tshirt_size?>" />
    <input type="hidden" id='r_email' name='r_email' value="<?= $r_email?>" />
    <input type="hidden" id='r_first_name' name='r_first_name' value="<?= $r_first_name?>" />
    <input type="hidden" id='r_last_name' name='r_last_name' value="<?= $r_last_name?>" />
    <input type="hidden" id='r_address'  name='r_address' value="<?= $r_address?>" />
    <input type="hidden" id='r_address_2'  name='r_address_2' value="<?= $r_address_2?>" />
    <input type="hidden" id='r_suburb'  name='r_suburb' value="<?= $r_suburb?>" />
    <input type="hidden" id='r_state'  name='r_state' value="<?= $r_state?>" />
    <input type="hidden" id='r_country'  name='r_country' value="<?= $r_country?>" />
    <input type="hidden" id='r_postal_code' name='r_postal_code' value="<?= $r_postal_code?>" />
    <input type="hidden" id='r_state'  name='r_state' value="<?= $r_state?>" />
    <input type="hidden" id='secret_gift'  name='secret_gift' value="<?= $secret_gift?>" />
    <input type="hidden" name="XID" value="<?= $xid_hash ?>">
    

    <legend>Card Holder Details</legend>
    
        <div class="form-group">
        <label for="email">Email Address</label>
        <input class="form-control" type="text" id="email" name="email" maxlength="100" value="<?= $email ?>" />
    </div>
    
    <div class="form-group">
        <label for="first_name">First Name (as it appears on card)</label>
        <input class="form-control" type="text" id="first_name" name="first_name" maxlength="100" value="<?= $first_name ?>" />
    </div>

    <div class="form-group">
        <label for="last_name">Last Name (as it appears on card)</label>
        <input class="form-control" type="text" id="last_name" name="last_name" maxlength="100" value="<?= $last_name ?>" />
    </div>

    <div class="form-group">
        <label>Card Number</label>
        <input class="form-control" type="text" name="cc_number" value="<?= $cc_number ?>" />
    </div>

    <div class="form-group">

        <label>Expiration: Month</label>

        <select class="form-control" name="cc_expiry_month">
            <?
            foreach ($months as $month) {
            $selected = ($month==$cc_expiry_month)?'selected':'';
            echo "<option $selected value='$month' label='$month'>$month</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label>Expiration: Year</label>
        <select class="form-control" name="cc_expiry_year">  
            <?
            foreach ($years as $year) {
            $selected = ($year==$cc_expiry_year)?'selected':'';
            echo "<option $selected value='$year' label='$year'>$year</option>";
            }
            ?> 
        </select>
    </div>

    <div class="form-group">
        <label>CVV Code <a href="http://www.cvvnumber.com/cvv.html" target="_blank" style="font-size:11px">What is my CVV code?</a></label>
        <input class="form-control" type="text" id="cc_cvn" name="cc_cvn" value="<?= $cc_cvn ?>" />
    </div>



    {!--
    <div class="form-group">
        <label>Security Code</label>
        <input class="form-control" type="text" name="cc_cvv2" value="" />
    </div>
    --}

    <div class="form-group">
        <label for="company">Company</label>
        <input class="form-control" type="text" id="company" name="company" value="<?= $company ?>" />
    </div>
    <div class="form-group">
        <label for="address">Street Address</label>
        <input class="form-control" type="text" id="address" name="address" maxlength="100" value="<?= $address ?>" />
    </div>

    <div class="form-group">
        <label for="address_2">Address Line 2</label>
        <input class="form-control" type="text" id="address_2" name="address_2" maxlength="100" value="<?= $address_2 ?>" />
    </div>

    <div class="form-group">
        <label for="city">Suburb/City</label>
        <input class="form-control" type="text" id="suburb" name="suburb" maxlength="100" value="<?= $suburb ?>" />
    </div>
    <div class="form-group">
        <label for="region_other">State/Province</label>
        <input class="form-control" type="text" id="state" name="state" value="<?= $state ?>" />
    </div>
    <div class="form-group">
        <label for="country">Country</label>
        <select class="form-control" name="country" id="country">
            <?
            $selected_country = ($countrycode)?$countrycode:'';
            $selected_country = ($country)?$country:$selected_country;

            foreach ($countrylist as $key => $value) {
            $selected = ($key==$selected_country)?'selected':'';
            echo "<option $selected value='$key' label='$value'>$value</option>";
            }

            ?>

        </select>
    </div>
    <div class="form-group">
        <label for="postal_code">Postal Code/Zip</label>
        <input class="form-control" type="text" id="postal_code" name="postal_code" maxlength="100" value="<?= $postal_code ?>" />
    </div>

    <div class="checkbox">
        <label>
            <input id="terms" name="terms" type="checkbox"> I have read and agree with the 
            <a href="<?= $https_site_url . $uri_string ?>#terms">Terms and Conditions</a>.
        </label>
    </div>


    <div class="form-group">
        <input class="btn btn-success" name="submit_payment_button" id="submit_payment_button" type="submit" value="Purchase My Subscription">
        <p id="please_wait" style="display:none; color:red;" class="help-block bold">Please wait while we process your payment...</p>
    </div>
    
    <input type="hidden" name="submit_payment" value="1" />

</form>


<? $this->view('terms'); ?>





<script>
    
  
    function set_initial_values() {
        var include_extras = '<?= $include_extras ?>';
                //$('#include_extras').is(':checked');
        if (include_extras) {
            selected = $("#r_country").val();  
            
            update_country(selected);
            
        } else {
            $("#postage_cost_label").html('0 (we won&#39;t send you anything by mail)');
            $("#postage_cost").val('0');
        }
   
    }
    
    function calculate_totals() {
        
        postage = parseFloat($("#postage_cost").val());
        //postage = postage.toFixed(2);
        sub = parseFloat($("#aud_price").val());
        //sub = sub.toFixed(2);
        total = postage+sub;
        total = total.toFixed(2);
        $("#total_cost_label").text(total);
        $("#total_cost").val(total);
        
        //$("#total_cost").val(total);
        //alert("TOTAL = " + total);  
    }
    
    $().ready(function() {
        

        $('#go_back').click(function() {
            parent.history.back();
            return false;
        });
        
        $("#cc_form").validate({
            
            submitHandler: function(form) {
                //$("#submit_payment").attr('disabled', 'disabled');
                 $(form).find(":submit").attr("disabled", 'disabled').attr("value", "Submitting..."); 
                 $("#please_wait").show();
                form.submit();
            },
            
            rules: {
                email: {
                    required: true,
                    email: true
                },
                first_name: {
                    required: true,
                    minlength: 2,
                },
                last_name: {
                    required: true,
                    minlength: 2,
                },
                address: {
                    required: true
                },
                suburb: {
                    required: true
                },
                state: {
                    required: true
                },
                postal_code: {
                    required: true
                },
                country: {
                    required: true
                },
                terms: {
                    required: true
                },
                cc_number: {
                    required: true,
                    creditcard: true
                },
                cc_cvn: {
                    required: true,
                    digits: true
                }
            },
            messages: {
                email:"Please enter a valid email address",
                first_name: {
                    required: "Please enter a first name",
                    minlength: "Your first name must consist of at least 2 characters"
                },
                last_name: {
                    required: "Please enter a last name",
                    minlength: "Your last name must consist of at least 2 characters"
                },
                address: "Please enter a street address",
                suburb: "Please enter a suburb or city",
                state: "Please enter a State or Province",
                country: "Please enter a country",
                postal_code: "Please enter a postal or zip code",
                cc_number: {
                    required: "Please enter a credit card number",
                    creditcard: "Please enter a valid credit card number"
                },
                terms: "Please confirm that you agree with our terms and conditions."
            }
        });
    });


</script>

<? $this->view('shared_js'); ?>

<? if (count($errors) == 0) {  ?>
    <script>
       set_initial_values();
       calculate_totals();
    </script>
<? } ?>

