<? $this->view('errors'); ?>


<form action="<?= $https_site_url ?>subscribe/payment" method="post">
    <input type="hidden" name="create_existing_member" value="1" />
    <input type="hidden" name="member_id" value="<?=$member_id?>" />
    <input type="hidden" name="email" value="<?=$email?>" />
    
    <? $this->view('subscription_type'); ?>
    

    <div class="alert alert-success"><strong>Note:</strong> You are currently logged in as <strong><?=$username?></strong>,
        with the following email address: <strong><?=$email?></strong>. Your subscription will be applied to this account. 
        If you would like to attach your subscription to an account with a different email address please <strong><a href="<?=$https_site_url?>?ACT=10&return=%2Fsubscribe">log out</a></strong> first.
        <br/>

    </div>
    
     <? $this->view('t_shirt_choices'); ?>



    <div class="form-group">
        <input class="btn btn-default" type="submit" value="Proceed to Checkout &raquo;">
        <input type="hidden" name="XID" value="<?= $xid_hash ?>">
    </div>
</form>


 <? $this->view('subscribe_js'); ?>
 
 <? $this->view('shared_js'); ?>
 
 <script>
   selected = $("#country").val();
   update_country(selected);
 </script>

