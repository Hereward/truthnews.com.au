<? // $this->view('errors'); ?>
<p>We're sorry to see you go! Please leave any comments or feedback below.</p>

<form id="cancel_subscription" name="cancel_subscription" action="<?= $https_site_url ?>
    members/cancel_subscription" method="post">
    <input type="hidden" name="confirm_cancellation_<?=$member_id?>" value="1" />
    <input type="hidden" name="member_id" value="<?= $member_id ?>" />

    <!-- This credit card fieldset is not required for free or external checkout (e.g., PayPal Express Checkout) payment methods. -->

    
    <div class="form-group">
        <label for="address">Comments/Feedback</label>
        <input class="form-control" type="text" id="comments" name="comments" maxlength="100" value="<?= $comments ?>" />
    </div>
    
   

    <div class="form-group">
        <input class="btn btn-success" type="submit" name="submit_cancellation" value="Cancel My Subscription">
        <p id="please_wait" style="display:none; color:red;" class="help-block bold">Please wait while we process your request...</p>

    </div>
    
</form>

<script>
        $().ready(function() {
            $("#cancel_subscription").validate({
                submitHandler: function(form) {
                //$("#submit_payment").attr('disabled', 'disabled');
                 $(form).find(":submit").attr("disabled", 'disabled').attr("value", "Submitting..."); 
                 $("#please_wait").show();
                form.submit();
                },
                rules: {},
                messages: {}
            });
        });
</script>
    

