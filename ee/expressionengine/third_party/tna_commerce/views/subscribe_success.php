<? if ($subscriber->existing_member == 0) { ?>
    <p class="bold">Your account details are shown below. A temporary password has been created for you. 
        For security reasons please <a href="<?=$site_url?>log-in">log in</a> and change your password as soon as possible, by editing your <a href="<?=$site_url?>members/profile/<?=$subscriber->member_id?>">profile</a>.</p>
    
    <p><strong>User Name:</strong> <?=$subscriber->username?>
        
    <p><strong>Temp Password:</strong> <?=$subscriber->temp_password?>
        
    <p>Log In: <a href="<?=$site_url?>log-in"><?=$site_url?>log-in</a></p>
    
<? } elseif ($logged_in == true) { ?>

    <p class="bold">We have applied your subscription to the currently logged in user, <?=$subscriber->username?>. 
    If this is in error please <a href="<?=$site_url?>contact">contact</a> us. </p>

    <p>To update your member profile please edit your <a href="<?=$site_url?>members/profile/<?=$subscriber->member_id?>">profile</a>.</p>
    
<? } else { ?>

    <p class="bold">We found an existing member account (user name: <?=$subscriber->username?> ) 
        attached to your supplied email address, so we have applied the subscription to that account. 
        If this is in error please contact us.</p>

    <p>To update your member profile please <a href="<?=$site_url?>log-in">log in</a>, 
        then edit your <a href="<?=$site_url?>members/profile/<?=$subscriber->member_id?>">profile</a>.</p>

    <p>If you can't remember your password you can recover it <a href="<?=$site_url?>member/forgot_password">here</a>.</p>

<? } ?>

<p>If you wish to cancel your subscription at any time you can do so from your <a href="<?=$site_url?>members/profile/<?=$subscriber->member_id?>">profile</a> page, but you&quot;ll need to <a href="<?=$site_url?>log-in">log in</a> first.</p>

<? if ($subscriber->type == 'yearly') { ?>
    <hr/>

    <p> If you ordered the DVD and t-shirt these will be mailed to you at the address below. If you want them sent somewhere else 
or you would like to pick them up yourself please contact us.</p>
    <address>
        <strong><?=$subscriber->first_name?> <?=$subscriber->last_name?></strong> <br>
        <? if ($subscriber->company) {echo $subscriber->company.'<br>';}?>
        <?=$subscriber->address?>
        <? 
           $trimmed_address = trim($subscriber->address_2);
           if (!empty($trimmed_address)) {echo '<br>'.$subscriber->address_2;}
        ?>
        <br>
        <?=$subscriber->suburb?><br>
        <?=$subscriber->state?> <?=$subscriber->postal_code?><br>
        <?=$countrylist[$subscriber->country]?><br>
    </address>

    <hr/>
<? } ?>

<p>Finally, please check out our <a href="<?=$site_url?>subscribers_only">"Subscribers Only"</a> page which contains a variety of 
   resources which you may find useful.</p>

<p>Thanks for supporting truth in media. Your money will not be wasted!</p>

<p>Best regards,</p>
<p class="bold">Hereward Fenton</p>






