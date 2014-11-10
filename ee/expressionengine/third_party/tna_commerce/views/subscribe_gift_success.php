
<?php if ($gateway_mode != 'live') {echo "<div class='lead'>GATEWAY MODE = [$gateway_mode]</div>";}?>

<p class="bold">Your gift subscription is complete.</p>

<?php if ($subscriber->type == 'yearly' && $subscriber->include_extras == '1') { ?>
   

    <p> The DVD and t-shirt will be mailed to your nominated recipient shown below. </p>
    <address>
        <strong>Member ID:</strong> <?=$subscriber->member_id?><br>
        <strong>T-shirt Size:</strong> <?=$subscriber->tshirt_size?><br><br>
        <strong><?=$subscriber->first_name?> <?=$subscriber->last_name?></strong> <br>
        <?php if ($subscriber->company) {echo $subscriber->company.'<br>';}?>
        <?=$subscriber->address?>
        <?php 
           $trimmed_address = trim($subscriber->address_2);
           if (!empty($trimmed_address)) {echo '<br>'.$subscriber->address_2;}
        ?>
        <br>
        <?=$subscriber->suburb?><br>
        <?=$subscriber->state?> <?=$subscriber->postal_code?><br>
        <?=$countrylist[$subscriber->country]?><br>
    </address>

<?php } ?>

<p>Thanks for supporting truth in media. Your money will not be wasted!</p>

<p>Best regards,</p>
<p class="bold">Hereward Fenton</p>






