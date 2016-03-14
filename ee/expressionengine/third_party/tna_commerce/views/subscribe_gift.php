<? $this->view('errors'); ?>

<form id="subscribe_form" name="subscribe_form" action="<?= $https_site_url ?>subscribe/payment_gift" method="post">
    <input type="hidden" name="create_member" value="1" />
    <input type="hidden" name="gift" value="1" />
    <input type="hidden" name="include_extras" value="1" />
    <input type="hidden" name="XID" value="<?= $xid_hash ?>">

    {!-- <input type="hidden" name="include_extras" id ="include_extras" value="1"> --}
    
    <input type="hidden" name="subscription_type" id="subscription_type" value="yearly">
    


    {!-- <div class="alert alert-info lead" role="alert">Recipient will receive a one year subscription - <strong>AUD $<?= $yearly_amount ?> + postage</strong>, charged to your credit card.</div> --}
    
    <legend>Recipient Details</legend>
       
  {!--
<div class="checkbox" style="display:none">
    
  <label>
    <input checked name="include_extras" id ="include_extras" type="checkbox" value="1">
      Yes, please send me the t-shirt &amp; DVD !
  </label>
</div>
  --}
    
    

    
    
    <div id='postage_settings'>

            <div class="form-group">
                    <label for="country">Destination Country for Postage</label>
                    <select class="form-control" name="r_country" id="r_country">
                        <?
                        $selected_country = ($countrycode)?$countrycode:'';
                        //$selected_country = ($country)?$country:$selected_country;
                        
                        foreach ($countrylist as $key => $value) {
                            $selected = ($key==$selected_country)?'selected':'';
                             echo "<option $selected value='$key' label='$value'>$value</option>";
                        }
                        
                        ?>
                    </select>
            </div>
        
                     <p>(Postage &amp; Handling: <span class='postage_cost_label' id='postage_cost_label'>AUD $<?= $standard_domestic ?></span>)</p>

             <div class="form-group">
                    <label>T-Shirt Size</label>

                    <select class="form-control" name="r_tshirt_size" id="r_tshirt_size">
                        <option value="XS">Extra Small</option>
                        <option value="S">Small</option>
                        <option value="M">Medium</option>
                        <option selected value="L">Large</option>
                        <option value="XL">Extra Large</option> 
                    </select>  
             </div>
    </div>

    
    <div class="form-group">
        <label for="r_email">Email Address</label>
        <input class="form-control" type="text" id="r_email" name="r_email" maxlength="100" value="<?= $r_email ?>" />
    </div>
    
    
      <div class="form-group">
        <label for="r_first_name">First Name</label>
        <input class="form-control" type="text" id="r_first_name" name="r_first_name" maxlength="100" value="<?= $r_first_name ?>" />
    </div>

    <div class="form-group">
        <label for="r_last_name">Last Name</label>
        <input class="form-control" type="text" id="r_last_name" name="r_last_name" maxlength="100" value="<?= $r_last_name ?>" />
    </div>


    <legend>Mailing Address</legend>


    <div class="form-group">
        <label for="r_address">Street Address</label>
        <input class="form-control" type="text" id="r_address" name="r_address" maxlength="100" value="<?= $r_address ?>" />
    </div>

    <div class="form-group">
        <label for="r_address_2">Address Line 2</label>
        <input class="form-control" type="text" id="r_address_2" name="r_address_2" maxlength="100" value="<?= $r_address_2 ?>" />
    </div>

    <div class="form-group">
        <label for="city">Suburb/City</label>
        <input class="form-control" type="text" id="r_suburb" name="r_suburb" maxlength="100" value="<?= $r_suburb ?>" />
    </div>
    <div class="form-group">
        <label for="r_suburb">State/Province</label>
        <input class="form-control" type="text" id="r_state" name="r_state" value="<?= $r_state ?>" />
    </div>
    
    <div class="form-group">
        <label for="r_postal_code">Postal Code/Zip</label>
        <input class="form-control" type="text" id="r_postal_code" name="r_postal_code" maxlength="100" value="<?= $r_postal_code ?>" />
    </div>
    
    <div class="checkbox">
    
      <label>
            <input name="secret_gift" id ="secret_gift" type="checkbox" value="1">
            <strong>Hide details from recipient.</strong> By default your recipient will be advised that you have sent them this gift. If this box is checked, your identity will remain hidden.
      </label>
    </div>


    <div class="form-group">

        <input class="btn btn-success" type="submit" value="Proceed to Checkout &raquo;">
    </div>
    

</form>

<script>

        
    $().ready(function() {
        
        $("#subscribe_form").validate({
            
            submitHandler: function(form) {
                //$("#submit_payment").attr('disabled', 'disabled');
                 $(form).find(":submit").attr("disabled", 'disabled').attr("value", "Submitting..."); 
                 $("#please_wait").show();
                form.submit();
            },
            
            rules: {
                r_email: {
                    required: true,
                    email: true
                },
                r_first_name: {
                    required: true,
                    minlength: 2,
                },
                r_last_name: {
                    required: true,
                    minlength: 2,
                },
                r_address: {
                    required: true
                },
                r_suburb: {
                    required: true
                },
                r_state: {
                    required: true
                },
                r_postal_code: {
                    required: true
                },
                r_country: {
                    required: true
                }
            },
            messages: {
                r_email:"Please enter a valid email address",
                r_first_name: {
                    required: "Please enter a first name",
                    minlength: "Your first name must consist of at least 2 characters"
                },
                r_last_name: {
                    required: "Please enter a last name",
                    minlength: "Your last name must consist of at least 2 characters"
                },
                r_address: "Please enter a street address",
                r_suburb: "Please enter a suburb or city",
                r_state: "Please enter a State or Province",
                r_country: "Please enter a country",
                r_postal_code: "Please enter a postal or zip code"
            }
        });
    });
  </script>
    
     <? $this->view('subscribe_gift_js'); ?>
     
     <? $this->view('shared_js'); ?>
     
     <script>
       selected = $("#r_country").val();
       update_country(selected);
     </script>

    
   
    
  
   
    
 
