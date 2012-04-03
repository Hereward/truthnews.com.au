<div id="search_widget">
    <table cellspacing="0" cellpadding="0" border="0" style="width: 100%;" class="tableBorder">
        <script type="text/javascript">
        $(document).ready(function(){

        $(".CG_input_date").datePicker({startDate:'01/01/1996'});

        $(".CG_input_date").each(
        function(i){
            if(!$(this).attr("id")){
                $(this).attr("id","CG_input_date_"+i);
            }
            //$(this).after('&lt;input type="hidden" name="'+$(this).attr("name")+'_hidden" id="'+$(this).attr("id")+'_hidden" value=""/&gt;');

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


</script><tbody><tr>
<td valign="top" style="width: 220px;" class="tableHeading">Search for your favourite album!</td>
</tr>
<tr>
<td valign="top" style="width: 220px;" class="tableCellTwo">
<form id="ssp_search" name="ssp_search" method="post" action="index.php?S=0&C=modules&M=ssp_director">
<div>
<input type="hidden" value="3" name="member_id">
<input type="hidden" value="" name="CG_input_date_1_hidden" id="CG_input_date_1_hidden">
<input type="hidden" value="" name="CG_input_date_2_hidden" id="CG_input_date_2_hidden">
<table border="0">

	<tbody><tr>
		<td valign="top" align="right"><b>start</b></td>
		<td valign="top" align="left">	<input type="text" name="CG_input_date_1" class="CG_input_date dp-applied" id="CG_input_date_1"><a title="Choose date" class="dp-choose-date" href="#">Choose date</a></td>
	</tr>
	<tr>
		<td valign="top" align="right"><b>end</b></td>
		<td valign="top" align="left">	<input type="text" name="CG_input_date_2" class="CG_input_date dp-applied" id="CG_input_date_2"><a title="Choose date" class="dp-choose-date" href="#">Choose date</a></td>
	</tr>
	<tr>
		<td valign="top" align="right"><b>status</b></td>
		<td valign="top" align="left">	<select name="status">
	<option value="open">open</option>
	<option value="closed">closed</option>
	<option value="draft">draft</option>
	<option value="all">all</option>
</select></td>
	</tr>
	<tr>
		<td valign="top" align="right"><b>type</b></td>
		<td valign="top" align="left">	<select name="query_type">
	<option value="comments">Comments</option>
	<option value="articles">Articles</option>
</select></td>
	</tr>
	<tr>
		<td valign="top" align="right"><b>weblogs</b></td>
		<td valign="top" align="left">	<select name="weblogs[]" size="5" multiple="multiple">
	<option value="0">_ALL</option>
	<option value="40">2008 launches intros</option>
	<option value="79">Advertorial</option>
	<option value="14">AIMS</option>
	<option value="51">Aims 2008</option>
	<option value="52">AIMS 2008 Calendar</option>
	<option value="53">AIMS 2008 Events</option>
	<option value="7">authors</option>
	<option value="12">be involved</option>
	<option value="21">big wheels</option>
	<option value="66">Buyers Guide</option>
	<option value="91">Car of the Year 2010</option>
	<option value="67">Clive Mathieson</option>
	<option value="54">coty</option>
	<option value="93">COTY-2010-management</option>
	<option value="50">Craig Duff</option>
	<option value="81">Craig Lowndes</option>
	<option value="85">Dealer Specials</option>
	<option value="33">Driven troppo</option>
	<option value="63">Editorial Dropzone</option>
	<option value="89">Features</option>
	<option value="24">Footer</option>
	<option value="30">Fun Stuff, Offers and Competitions</option>
	<option value="61">Garagisti</option>
	<option value="87">Geo targeted promos</option>
	<option value="47">Gordon Lomas</option>
	<option value="15">Hints &amp; tips</option>
	<option value="59">homepage</option>
	<option value="20">in depth features</option>
	<option value="25">In Depth Intros</option>
	<option value="73">Jamie Whincup</option>
	<option value="76">Keith Didham</option>
	<option value="45">Kevin Hepworth</option>
	<option value="69">Landing Page Highlights   </option>
	<option value="71">Landing Pages</option>
	<option value="23">Legacy Gallery</option>
	<option value="32">Lo-tech autoclub</option>
	<option value="57">Long Term Test Drives</option>
	<option value="78">MLP Global Settings</option>
	<option value="77">MLP Main</option>
	<option value="65">Model Watch</option>
	<option value="10">motoring news</option>
	<option value="36">motorshows</option>
	<option value="38">motorshows intros</option>
	<option value="19">motorsports</option>
	<option value="44">Neil Dowling </option>
	<option value="35">newsletter</option>
	<option value="86">Newsletter v2</option>
	<option value="17">on-going costs</option>
	<option value="46">Paul Gover</option>
	<option value="43">Paul Pottinger</option>
	<option value="26">Polls</option>
	<option value="75">Question And Answer</option>
	<option value="11">research &amp; reviews</option>
	<option value="28">Road noise</option>
	<option value="18">road tests &amp; reviews</option>
	<option value="55">Rod Halligan</option>
	<option value="37">site promos</option>
	<option value="6">Sources</option>
	<option value="62">Spy Shots</option>
	<option value="83">Stars Cars</option>
	<option value="31">Static</option>
	<option value="42">Stephen Corby</option>
	<option value="49">Stephen Ottley</option>
	<option value="29">Story Suggestions</option>
	<option value="48">Stuart Martin</option>
	<option value="39">test_weblog</option>
	<option value="27">Tony's blog</option>
	<option value="16">Unclassified</option>
	<option value="13">videos</option>
	<option value="34">Your Photos</option>
</select></td>
	</tr>
	<tr>
		<td valign="top" align="right"><b></b></td>
		<td valign="top" align="left">	<input type="submit" value=" GO! " name="submit_mf_query"></td>
	</tr>
</tbody></table>
</div>
</form></td>
</tr>
</tbody></table>
</div>