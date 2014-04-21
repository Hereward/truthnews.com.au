<form action="<?= $site_url ?>subscribe/payment" method="post">
    <input type="hidden" name="membrr_order_form" value="1" />
    <input type="hidden" name="plan_id" value="1000" />
    <!-- This credit card fieldset is not required for free or external checkout (e.g., PayPal Express Checkout) payment methods. -->

   {!-- <legend>Billing Information</legend> --}

    <div class="form-group">
        <label>Subscription Type</label>

        <select class="form-control" name="subscription_type" id="subscription_type">

            <option value="yearly">Yearly - AUD $65.00 per year (includes T-shirt)</option>
            <option value="monthly">Monthly - AUD $6.50 per month</option>

        </select>

    </div>

    <div class="form-group">
        <label for="first_name">First Name</label>
        <input class="form-control" type="text" id="first_name" name="first_name" maxlength="100" value="" />
    </div>

    <div class="form-group">
        <label for="last_name">Last Name</label>
        <input class="form-control" type="text" id="last_name" name="last_name" maxlength="100" value="" />
    </div>

    <div class="form-group">
        <label for="email">Email Address</label>
        <input class="form-control" type="text" id="email" name="email" maxlength="100" value="" />
    </div>

    <div class="form-group">
        <div><strong>Note: </strong>Your email address will be your username when you sign-in.</div>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input class="form-control" type="password" id="password" name="password" maxlength="100" value="" />
    </div>
    <div class="form-group">
        <label for="password2">Confirm Password</label>
        <input class="form-control" type="password" id="password2" name="password2" maxlength="100" value="" />
    </div>

    <div class="form-group">
        <label for="Introduction">Introduction (optional) - tell us a little about yourself!</label>
        <textarea id="Introduction" name="Introduction" class="form-control" rows="3"></textarea>

    </div>

    <div class="form-group">

        <input class="btn btn-default" type="submit" value="Proceed to Checkout &raquo;">
    </div>


</form>

