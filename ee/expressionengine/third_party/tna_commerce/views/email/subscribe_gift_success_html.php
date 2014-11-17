Dear <?=$subscriber->first_name?>,

<p>You have received a gift subscription to <a href="http://www.truthnews.com.au/">Truth News Australia</a>! You'll soon be receiving our t-shirt and DVD in the mail, and your subscriber account gives you access to all our podcasts for one year.</p>

<?php if (!$subscriber_gift_details->secret_gift)  { ?>
    <p>
        This gift was sent to you by <strong><a href="mailto:<?=$subscriber_gift_details->email?>"><?=$subscriber_gift_details->first_name?> <?=$subscriber_gift_details->last_name?></a></strong>.
    </p>
<?php } else { ?>
    <p>The giver of this gift wishes to remain anonymous.</p>
<?php } ?>

<? $this->view('subscribe_success'); ?>





