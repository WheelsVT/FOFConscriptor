<?
/***************************************************************************
 *                                list.inc.php
 *                            -------------------
 *   begin                : Friday, Mar 28, 2008
 *   copyright            : (C) 2008 J. David Baker
 *   email                : me@jdavidbaker.com
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

class table_list {
  // This class will handle the drawing of all lists.  Call the initialization functions to set
  // things like the headers, date format, etc.  Then call the draw_list function with the sql statement
  // to draw the list.  It will keep track of its own page number, sort, etc.
  function table_list($multipage = true, $interface_append = '') {
    $this->header = array();
    $this->multipage = $multipage;
    $this->interface_id = md5($_SERVER['SCRIPT_NAME'].$interface_append);

    // Retrieve the order_by value
    $this->order_by = $_GET['order_by'];
    if (!$this->order_by) {
      $this->order_by = $_SESSION['list_order_by'][$this->interface_id];
    }
    $_SESSION['list_order_by'][$this->interface_id] = $this->order_by;

    // Retrieve the page page value, and set to 0 if not set
    $this->page = $_POST['page'];
    if (!$this->page) {
      $this->page = $_SESSION['current_page'][$this->interface_id];
    }
    if (!$this->page) {
      $this->page = 1;
    }
    $_SESSION['current_page'][$this->interface_id] = $this->page;

    // Retrieve the records per page, set it to 32 if not set
    $this->records_per_page = $_POST['records_per_page'];
    if (!$this->records_per_page) {
      $this->records_per_page = $_COOKIE['records_per_page_'.$this->interface_id];
    }
    if (!$this->records_per_page) {
      $this->records_per_page = 32;
    }
    setcookie('records_per_page_'.$this->interface_id, $this->records_per_page, time()+60*60*24*360*10);
    $this->queries = array();

    $this->show_totals = true;
    $this->classes = array("data");
    $this->id = null;
  }

  function append_query($queries) {
    // Add $queries to the end of the query to get back to this page
    $this->queries = array_merge($this->queries, $queries);
  }

  function set_show_totals($value) {
    $this->show_totals = $value;
  }

  function set_class($class) {
    $this->classes[] = $class;
  }

  function clear_order_by() {
    $this->order_by = -1;
  }

  function set_id($id) {
    // Sets the id attribute for the table
    $this->id = ' id="'.$id.'"';
  }

  function set_header($column, $text, $default_sort = '0', $link = true, $sort = true) {
    $this->header[$column]['text'] = $text;
    $this->header[$column]['sort'] = $sort;
    $this->header[$column]['link'] = $link;
    if ($default_sort || !$this->default_sort) {
      $this->default_sort = $column;
      if ($default_sort == 'desc') {
	$this->default_sort .= ' desc';
      }
    }
  }

  function set_date_format($column, $format) {
    $this->header[$column]['date'] = $format;
  }

  function set_link($link, $id_col) {
    // Pass the link in the form of "...?id=" and the value of $id_col will be appended to the end of the link.
    $this->link = $link;
    $this->id_col = $id_col;
  }

  function set_data_format($column, $format) {
    // Pass the format in "text text %data% text text" - %data% will be replaced by the data, %id% will be replaced by
    // $id_col in the link
    $this->header[$column]['format'] = $format;
  }

  function set_number_format($column, $format) {
    $this->header[$column]['number_format'] = $format;
  }

  function set_exec($column, $command) {
    // A command to execute on the data, passing the data as the single argument
    $this->header[$column]['exec'] = $command;
  }

  function add_order_by($column) {
    $this->add_order_by[] = $column;
  }

  function set_form($action, $method, $name = "form") {
    // Info so that we can draw a form around the table
    $this->form['name'] = $name;
    $this->form['action'] = $action;
    $this->form['method'] = $method;
  }

  function set_form_extra($extra) {
    // Extra values for the form
    $this->form['extra'] = $extra;
  }

  function set_form_button($name, $value, $type="submit") {
    $this->buttons[$name]['value'] = $value;
    $this->buttons[$name]['type'] = $type;
  }

  function set_map_data($lat, $long) {
    $this->latitude = $lat;
    $this->longitude = $long;
    $this->has_map = true;
    if ($_GET['map'] == -1) {
      $this->show_map = false;
    } elseif ($_GET['map'] == 1) {
      $this->show_map = true;
    } else {
      $this->show_map = $_SESSION['show_map'][$this->interface_id];
    }
    $_SESSION['show_map'][$this->interface_id] = $this->show_map;
  }

  function set_extra_text($text) {
    // Adds $text to the bottom of the form
    // Can call more than once, it will append to the end.
    $this->extra_text[] = $text;
  }

  function set_style($column) {
    // Pass a column to use as the contents of the "style" tag
    $this->style = $column;
  }

  function set_cell_style($data_column, $style_column) {
    $this->cell_style[$data_column] = $style_column;
  }

  function draw_list($statement) {
    // Generate the queries for the heading links
    $queries = array();
    if (is_array($this->queries)) {
      foreach($this->queries as $key=>$value) {
	$queries[] = "$key=$value";
      }
    }
    $queries = implode("&", $queries);
    // If we are showing the map, branch to the draw_map function
    if ($this->show_map) {
      return $this->draw_map($statement, $queries);
    }
    // Append the order by
    $columns = count($this->header);
    $width = ceil(100/$columns);
    if ($this->order_by != -1) {
      if (!array_key_exists(preg_replace("/ desc/", "", $this->order_by), $this->header)) {
	$this->order_by = $this->default_sort;
      }
    }
    $statement .= " order by ".$this->order_by;
    if (is_array($this->add_order_by)) {
      if ($this->order_by) {
	$statement .= ',';
      }
      $statement .= implode(",",$this->add_order_by);
    }
    $result = mysql_query($statement);
    if (mysql_error()) {
      $html = "<p>There was an error in the list statement:";
      $html .= "<p>".htmlentities($statement);
      $html .= "<p>".htmlentities(mysql_error());
      return $html;
    }
    // Count the rows
    $this->rows = mysql_num_rows($result);
    $this->pages = ceil($this->rows/$this->records_per_page);
    if ($this->page > $this->pages - 1) {
      $this->page = max($this->pages, 1);
    } elseif ($this->page < 1) {
      $this->page = 1;
    }
    // Decrement $this->page so that we have it 0-based
    $this->page --;

    // Store the statement in the session for the mailmerge export
    $_SESSION['list_statement'] = $statement;
    $_SESSION['list_header'] = $this->header;

    /*
    // Give the mailmerge link
    $html .= '
<p class="no_print" align="center"><a href="mailmerge.php">Save as Mail Merge</a></p>';
    */
    // Draw the pages
    if ($this->multipage) {
      $html .= $this->draw_page_numbers();
    }
    
    if ($this->has_map) {
      $html .= '
<p align="center" class="no_print"><a href="?'.$queries.'&map=1">&raquo; Show map</a></p>';
    }

    // Draw the form if we have one
    if (is_array($this->form)) {
      $html .= '
<form method="'.$this->form['method'].'" action="'.$this->form['action'].'" name="'.$this->form['name'].'" '.$this->form['extra'].'>';
    }

    // Draw the header
    $html .= '
<table class="'.implode(" ",$this->classes).'" align="center"'.$this->id.'>
  <tbody class="'.implode(" ",$this->classes).'">
    <tr class="heading">';
    foreach ($this->header as $header=>$values) {
      if (preg_match("/^".$header."( desc)?$/", $this->order_by)) {
	if (preg_match("/ desc$/", $this->order_by)) {
	  $arrow = '<img src="images/arrow_down.gif" border="0"> ';
	  $desc = '';
	} else {
	  $arrow = '<img src="images/arrow_up.gif" border="0"> ';
	  $desc = " desc";
	}
      } else {
	$arrow = "";
	$desc = "";
      }
      $html .= '
      <td class="heading">';
      if ($values['sort']) {
	$html .= '
        <a href="?'.$queries.'&order_by='.$header . $desc.'">';
      }
      $html .= $values['text'].' '.$arrow;
      if ($values['sort']) {
	$html .= '
        </a>';
      }
      $html .= '
      </td>';
    }
    $html .= '
    </tr>';
    // Draw the rows
    if ($this->multipage) {
      $first = $this->page * $this->records_per_page;
      $last = $first + $this->records_per_page;
    } else {
      $first = 0;
      $last = mysql_num_rows($result);
    }
    if ($last > mysql_num_rows($result)) {
      $last = mysql_num_rows($result);
    }
    $current = $first;
    while ($current < $last) {
      if ($class == "light") {
	$class = "dark";
      } else {
	$class = "light";
      }
      $html .= '
    <tr>';
      foreach ($this->header as $header=>$values) {
	if ($this->style) {
	  $style = ' style="'.mysql_result($result, $current, $this->style).'"';
	} else {
	  $style = '';
	}
	$html .= '
      <td class="'.$class.'"'.$style.'>';
	if ($style) {
	  $html .= '<span '.$style.'>';
	}
	$html .= $this->get_data($result, $values, $current, $header);
	if ($style) {
	  $html .= '</span>';
	}
	$html .= '
      </td>';	
      }
      $html .= '
    </tr>';
      $current++;
    }
    $html .= '
  </tbody>
</table>';

    // Append any extra text
    if ($this->extra_text) {
      foreach($this->extra_text as $text) {
	$html .= $text;
      }
    }

    // Close the form
    if (is_array($this->buttons)) {
      $html .= '
<p align="center">';
      foreach ($this->buttons as $name=>$value) {
	$html .= '
<input type="'.$value['type'].'" name="'.$name.'" value="'.$value['value'].'">';
      }
    }

    if (is_array($this->form)) {
      $html .= '
</form>';
    }
    // How many are we showing on this page?
    if ($this->show_totals) {
      $html .= '
<p class="report_total">Showing '.number_format(($first+1),0).' - '.number_format($last,0).' of '.number_format($this->rows,0)."</p>";
    }
    // Draw the pages at the end
    if ($this->multipage) {
      $html .= $this->draw_page_numbers();
    }
    return $html;
  }

  function get_data($result, $values, $record, $header) {
    if ($this->link) {
      if (preg_match('/%id%/', $this->link)) {
	$link = str_replace("%id%", mysql_result($result, $record, $this->id_col), $this->link);
      } else {
	$link = $this->link . mysql_result($result, $record, $this->id_col);
      }
    } else {
      $link = '';
    }
    if ($this->cell_style[$header]) {
      $style = mysql_result($result, $record, $this->cell_style[$header]);
    }
    $html .= '
      <span style="'.$style.'">';
    if ($link && $values['link']) {
      if (!$style) {
	if ($this->style) {
	  $style = ' style="'.mysql_result($result, $record, $this->style).'"';
	} else {
	  $style = '';
	}
      }
      $html .= '
      <a href="'.$link.'"'.$style.'>';
    }
    $data = mysql_result($result, $record, $header);
    if ($values['exec']) {
      $command = $values['exec'];
      $data = $command($data);
    }
    if ($values['date']) {
      if ($data == '0000-00-00' || $data == '0000-00-00 00:00:00' || !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}/", $data)) {
	if (!$data || preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}/", $data)) {
	  $data = "N/A";
	}
      } else {
	$data = date($values['date'], strtotime($data));
      }
    }
    if ($values['number_format']) {
      $data = number_format($data, $values['number_format']);
    }
    if ($values['format']) {
      $data = str_replace("%data%", $data, $values['format']);
    }
    if ($this->id_col) {
      $data = str_replace("%id%", mysql_result($result, $record, $this->id_col), $data);
    }
    if (!$data) {
      $data = '&nbsp;';
    }
    $html .= $data;
    if ($link) {
      $html .= '
      </a>';
    }
    $html .= '</span>';
    return $html;
  }

  function draw_page_numbers() {
    $id = uniqid('form');
    $page = 1;
    $html = '
<form method="post" action="'.$_SERVER['REQUEST_URI'].'" name="'.$id.'">
<table class="pages" align="center">';
    foreach($_GET as $key=>$value) {
      if ($key != "page" && $key != "records_per_page") {
	$html .= '
<input type="hidden" name="'.$key.'" value="'.$value.'">';
      }
    }
    $html .= '
  <tr>
    <td class="pages">Page:</td>
    <td class="pages">
      <select name="page" onchange="document.'.$id.'.submit();" class="pages">';
    while ($page <= $this->pages) {
      if ($this->page+1 == $page) {
	$selected = " selected";
      } else {
	$selected = "";
      }
      $html .= '
<option value="'.$page.'"'.$selected.'>'.$page.'</option>';
      $page++;
    }
    $html .= '
      </select>
    </td>
    <td class="pages" width="25">&nbsp;</td>
    <td class="pages">Rows Per Page:</td>
    <td class="pages">
      <select name="records_per_page" onchange="document.'.$id.'.submit()">';
    $options = array(5, 10, 25, 32, 50, 100, 200, 300, 400, 500, 1000, 2000, 5000);
    foreach($options as $value) {
      if ($value == $this->records_per_page) {
	$selected = " selected";
      } else {
	$selected = "";
      }
      $html .= '
        <option value="'.$value.'"'.$selected.'>'.$value.'</option>';
    }
    $html .= '
      </select>
    </td>
  </tr>
</table>
</form>';
    return $html;
  }

  function draw_map($statement, $queries) {
    // Draws the results in a map
    $html = '
<p align="center"><a href=./?'.$queries.'&map=-1">&raquo; List View</a></p>';
    $html .= file_get_contents("includes/html/map.html");
    $html = str_replace("%google_key%", kGoogleKey, $html);

    $result = mysql_query($statement);
    $i=0;
    while ($row = mysql_fetch_array($result)) {
      if ($row[$this->latitude] != 0) {
	// We have a valid latitude and longitude
	$bubble = '
<table>';
	foreach($this->header as $header=>$values) {
	  $bubble .= '
  <tr>
    <td align="right"><b>'.$values['text'].':</b></td>
    <td>'.$this->get_data($result, $values, $i, $header).'</td>
  </tr>';
	}
	$bubble .= '
</table>';
	$bubble = addslashes(str_replace("\n", "", $bubble));
	$markers .= '
point = new GLatLng('.$row[$this->latitude].', '.$row[$this->longitude].');
map.addOverlay(createMarker(point, \''.$bubble.'\', greenIcon));';
	$i++;
      }
    }

    $html = str_replace("%markers%", $markers, $html);
    return $html;
  }

  function export_mailmerge() {
    // Takes the stored statement and exports a CSV with the headers in the first column
    $this->header = $_SESSION['list_header'];
    $statement = $_SESSION['list_statement'];
    
    // Print the headers
    $data = array();
    $final = '';
    foreach($this->header as $header=>$values) {
      $data[] = '"'.$values['text'].'"';
    }
    $final .= implode(",",$data)."\r\n";

    $result = mysql_query($statement);
    $current = 0;
    $last = mysql_num_rows($result);
    while ($current < $last) {
      $data = array();
      foreach($this->header as $header=>$values) {
	$data[] = '"'.$this->get_data($result, $values, $current, $header).'"';
      }
      $final .= implode(",",$data)."\r\n";
      $current ++;
    }

    // Send the csv file as an attachment
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=ReportExport.csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $final;
  }
}
?>