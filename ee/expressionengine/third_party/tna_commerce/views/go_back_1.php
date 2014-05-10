
<div class="well">
<table cellpadding="5"> 
    
    <tr><td class=""><strong>Subscription Type:</strong></td><td><?=$subscription_type?></td></tr>
    <tr><td><strong>Email:</strong></td><td><?=$email?></td></tr>
  
    <tr><td><strong>T-shirt size:</strong></td><td><?=$tshirt_size?></td></tr>
    <tr><td><strong>Initial Charge:</strong></td><td>AUD $<?=$subscription_details->aud_price?></td></tr>
    <tr><td colspan="2"><?=$subscription_details->description?></td></tr>
    <tr><td><button type="button" id="go_back" class="btn btn-warning btn-sm"> &laquo; Go Back / Change Details</button> </td></tr>

</table>
</div>
