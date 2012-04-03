<?php

//source unknown for logic of showPageNumbers()
//modified by drale.com - 1-19-2010
//added query_string reproduction and divs
//added showNext() and showPrev()

class Pagination {
	
	var $current_cookie;
	var $module_view_url;
	var $current_search_term;
	
	function Pagination($module_view_url) {
		$this->module_view_url = $module_view_url;
		$this->set_cookie_init();
		$this->set_search_term_init();
		$this->query_string = $this->queryString();
	}

	function get_cookie() {
		return $this->current_cookie;
	}
	
    function get_search_term() {
		return $this->current_search_term;
	}
	
	function set_search_term_init() {
		$cookie = '';
		if (isset ($_COOKIE['ssp_search_term']) && ($_COOKIE['ssp_search_term'])) {
			$cookie = $_COOKIE['ssp_search_term'];
			$cookie = base64_decode($cookie);
		}
		$this->current_search_term = $cookie;
	}
	
	function set_cookie_init() {
		$cookie = '';
		if (isset ($_COOKIE['ssp_search_query']) && ($_COOKIE['ssp_search_query'])) {
			$cookie = $_COOKIE['ssp_search_query'];
			$cookie = base64_decode($cookie);
		}
		$this->current_cookie = $cookie;
	}

	function set_sql_cookie($value) {

		$enc_val = base64_encode($value);
		setcookie ("ssp_search_query", $enc_val, time() + 7200);
		$this->current_cookie = $value;
	}
	
	function set_search_cookie($value) {
		$enc_val = base64_encode($value);
		setcookie ("ssp_search_term", $enc_val, time() + 7200);
		$this->current_search_term = $value;
	}

	function clear_cookie() {
		//setcookie("ssp_search_query", '0', time() + 7200);
		setcookie ("ssp_search_query", '', time() - 3600);
		setcookie ("ssp_search_term", '', time() - 3600);
	}



	function getStartRow($page, $limit) {
		$startrow = $page * $limit - ($limit);
		return $startrow;
	}
	function showPageNumbers($totalrows, $page, $limit) {

		$pagination_links = null;

		/*
		PAGINATION SCRIPT
		seperates the list into pages
		*/
		$numofpages = $totalrows / $limit;
		/* We divide our total amount of rows (for example 102) by the limit (25). This 
		
		will yield 4.08, which we can round down to 4. In the next few lines, we'll 
		create 4 pages, and then check to see if we have extra rows remaining for a 5th 
		page. */

		for ($i = 1; $i <= $numofpages; $i++) {
			/* This for loop will add 1 to $i at the end of each pass until $i is greater 
			than $numofpages (4.08). */

			if ($i == $page) {
				$pagination_links .= '<div class="page-link"><span>' . $i . '</span></div> ';
			} else {
				$pagination_links .= '<div class="page-link"><a href="?page=' . $i . '&' . $this->query_string . '">' . $i . '</a></div> ';
			}
			/* This if statement will not make the current page number available in 
			link form. It will, however, make all other pages available in link form. */
		} // This ends the for loop

		if (($totalrows % $limit) != 0) {
			/* The above statement is the key to knowing if there are remainders, and it's 
			all because of the %. In PHP, C++, and other languages, the % is known as a 
			Modulus. It returns the remainder after dividing two numbers. If there is no 
			remainder, it returns zero. In our example, it will return 0.8 */

			if ($i == $page) {
				$pagination_links .= '<div class="page-link"><span>' . $i . '</span></div> ';
			} else {
				$pagination_links .= '<div class="page-link"><a href="?page=' . $i . '&' . $this->query_string . '">' . $i . '</a></div> ';
			}
			/* This is the exact statement that turns pages into link form that is used above */
		} // Ends the if statement 

		return $pagination_links;
	}

	//added by drale.com - 1-19-2010
	function showNext($totalrows, $page, $limit, $text = "next &raquo;") {
		$next_link = null;
		$numofpages = $totalrows / $limit;

		if ($page < $numofpages) {
			$page++;
			$next_link = '<div class="page-link"><a href="?page=' . $page . '&' . $this->query_string . '">' . $text . '</a></div>';
		}

		return $next_link;
	}

	function showPrev($totalrows, $page, $limit, $text = "&laquo; prev") {
		$prev_link = null;
		$numofpages = $totalrows / $limit;

		if ($page > 1) {
			$page--;
			$prev_link = '<div class="page-link"><a href="?page=' . $page . '&' . $this->query_string . '">' . $text . '</a></div>';
		}

		return $prev_link;
	}

	function queryString() {
		//matches up to 10 digits in page number
		$query_string = eregi_replace("page=[0-9]{0,10}&", "", $_SERVER['QUERY_STRING']);
		return $query_string;
	}
}
?>