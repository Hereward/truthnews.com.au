<script>
    function update_country(selected) {
            if (selected == 'AU') {
                $("#postage_cost_label").text('<?= $standard_domestic ?>');
                $("#postage_cost").val('<?= $standard_domestic ?>');
            } else if (selected == 'US') {
                $("#postage_cost_label").text('AUD $<?= $standard_us ?>');
                $("#postage_cost").val('<?= $standard_us ?>');
            } else {
                $("#postage_cost_label").text('<?= $standard_international ?>');
                $("#postage_cost").val('<?= $standard_international ?>');
            }
    }
    
</script>







