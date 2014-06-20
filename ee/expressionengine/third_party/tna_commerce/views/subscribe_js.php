<script>

    $().ready(function() {

        $("#subscription_type").change(function() {
            //$('#tshirt_group').toggle();
            type = $("#subscription_type").val();
             if (type != 'yearly') {
                 $('#include_extras').attr('checked', false);
                 $('#postage_settings').hide();
             }
             
             set_tshirt_vis();
             set_concession_info();
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
                $('#postage_settings').hide();
            } else {
                selected = $("#country").val();
                
                update_country(selected);
                 $('#postage_settings').show();

            }

            //var isChecked = $('#postage_settings').prop("checked")?true:false;
            //set_tshirt_vis();
        });

    });
    
        function set_concession_info() {
            type = $("#subscription_type").val();
            if (type == 'yearly_concession') {
                $('#concession_info').show();
            } else {
                $('#concession_info').hide();
            }
        }

        function set_tshirt_vis() {
            stype = $("#subscription_type").val();
            if (stype != 'yearly') {
                $('#tshirt_group').hide();
            } else {
                $('#tshirt_group').show();
                //$('#postage_settings').show();
            }
            
        }
    
    
    var isChecked = $('#include_extras').is(':checked');
    if (isChecked) {
        //alert('hello');
        $("#postage_settings").show();
    }
    
    set_tshirt_vis();
    set_concession_info();
    
</script>









