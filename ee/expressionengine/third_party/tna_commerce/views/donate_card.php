<? $this->view('errors'); ?>



<form id="cc_form" name="cc_form" method="post">
    
    <input type="hidden" name="email" value="<?= $email ?>" />
    
    <div class="form-group">
    <label>Donation Amount (AUD)</label>
    <select class="form-control" name="aud_price" id="subscription_type">
        <option <?=($aud_price == 500)?'selected':''?> value="500">$5.00</option>
        <option <?=($aud_price == 1000)?'selected':''?> value="1000">$10.00</option>
        <option <?=($aud_price == 2000)?'selected':''?> value="2000">$20.00</option>
        <option <?=($aud_price == 5000)?'selected':''?> value="5000">$50.00</option>
        <option <?=($aud_price == 10000)?'selected':''?> value="10000">$100.00</option>
        <option <?=($aud_price == 20000)?'selected':''?> value="20000">$200.00</option>
        <option <?=($aud_price == 50000)?'selected':''?> value="50000">$500.00</option>
        <option <?=($aud_price == 100000)?'selected':''?> value="100000">$1000.00</option>
    </select>
</div>



    <div class="form-group">
        <label for="CardHoldersName">Full Name (as it appears on card):</label>
        <input class="form-control" type="text" id="CardHoldersName" name="CardHoldersName" maxlength="100" value="<?= $CardHoldersName ?>" />
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
    




  
    <div class="form-group">
        <input class="btn btn-success" name="submit_payment_button" id="submit_payment_button" type="submit" value="Pay Now">
        <p id="please_wait" style="display:none; color:red;" class="help-block bold">Please wait while we process your payment...</p>
    </div>
    
    <input type="hidden" name="submit_payment" value="1" />

</form>




<script>
    

    $().ready(function() {
        

        $("#cc_form").validate({
            
            submitHandler: function(form) {
                //$("#submit_payment").attr('disabled', 'disabled');
                 $(form).find(":submit").attr("disabled", 'disabled').attr("value", "Submitting..."); 
                 $("#please_wait").show();
                form.submit();
            },
            
            rules: {
                CardHoldersName: {
                    required: true,
                    minlength: 2,
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
                CardHoldersName: {
                    required: "Please enter your full name as it appears on card",
                    minlength: "Your name must consist of at least 2 characters"
                },
               
                cc_number: {
                    required: "Please enter a credit card number",
                    creditcard: "Please enter a valid credit card number"
                }
            }
        });
    });


</script>




