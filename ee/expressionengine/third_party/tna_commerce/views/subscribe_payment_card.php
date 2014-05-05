{!--
<div class="alert alert-success"><strong>Note:</strong> Blah....
    <br/>

</div>
--}

<form id="cc_form" name="cc_form" method="post">
    
    <input type="hidden" name="member_id" value="<?=$member_id?>" />

    <!-- This credit card fieldset is not required for free or external checkout (e.g., PayPal Express Checkout) payment methods. -->

    {!-- <legend>Credit or Debit Card Information</legend>  --}
{!--
    <div class="form-group">
        <label>Credit Card Name</label>
        <input class="form-control" type="text" name="cc_name" value="" />
    </div>
--}


    <div class="form-group">
        <label for="first_name">First Name (as it appears on card)</label>
        <input class="form-control" type="text" id="first_name" name="first_name" maxlength="100" value="<?=$first_name?>" />
    </div>

    <div class="form-group">
        <label for="last_name">Last Name (as it appears on card)</label>
        <input class="form-control" type="text" id="last_name" name="last_name" maxlength="100" value="<?=$last_name?>" />
    </div>

    <div class="form-group">
        <label>Card Number</label>
        <input class="form-control" type="text" name="cc_number" value="" />
    </div>

    <div class="form-group">

        <label>Expiration: Month</label>

        <select class="form-control" name="cc_expiry_month"><option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>Expiration: Year</label>

            <select class="form-control" name="cc_expiry_year">
                <option value="2014">2014</option>
                <option value="2015">2015</option>
                <option value="2016">2016</option>
                <option value="2017">2017</option>
                <option value="2018">2018</option>
                <option value="2019">2019</option>
                <option value="2020">2020</option>
                <option value="2021">2021</option>
                <option value="2022">2022</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
            </select>


    </div>

{!--
    <div class="form-group">
        <label>Security Code</label>
        <input class="form-control" type="text" name="cc_cvv2" value="" />
    </div>
--}



    <legend>Billing Address</legend>


    <div class="form-group">
        <label for="company">Company</label>
        <input class="form-control" type="text" id="company" name="company" value="" />
    </div>
    <div class="form-group">
        <label for="address">Street Address</label>
        <input class="form-control" type="text" id="address" name="address" maxlength="100" value="" />
    </div>
    <div class="form-group">
        <label for="address_2">Address Line 2</label>
        <input class="form-control" type="text" id="address_2" name="address_2" maxlength="100" value="" />
    </div>
    <div class="form-group">
        <label for="city">Suburb/City</label>
        <input class="form-control" type="text" id="suburb" name="suburb" maxlength="100" value="" />
    </div>
    <div class="form-group">
        <label for="region_other">State/Province</label>
        <input class="form-control" type="text" id="state" name="state" value="" />
    </div>
    <div class="form-group">
        <label for="country">Country</label>
        <select class="form-control" name="country" id="country">
            <?
            foreach ($countrylist as $key => $value) {
                $selected = ($key==$countrycode)?'selected':'';
                echo "<option $selected value='$key' label='$value'>$value</option>";
            }

            ?>

        </select>
    </div>
    <div class="form-group">
        <label for="postal_code">Postal Code/Zip</label>
        <input class="form-control" type="text" id="postal_code" name="postal_code" maxlength="100" value="" />
    </div>

    {!--
    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input class="form-control" type="text" id="phone" name="phone" value="" />
    </div>
    --}


    {!--
    <div class="form-group">
        <label for="screen_name">Screen Name</label>
        <input class="form-control" type="text" id="screen_name" name="screen_name" maxlength="100" value="" />
    </div>
    --}






    {!--
    <legend>Coupon</legend>

    <div class="form-group">
        <label for="coupon">Coupon</label>
        <input class="form-control" type="text" id="coupon" name="coupon" maxlength="100" value="" />
    </div>
    --}

    <div class="checkbox">
        <label>
            <input id="terms" name="terms" type="checkbox"> I have read and agree with the 
            <a href="<?=$https_site_url.$uri_string?>#terms">Terms and Conditions</a>.
        </label>
    </div>

    <div class="form-group">
        <input class="btn btn-success" name="submit_payment" id="submit_payment" type="submit" value="Purchase My Subscription">
    </div>

</form>


<? $this->view('terms'); ?>



<script>
        $().ready(function() {
            //alert("boooo");
        // $('#test').validate( {invalidHandler: $.watermark.showAll} );

            $("#cc_form").validate({
                rules: {
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
                        }
                    },
                messages: {
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

