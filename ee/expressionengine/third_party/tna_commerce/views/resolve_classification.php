<div style="margin:10px 0px 20px 0px; padding:5px; border:1px solid green;">

    <table cellpadding="2">

        <tr>
            <td align="right"><h4>Resolved Classifcation: </h4></td>
            <td>
                <?
                if ($c_id) {
                    echo "$c_id | $c_label";
                } else {
                    echo "<span style='color:red'>FAILED!</span>";
                }
                ?>
            </td>
        </tr>
    </table>

</div>

<div style="margin:10px 0px 20px 0px; padding:5px; border:1px solid green;">
    <table cellpadding="2">

        <tr>
            <td align="right"><strong>Resolved URL:</strong></td>
            <td>
                <?
                if ($url) {
                    echo "<a href=\"$url\">$url</a>";
                } else {
                    echo "<span style='color:red'>FAILED!</span>";
                }
                ?>
            </td>
        </tr>

        <tr>
            <td align="right"><strong>Supplied keyword(s):</strong></td>
            <td>
                <?
                if ($keyword) {
                    echo "$keyword";
                } else {
                    echo "<span style='color:red'>EMPTY!</span>";
                }
                ?>
            </td>
        </tr>

        <tr>
            <td align="right"><strong>Status:</strong></td>
            <td>
                <?
                if ($resolved_state) {
                    echo "$resolved_state";
                } else {
                    echo "<span style='color:red'>EMPTY!</span>";
                }
                ?>
            </td>
        </tr>
    </table>
</div>

<div style="margin:20px 0;">
    <h4>Related Classifications</h4>

    <?
    if ($related_classifications) {
        echo $related_classifications;
    } else {
        echo "<div style='color:red'>NONE FOUND!</div>";
    }
    ?>
</div>

<? if ($t_count>1) { ?>

    <div style='margin-bottom:50px; margin-left:20px; float:right; width:450px; overflow:auto;'>

        <div style="margin-top:0px;">
            <h4 style="color:green;">Secondary results for truncated search string</h4>

        </div>

        <div style="margin:10px 0px 20px 0px; padding:5px; border:1px solid red;">
            <table cellpadding="2">
                <tr>
                    <td align="right"><strong>Keyword (truncated): </strong></td><td><?=$short_key?></td>
                </tr>
                <tr>
                    <td align="right"><strong>Area: </strong></td><td></td>
                </tr>

            </table>
        </div>


        <div style="margin-top:30px;">
            <h4>Primary Keyword &gt; Classification Match (exact)</h4>
            <?=$r_match_short?>
        </div>

        <div style="margin-top:30px;">
            <h4>Classification Matches (partial)</h4>
            <?=$c_match_short?>
        </div>


        <div style="margin-top:30px;">
            <h4>Keyword Matches (partial)</h4>
            <?=$k_match_short?>
        </div>

    </div>

<? } ?>

<div style="width:450px; overflow:auto;">
    <h4 style="color:green;">Resolution Data</h4>
    <div style="margin:10px 0px 20px 0px; padding:5px; border:1px solid red;">
        <table cellpadding="2">
            <tr>
                <td align="right"><strong>Keyword: </strong></td><td><?=$keyword?></td>
            </tr>
            <tr>
                <td align="right"><strong>Area: </strong></td><td></td>
            </tr>
        </table>
    </div>

    <div style="margin-top:30px;">
        <h4>Primary Keyword &gt; Classification Match (exact)</h4>
        <?=$r_match?>
    </div>

    <div style="margin-top:30px;">
        <h4>Classification Matches (partial)</h4>
        <?=$c_match?>
    </div>


    <div style="margin-top:30px;">
        <h4>Keyword Matches (partial)</h4>
        <?=$k_match?>
    </div>

</div>







