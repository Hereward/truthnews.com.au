Dear <?=$subscriber->first_name?>,

You have received a gift subscription to Truth News Australia!

<?php if ($subscriber->existing_member == 0) { ?>
Your account details are shown below. A temporary password has been created for you. For security reasons please log in and change your password as soon as possible, by editing your profile.

Subscription Type: <?=$subscriber->type?> 

User Name: <?=$subscriber->username?> 
(This is the same as your email address)

Temp Password:<?=$subscriber->temp_password?> 

Log In: <?=$site_url?>log-in

Edit Profile: <?=$site_url?>members/profile/<?=$subscriber->member_id?>

    
<?php } else { ?>
We found an existing member account (user name: <?=$subscriber->username?> ) attached to your supplied email address, so we have applied the subscription to that account. If this is in error please contact us.

To update your member profile please log in then edit your profile.

Log In: <?=$site_url?>log-in
Edit Profile: <?=$site_url?>members/profile/<?=$subscriber->member_id?>

If you can't remember your password you can recover it here:
<?=$site_url?>member/forgot_password

<?php } ?>

If you wish to cancel your subscription at any time you can do so from your profile, but you'll need to log in first.

<?php if ($subscriber->type == 'yearly' && $subscriber->include_extras == '1') { ?>
Your DVD and t-shirt will be mailed to you at the address below. If you want them sent somewhere else or you would like to pick them up yourself please contact us.

Member ID: <?=$subscriber->member_id?>
T-shirt Size: <?=$subscriber->tshirt_size?>

<?=$subscriber->first_name?> <?=$subscriber->last_name?>

<?php if ($subscriber->company) {echo $subscriber->company;}?>

<?=$subscriber->address?> 
<?php
$trimmed_address = trim($subscriber->address_2);
if (!empty($trimmed_address)) {echo $subscriber->address_2;}
?>

<?=$subscriber->suburb?>

<?=$subscriber->state?> <?=$subscriber->postal_code?>

<?=$countrylist[$subscriber->country]?>

<?php } ?>

Finally, please check out our "Subscribers Only" page which contains a variety of resources which you may find useful:
<?=$site_url?>subscribers_only


Best regards,
Hereward Fenton






