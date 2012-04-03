<?php

error_reporting(0);

//DATABASE SETTINGS
$config['host'] = $db_config->row('host');
$config['user'] = $db_config->row('user');
$config['pass'] = $db_config->row('password');
$config['database'] = $db_config->row('database');
$config['table'] = 'ssp_albums';
$config['nicefields'] = true; //true or false | "Field Name" or "field_name"
$config['perpage'] = 50;
$config['showpagenumbers'] = true; //true or false
$config['showprevnext'] = true; //true or false
$sql_where = '';
$cookie_val = '';
$ignore_cookie = false;
$edit_path = $db_config->row('ssp_url')."/index.php?/albums/edit";

if (isset ($_POST['submit_album_search'])) {
	$Pagination->clear_cookie();
	$ignore_cookie = true;
}

$sql_where = escape_it('general_search', 'name', $sql_where, $Pagination, $ignore_cookie);
$sql_where = escape_it('general_search', 'id', $sql_where, $Pagination, $ignore_cookie);
$sql_where = escape_it('general_search', 'created', $sql_where, $Pagination, $ignore_cookie);
$sql_where = escape_it('general_search', 'description', $sql_where, $Pagination, $ignore_cookie);
$sql_where = escape_it('general_search', 'tags', $sql_where, $Pagination, $ignore_cookie);

$current_cookie = $Pagination->get_cookie();
$query_string = $Pagination->queryString();

/******************************************/
//SHOULDN'T HAVE TO TOUCH ANYTHING BELOW...
//except maybe the html echos for pagination and arrow image file near end of file.
$pagination_links = '';
$prev_link = '';
$next_link = '';

//CONNECT
mysql_connect($config['host'], $config['user'], $config['pass']);
mysql_select_db($config['database']);

$limit = $config['perpage'];
if (isset ($_GET['page']) && is_numeric(trim($_GET['page']))) {
	$page = mysql_real_escape_string($_GET['page']);
} else {
	$page = 1;
}
$startrow = $Pagination->getStartRow($page, $limit);

//IF ORDERBY NOT SET, SET DEFAULT
if (!isset ($_GET['orderby']) OR trim($_GET['orderby']) == "") {
	//GET FIRST FIELD IN TABLE TO BE DEFAULT SORT
	$sql = "SELECT * FROM `" . $config['table'] . "` LIMIT 1";
	$result = mysql_query($sql) or die("line 56 | $sql | ".mysql_error());
	$array = mysql_fetch_assoc($result);
	//first field
	$i = 0;
	foreach ($array as $key => $value) {
		if ($i > 0) {
			break;
		} else {
			$orderby = $key;
		}
		$i++;
	}
	//default sort
	$sort = "ASC";
} else {
	$orderby = mysql_real_escape_string($_GET['orderby']);
}

//IF SORT NOT SET OR VALID, SET DEFAULT
if (!isset ($_GET['sort']) OR ($_GET['sort'] != "ASC" AND $_GET['sort'] != "DESC")) {
	//default sort
	$sort = "DESC";
} else {
	$sort = mysql_real_escape_string($_GET['sort']);
}

//GET DATA
$sql_base = "SELECT id,name,description,path,images_count,tags,active,created,modified FROM `" . $config['table'] . "` $sql_where";
$sql = $sql_base . " ORDER BY $orderby $sort LIMIT $startrow,$limit";

//die("sql = [$sql]\n <br/> sql_base = [$sql_base]\n <br/>".dump($_POST));

$full_result = mysql_query($sql_base) or die(mysql_error());

$result = mysql_query($sql) or die(mysql_error());

$num_rows = mysql_num_rows($full_result);

//$thumb_url = $album->contents[0][1]->thumb->url;
//START TABLE AND TABLE HEADER
if ($num_rows) {
	echo "<div style='margin:10px 0px 10px 0px; font-weight:bold;'> TOTAL ROWS RETURNED: $num_rows</div>";
	echo "<table border='1' cellpadding='5' class='tv'>\n<tr>";
	$array = mysql_fetch_assoc($result);
	foreach ($array as $key => $value) {
		if ($config['nicefields']) {
			$field = str_replace("_", " ", $key);
			$field = ucwords($field);
		}

		if ($field == "Path") {
			$field = "Actions";
        }

		$field = columnSortArrows($key, $field, $orderby, $sort, $module_view_url, $query_string);
		echo "<th class='tv'>" . $field . "</th>\n";
	}
	echo "<th class='tv'>" . 'Play' . "</th>\n";

	echo "</tr>\n";

	//reset result pointer
	mysql_data_seek($result, 0);

	//start first row style
	$tr_class = "class='odd'";

	//LOOP TABLE ROWS
	while ($row = mysql_fetch_assoc($result)) {
        $target_id = get_target_id($row['id'],$db,$main_db_name);
        
		echo "<tr " . $tr_class . ">\n";
		foreach ($row as $field => $value) {
			echo "<td class='tv'>";
			//echo ($field == 'path') ? "<a target='_blank' href='$edit_path/{$row['id']}'>Edit</a>" : $value;
			if ($field == 'path') {
				echo "<a target='_blank' href='$edit_path/{$row['id']}'>Edit</a>";
			} elseif ($field == 'tags') {
				echo "<div style='width:100px; overflow:hidden;'>$value</div>";
			} else {
				echo $value;
			}
			echo "</td>\n";
		}


		//$album = $director->album->get($row['id']);
		//$contents = $album->contents[0];
		//$image = $contents[0];
		$thumb_url = get_thumb($director, $row['id']);
		$gallery_title = $row['name'];
		$gallery_title = str_replace(" ", "-", $gallery_title);  // convert spaces to hyphens
    	$gallery_title = str_replace("&", "and", $gallery_title);
    	$gallery_title = str_replace("'", "", $gallery_title);  // remove single quotes
    	$gallery_title = str_replace('"', '', $gallery_title);  // remove double quotes
    	$gallery_title = strtolower($gallery_title);
		echo "<td class='tv'>";
		echo "<a target='_blank' href='{$root_url}pics/$gallery_title/{$row['id']}/0/$target_id'><img border='0' width='104' height='71' src='$thumb_url' alt='image' /></a>";
		echo "</td>\n";

		echo "</tr>\n";

		//switch row style
		if ($tr_class == "class='odd'") {
			$tr_class = "class='even'";
		} else {
			$tr_class = "class='odd'";
		}

	}

	//END TABLE
	echo "</table>\n";

	//create page links
	//$q1 = mysql_query($sql_01);
	//$nr1 = mysql_num_rows($q1);
	if (!$num_rows) {

		$config['showpagenumbers'] = false;
		$config['showprevnext'] == false;
	}

	if ($config['showprevnext'] == true) {
		$prev_link = $Pagination->showPrev($num_rows, $page, $limit);
		$next_link = $Pagination->showNext($num_rows, $page, $limit);
	} else {
		$prev_link = null;
		$next_link = null;
	}

	if ($config['showpagenumbers'] == true) {
		$pagination_links = $Pagination->showPageNumbers($num_rows, $page, $limit);
	} else {
		$pagination_links = null;
	}

	if (!($prev_link == null && $next_link == null && $pagination_links == null)) {

		echo '<div class="pagination">' . "\n";
		echo $prev_link;
		echo $pagination_links;
		echo $next_link;
		echo '<div style="clear:both;"></div>' . "\n";
		echo "</div>\n";
	}

} else {
	echo "<div>ZERO RECORDS RETURNED!</div>";
}
print "
<div style='position:relative; top:20px; padding:5px; border:1px solid dimgray;'>
<code style='margin:10px 0px 10px 0px;'><strong>SQL QUERY:</strong> $sql</code>
<br/><br/>
<code><strong>COOKIE value</strong>: [$current_cookie]</code>
</div>
";

/*FUNCTIONS*/

function columnSortArrows($field, $text, $currentfield = null, $currentsort = null, $module_view_url = null, $query_string = null) {
	//defaults all field links to SORT ASC
	//if field link is current ORDERBY then make arrow and opposite current SORT

	$sortquery = "sort=ASC";
	$orderquery = "orderby=" . $field;

	if ($currentsort == "ASC") {
		$sortquery = "sort=DESC";
		$sortarrow = "<img src='$module_view_url/arrow_up.png' />";
	}

	if ($currentsort == "DESC") {
		$sortquery = "sort=ASC";
		$sortarrow = "<img src='$module_view_url/arrow_down.png' />";
	}

	if ($currentfield == $field) {
		$orderquery = "orderby=" . $field;
	} else {
		$sortarrow = null;
	}

	return '<a href="?' . $query_string . '&' . $orderquery . '&' . $sortquery . '">' . $text . '</a> ' . $sortarrow;

}

function dump($obj) {
	ob_start();
	var_dump($obj);
	$ret_val = ob_get_contents();
	ob_end_clean();
	return "<textarea rows='20' cols='150'>$ret_val</textarea>";
}

function escape_it($fld, $db_fld, $sql_where, $Pagination, $ignore_cookie) {
	$output = '';
	if (isset ($_POST[$fld]) && ($_POST[$fld])) {
		$criteria = "$db_fld LIKE '%" . mysql_real_escape_string($_POST[$fld]) . "%'";
		if (!$sql_where) {
			$sql_where = 'WHERE ';
			$sql_where .= $criteria;
		} else {
			$sql_where .= " OR $criteria";
		}

		//die("ALMOST setting cookie = [$sql_where]");
		$Pagination->set_sql_cookie($sql_where);
		$Pagination->set_search_cookie($_POST[$fld]);
	}
	$cookie_val = ($ignore_cookie) ? '' : $Pagination->get_cookie();
	$output = ($sql_where) ? $sql_where : $cookie_val;
	return $output;
}

function get_thumb($director, $album_id) {
	$album = $director->album->get($album_id);
	$contents = $album->contents[0];
	$thumb_url = '';
	$image = $contents[0];
	$thumb_url = $contents->content[0]->thumb->url;
	return $thumb_url;

}

function resolve_field_name($name,$db) {
	mysql_select_db($main_db_name);
        $id = '';
        $query = "SELECT * from exp_channel_fields WHERE field_name = '$name'";
        $result = $db->query($query);
        $id = $result->row('field_id');
        return $id;
}

function get_target_id($album_id,$db,$main_db_name) {
	mysql_select_db($main_db_name);
	$default_id = '58181';
    $target_id = '';
    $field_int = resolve_field_name('ssp_album_id',$db,$main_db_name);

     $id_query = "SELECT * FROM exp_channel_data WHERE field_id_$field_int = '$album_id'";
     $result = $db->query($id_query);

     if ($result->num_rows > 0) {
           $target_id = $result->row('entry_id');
     } else {
           $target_id = $default_id;
     }
     
     return $target_id;
}

?>
