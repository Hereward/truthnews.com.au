<? $this->view('errors'); ?>

<form id="donate_form" name="donate_form" action="<?= $https_site_url ?>donate/payment" method="post">

    <div class="form-group">
        <label for="email">Please Enter Your Email Address</label>
        <input class="form-control" type="text" id="email" name="email" maxlength="100" value="" />
    </div>




    <div class="form-group">
        <input class="btn btn-success" type="submit" id="submit_email" name="submit_email" value="Proceed to Payment Page &raquo;">
    </div>
    

</form>

<script>
        $().ready(function() {
            $("#donate_form").validate({
                rules: {
                        email: {
                            required: true,
                            email: true
                        }
                    }
            });
            
        });
    </script>
    
    

    
   
    
  
   
    
 
