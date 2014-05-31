<? $this->view('errors'); ?>

<form id="subscribe_form" name="subscribe_form" action="<?= $https_site_url ?>subscribe/payment" method="post">
    <input type="hidden" name="create_member" value="1" />

    <!-- This credit card fieldset is not required for free or external checkout (e.g., PayPal Express Checkout) payment methods. -->

  

    <div class="form-group">
        <label>Subscription Type</label>

        <select class="form-control" name="subscription_type" id="subscription_type">

            <option value="yearly">Yearly - AUD $<?=$yearly_amount?> per year (includes T-shirt)</option>
            <option value="monthly">Monthly - AUD $<?=$monthly_amount?> per month</option>

        </select>

    </div>
    
    <div id="tshirt_group" class="form-group">
        <label>T-Shirt Size</label>
        {!-- <p class="help-block">With this subscription you are entitled to a free TNRA t-shirt!</p> --}

        <select class="form-control" name="tshirt_size" id="tshirt_size">
            <option value="XS">Extra Small</option>
            <option value="S">Small</option>
            <option value="M">Medium</option>
            <option selected value="L">Large</option>
            <option value="XL">Extra Large</option> 
        </select>

    </div>

    <div class="form-group">
        <label for="email">Email Address</label>
        <input class="form-control" type="text" id="email" name="email" maxlength="100" value="" />
    </div>
{!--
    <div class="form-group">
        <label for="screen_name">Screen Name</label>
        <input class="form-control" type="text" id="screen_name" name="screen_name" maxlength="100" value="<?=$screen_name?>" />
    </div>
--}


{!--
    <div class="form-group">
        <label for="Introduction">Introduction (optional) - tell us a little about yourself!</label>
        <textarea id="Introduction" name="Introduction" class="form-control" rows="3"></textarea>

    </div>
--}

    <div class="form-group">

        <input class="btn btn-success" type="submit" value="Proceed to Checkout &raquo;">
    </div>
    



</form>

<script>
        $().ready(function() {
            $("#subscribe_form").validate({
                rules: {
                        email: {
                            required: true,
                            email: true
                        }
                    },
                messages: {
                    screen_name: {
                            required: "Please enter a screen name",
                            minlength: "Your screen name must consist of at least 2 characters"
                        },
                        email: "Please enter a valid email address"
                       
                    }
            });
        });
    </script>
    
   
    <script>
        $( "#subscription_type" ).change(function() {
            $('#tshirt_group').toggle()
        });
    </script>
    
 
