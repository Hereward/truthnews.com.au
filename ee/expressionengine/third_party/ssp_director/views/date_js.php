<script type='text/javascript'>
        $(document).ready(function(){
          
        $(".CG_input_date").datePicker({startDate:'01/01/1996'});

 

        $(".CG_input_date").each(
        function(i){
            if(!$(this).attr("id")){
                $(this).attr("id","CG_input_date_"+i);
            }
            //$(this).after('<input type="hidden" name="'+$(this).attr("name")+'_hidden" id="'+$(this).attr("id")+'_hidden" value=""/>');

            $(this).blur(
                function(){
                    var fieldDate = $(this).val(),
                        fieldParams = fieldDate.split("/"),
                        yy = fieldParams[2],
                        mm = parseInt(fieldParams[1],10)-1,
                        dd = parseInt(fieldParams[0],10),
                        GMTDate = new Date();
                    if(fieldDate !== ""){
                        GMTDate.setYear(yy);
                        GMTDate.setMonth(mm);
                        GMTDate.setDate(dd);
                        if(i===0){
                        	GMTDate.setHours(0);
                            GMTDate.setMinutes(0);
                            GMTDate.setSeconds(0);
                        }else{
                        	GMTDate.setHours(23);
                        	GMTDate.setMinutes(59);
                        	GMTDate.setSeconds(59);

                        }
                        $("#"+$(this).attr("id")+"_hidden").val(GMTDate.toUTCString());
                    }
                }
            );
        }
      );
     });


</script>