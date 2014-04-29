<?
foreach ($errors as $error) {
    echo "<p>Error: $error</p>\n";
}
?>

<form action="<?= $https_site_url ?>subscribe" method="post">
    <input type="hidden" name="create_existing_member" value="1" />
    <div class="form-group">
        <label>Subscription Type</label>
        <select class="form-control" name="subscription_type" id="subscription_type">
            <option value="yearly">Yearly - AUD $65.00 per year (includes T-shirt)</option>
            <option value="monthly">Monthly - AUD $6.50 per month</option>
        </select>
    </div>

    <div class="alert alert-success"><strong>Note:</strong> Your subscription will be applied to the currently logged in member, <strong><?=$username?></strong>,
        with the following email address: <strong><?=$email?></strong>.
        <br/>

    </div>



    <div class="form-group">
        <input class="btn btn-default" type="submit" value="Proceed to Checkout &raquo;">
    </div>
</form>

