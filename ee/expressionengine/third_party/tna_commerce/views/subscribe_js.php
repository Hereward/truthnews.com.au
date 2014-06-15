<script>
    $().ready(function() {

        $("#subscription_type").change(function() {
            $('#tshirt_group').toggle();
            
            type = $("#subscription_type").val();
             
             if (type == 'monthly') {
                 $('#include_extras').attr('checked', false);
                 $('#postage_settings').hide();
             }
        });


        $("#country").change(function() {
            selected = $("#country").val();

            update_country(selected);

        });

        $("#include_extras").change(function() {

            var isChecked = $('#include_extras').is(':checked');

            if (!isChecked) {
                $("#postage_cost_label").text('AUD $0.00');
                $("#postage_cost").val('0');
            } else {
                selected = $("#country").val();
                
                update_country(selected);

            }

            //var isChecked = $('#postage_settings').prop("checked")?true:false;


            $('#postage_settings').toggle();
        });

    });
    
    var isChecked = $('#include_extras').is(':checked');
    if (isChecked) {
        //alert('hello');
        $("#postage_settings").show();
    }
    
    stype = $("#subscription_type").val();
    if (stype == 'monthly') {
        $('#tshirt_group').hide();
    }
    
    
</script>







