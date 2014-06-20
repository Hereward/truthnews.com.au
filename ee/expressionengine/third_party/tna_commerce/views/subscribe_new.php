<? $this->view('errors'); ?>

<form id="subscribe_form" name="subscribe_form" action="<?= $https_site_url ?>subscribe/payment" method="post">
    <input type="hidden" name="create_member" value="1" />

    <!-- This credit card fieldset is not required for free or external checkout (e.g., PayPal Express Checkout) payment methods. -->

  

    <? $this->view('subscription_type'); ?>
    
    <? $this->view('t_shirt_choices'); ?>
    


    <div class="form-group">
        <label for="email">Email Address</label>
        <input class="form-control" type="text" id="email" name="email" maxlength="100" value="" />
    </div>




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
                    }
            });
            
        });
    </script>
    
     <? $this->view('subscribe_js'); ?>
     
     <? $this->view('shared_js'); ?>
     
     <script>
       selected = $("#country").val();
       update_country(selected);
     </script>

    
   
    
  
   
    
 
