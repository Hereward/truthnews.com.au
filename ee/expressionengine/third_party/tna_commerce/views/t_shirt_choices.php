{!--
<legend>T-Shirt & DVD</legend>
--}

<input type="hidden" id="postage_cost" name="postage_cost" value="7.20" />


<div id="tshirt_group" class="form-group">
<hr>
<p class="bold">As a yearly subscriber you are entitled to receive our t-shirt and DVD. 
 We will only charge you for the postage fees as determined by Australia Post.
</p>

<div class="checkbox">
    
  <label>
    <input name="include_extras" id ="include_extras" type="checkbox" value="1">
      Yes, please send me the t-shirt &amp; DVD !
  </label>
</div>

    <div id='postage_settings'>
             <p>Postage &amp; Handling: <span class='postage_cost_label' id='postage_cost_label'>AUD $7.20</span></p>
            <div class="form-group">
                    <label for="country">Destination Country for Postage</label>
                    <select class="form-control" name="country" id="country">
                        <?
                        $selected_country = ($countrycode)?$countrycode:'';
                        $selected_country = ($country)?$country:$selected_country;

                        foreach ($countrylist as $key => $value) {
                        $selected = ($key==$selected_country)?'selected':'';
                        echo "<option $selected value='$key' label='$value'>$value</option>";
                        }

                        ?>

                    </select>
            </div>

           

                    <label>T-Shirt Size</label>

                    <select class="form-control" name="tshirt_size" id="tshirt_size">
                        <option value="XS">Extra Small</option>
                        <option value="S">Small</option>
                        <option value="M">Medium</option>
                        <option selected value="L">Large</option>
                        <option value="XL">Extra Large</option> 
                    </select>  
    </div>
        
        
<hr>
</div>





    
    
    
 
    




   
 
