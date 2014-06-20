<div class="form-group">
    <label>Subscription Type</label>
    <select class="form-control" name="subscription_type" id="subscription_type">
        <option value="yearly">Yearly - AUD $<?= $yearly_amount ?> per year</option>
        <option value="yearly_concession">Concession - AUD $<?= $yearly_concession_amount ?> per year</option>
        <option value="monthly">Monthly - AUD $<?= $monthly_amount ?> per month</option>
    </select>
</div>

<p style="display:none; color:red;" id="concession_info" class="bold">This is a concessional subscription price for students and pensioners.</p>