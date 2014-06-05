Dear <?=$subscriber->first_name?>,

Your subscription to Truth News Australia is now active.

<? if ($subscriber->existing_member == 0) { ?>
Your account details are shown below. A temporary password has been created for you. For security reasons please log in and change your password as soon as possible, by editing your profile.

User Name: <?=$subscriber->username?> 
(This is the same as your email address)

Temp Password:<?=$subscriber->temp_password?> 

Log In: <?=$site_url?>log-in

Edit Profile: <?=$site_url?>members/profile/<?=$subscriber->member_id?>

<? } elseif ($logged_in == true) { ?>
We have applied your subscription to the currently logged in user, <?=$subscriber->username?>. If this is in error please contact us. 

Contact Us: <?=$site_url?>contact

To update your member profile please edit your profile.

Edit Profile: <?=$site_url?>members/profile/<?=$subscriber->member_id?>
    
<? } else { ?>
We found an existing member account (user name: <?=$subscriber->username?> ) attached to your supplied email address, so we have applied the subscription to that account. If this is in error please contact us.

To update your member profile please log in then edit your profile.

Log In: <?=$site_url?>log-in
Edit Profile: <?=$site_url?>members/profile/<?=$subscriber->member_id?>

If you can't remember your password you can recover it here:
<?=$site_url?>member/forgot_password

<? } ?>

If you wish to cancel your subscription at any time you can do so from your profile, but you'll need to log in first.

<? if ($subscriber->type == 'yearly') { ?>
We will be sending your t-shirt to the billing address below. If you want the t-shirt sent somewhere else please contact us.

<?=$subscriber->first_name?> <?=$subscriber->last_name?>

<? if ($subscriber->company) {echo $subscriber->company;}?>

<?=$subscriber->address?> 
<? 
$trimmed_address = trim($subscriber->address_2);
if (!empty($trimmed_address)) {echo $subscriber->address_2;}
?>

<?=$subscriber->suburb?>

<?=$subscriber->state?> <?=$subscriber->postal_code?>

<?=$countrylist[$subscriber->country]?>
<? } ?>

Finally, please check out our "Subscribers Only" page which contains a variety of resources which you may find useful:
<?=$site_url?>subscribers_only

Thanks for supporting truth in media. Your money will not be wasted!

Best regards,
Hereward Fenton






