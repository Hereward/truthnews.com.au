<? $this->view('errors'); ?>

<form action="<?= $https_site_url ?>subscribe/payment" method="post">
    <input type="hidden" name="create_existing_member" value="1" />
    <input type="hidden" name="member_id" value="<?=$member_id?>" />
    <input type="hidden" name="email" value="<?=$email?>" />
    
    <div class="form-group">
        <label>Subscription Type</label>
        <select class="form-control" name="subscription_type" id="subscription_type">
            <option value="yearly">Yearly - AUD $<?=$yearly_amount?> per year</option>
            <option value="monthly">Monthly - AUD $<?=$monthly_amount?> per month</option>

        </select>
    </div>
    
    <? $this->view('t_shirt_choices'); ?>
    
    {!--
    
     <div id="tshirt_group" class="form-group">
        <label>T-Shirt Size</label>
         <p class="help-block">With this subscription you are entitled to a free TNRA t-shirt!</p> 

        <select class="form-control" name="tshirt_size" id="tshirt_size">
            <option value="XS">Extra Small</option>
            <option value="S">Small</option>
            <option value="M">Medium</option>
            <option selected value="L">Large</option>
            <option value="XL">Extra Large</option> 
        </select>

    </div>
    --}

    <div class="alert alert-success"><strong>Note:</strong> You are currently logged in as <strong><?=$username?></strong>,
        with the following email address: <strong><?=$email?></strong>. Your subscription will be applied to this account. 
        If you would like to attach your subscription to an account with a different email address please <strong><a href="<?=$https_site_url?>?ACT=10&return=%2Fsubscribe">log out</a></strong> first.
        <br/>

    </div>



    <div class="form-group">
        <input class="btn btn-default" type="submit" value="Proceed to Checkout &raquo;">
    </div>
</form>


 <? $this->view('subscribe_js'); ?>

