<?PHP

// Copyright (c) 2007-2008 Sancho Lerena, slerena@gmail.com
// Copyright (c) 2008 Esteban Sanchez, estebans@artica.es
// Copyright (c) 2007-2011 Artica, info@artica.es

// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public License
// (LGPL) as published by the Free Software Foundation; version 2

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.

//JQuery 1.6.1 library addition

global $config;


function include_javascript_dependencies_flot_graph($return = false) {
	global $config;
	
	static $is_include_javascript = false;
	
	if (!$is_include_javascript) {
		$is_include_javascript = true;
		
		$metaconsole_hack = '';
		if (defined('METACONSOLE')) {
			$metaconsole_hack = '../../';
		}
		
		// NOTE: jquery.flot.threshold is not te original file. Is patched to allow multiple thresholds and filled area
		$output = '
			<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="' . ui_get_full_url($metaconsole_hack . '/include/graphs/flot/excanvas.js') . '"></script><![endif]-->
			<script language="javascript" type="text/javascript" src="'.
				ui_get_full_url($metaconsole_hack . '/include/graphs/flot/jquery.flot.min.js') .'"></script>
			<script language="javascript" type="text/javascript" src="'.
				ui_get_full_url($metaconsole_hack  . '/include/graphs/flot/jquery.flot.pie.js') .'"></script>
			<script language="javascript" type="text/javascript" src="'.
				ui_get_full_url($metaconsole_hack . '/include/graphs/flot/jquery.flot.crosshair.min.js') .'"></script>
			<script language="javascript" type="text/javascript" src="'.
				ui_get_full_url($metaconsole_hack . '/include/graphs/flot/jquery.flot.stack.min.js') .'"></script>
			<script language="javascript" type="text/javascript" src="'.
				ui_get_full_url($metaconsole_hack . '/include/graphs/flot/jquery.flot.selection.min.js') .'"></script>
			<script language="javascript" type="text/javascript" src="'.
				ui_get_full_url($metaconsole_hack . '/include/graphs/flot/jquery.flot.resize.min.js') .'"></script>
			<script language="javascript" type="text/javascript" src="'.
				ui_get_full_url($metaconsole_hack . '/include/graphs/flot/jquery.flot.threshold.js') .'"></script>
			<script language="javascript" type="text/javascript" src="'.
				ui_get_full_url($metaconsole_hack . '/include/graphs/flot/jquery.flot.symbol.min.js') .'"></script>
			<script language="javascript" type="text/javascript" src="'.
				ui_get_full_url($metaconsole_hack . '/include/graphs/flot/jquery.flot.exportdata.pandora.js') .'"></script>
			<script language="javascript" type="text/javascript" src="'.
				ui_get_full_url($metaconsole_hack . '/include/graphs/flot/jquery.flot.axislabels.js') .'"></script>
			<script language="javascript" type="text/javascript" src="'.				
				ui_get_full_url($metaconsole_hack . '/include/graphs/flot/pandora.flot.js') .'"></script>';
		$output .= "
			<script type='text/javascript'>
			var precision_graph = " . $config['graph_precision'] . ";
			function pieHover(event, pos, obj) 
			{
				if (!obj)
					return;
				percent = parseFloat(obj.series.percent).toFixed(2);
				$('#hover').html('<span style=\'font-weight: bold; color: '+obj.series.color+'\'>'+obj.series.label+' ('+percent+'%)</span>');
				$('.legendLabel').each(function() {
					if ($(this).html() == obj.series.label) {
						$(this).css('font-weight','bold');
					}
					else {
						$(this).css('font-weight','');
					}
				});
			}
			
			function pieClick(event, pos, obj) 
			{
				if (!obj)
					return;
				percent = parseFloat(obj.series.percent).toFixed(2);
				alert(''+obj.series.label+': '+obj.series.data[0][1]+' ('+percent+'%)');
			}
			</script>";
		
		if (!$return)
			echo $output;
		
		return $output;
	}
}

///////////////////////////////
////////// AREA GRAPHS ////////
///////////////////////////////
function flot_area_stacked_graph($chart_data, $width, $height, $color,
	$legend, $long_index, $homeurl = '', $font = '', $font_size = 7, 
	$unit = '', $water_mark = '', $serie_types = array(), 
	$chart_extra_data = array(), $yellow_threshold = 0, 
	$red_threshold = 0, $adapt_key= '', $force_integer = false, 
	$series_suffix_str = '', $menu = true, $background_color = 'white', 
	$dashboard = false, $vconsole = false, $agent_module_id = 0) {
	
	global $config;
	
	return flot_area_graph($chart_data, $width, $height, $color,
		$legend, $long_index, $homeurl, $unit, 'area_stacked',
		$water_mark, $serie_types, $chart_extra_data, $yellow_threshold,
		$red_threshold, $adapt_key, $force_integer, $series_suffix_str,
		$menu, $background_color, $dashboard, $vconsole, 
		$agent_module_id, $font, $font_size);
}

function flot_area_simple_graph($chart_data, $width, $height, $color,
	$legend, $long_index, $homeurl = '', $unit = '', $water_mark = '',
	$serie_types = array(), $chart_extra_data = array(),
	$yellow_threshold = 0, $red_threshold = 0, $adapt_key= '',
	$force_integer = false, $series_suffix_str = '', $menu = true,
	$background_color = 'white', $dashboard = false, $vconsole = false, 
	$agent_module_id = 0, $font = '', $font_size = 7, $xaxisname = '', 
	$percentil_values = array(), $threshold_data = array()) {
	
	global $config;

	return flot_area_graph($chart_data, $width, $height, $color,
		$legend, $long_index, $homeurl, $unit, 'area_simple',
		$water_mark, $serie_types, $chart_extra_data, $yellow_threshold,
		$red_threshold, $adapt_key, $force_integer, $series_suffix_str,
		$menu, $background_color, $dashboard, $vconsole, 
		$agent_module_id, $font, $font_size, $xaxisname, $percentil_values,
		$threshold_data);
}

function flot_line_stacked_graph($chart_data, $width, $height, $color,
	$legend, $long_index, $homeurl = '', $font = '', $font_size = 7, 
	$unit = '', $water_mark = '', $serie_types = array(), 
	$chart_extra_data = array(), $yellow_threshold = 0, 
	$red_threshold = 0, $adapt_key= '', $force_integer = false, 
	$series_suffix_str = '', $menu = true, $background_color = 'white', 
	$dashboard = false, $vconsole = false, $agent_module_id = 0,
	$xaxisname = "") {
	
	global $config;
	
	return flot_area_graph($chart_data, $width, $height, $color,
		$legend, $long_index, $homeurl, $unit, 'line_stacked',
		$water_mark, $serie_types, $chart_extra_data, $yellow_threshold,
		$red_threshold, $adapt_key, $force_integer, $series_suffix_str,
		$menu, $background_color, $dashboard, $vconsole, 
		$agent_module_id, $font, $font_size, $xaxisname);
}

function flot_line_simple_graph($chart_data, $width, $height, $color,
	$legend, $long_index, $homeurl = '', $font = '', $font_size = 7, 
	$unit = '', $water_mark = '', $serie_types = array(), 
	$chart_extra_data = array(), $yellow_threshold = 0, 
	$red_threshold = 0, $adapt_key= '', $force_integer = false, 
	$series_suffix_str = '', $menu = true, $background_color = 'white', 
	$dashboard = false, $vconsole = false, $agent_module_id = 0, 
	$percentil_values = array(), $threshold_data = array(),
	$xaxisname = '') {
	
	global $config;
	
	return flot_area_graph($chart_data, $width, $height, $color,
		$legend, $long_index, $homeurl, $unit, 'line_simple',
		$water_mark, $serie_types, $chart_extra_data, $yellow_threshold,
		$red_threshold, $adapt_key, $force_integer, $series_suffix_str,
		$menu, $background_color, $dashboard, $vconsole, 
		$agent_module_id, $font, $font_size, $xaxisname, $percentil_values,
		$threshold_data);
}

function flot_area_graph($chart_data, $width, $height, $color, $legend,
	$long_index, $homeurl, $unit, $type, $water_mark, $serie_types,
	$chart_extra_data, $yellow_threshold, $red_threshold, $adapt_key,
	$force_integer, $series_suffix_str = '', $menu = true,
	$background_color = 'white', $dashboard = false, $vconsole = false, 
	$agent_module_id = 0, $font = '', $font_size = 7, $xaxisname = '',
	$percentil_values = array(), $threshold_data = array()) {
	
	global $config;
	
	include_javascript_dependencies_flot_graph();

	$menu = (int)$menu;
	// Get a unique identifier to graph
	$graph_id = uniqid('graph_');
	
	$background_style = '';
	switch ($background_color) {
		default:
		case 'white':
			$background_style = ' background: #fff; ';
			break;
		case 'black':
			$background_style = ' background: #000; ';
			break;
		case 'transparent':
			$background_style = '';
			break;
	}
	
	// Parent layer
	$return = "<div class='parent_graph' style='width: " . ($width) . "px; " . $background_style . "'>";
	// Set some containers to legend, graph, timestamp tooltip, etc.
	$return .= "<p id='legend_$graph_id' class='legend_graph' style='font-size:$font_size"."pt !important;'></p>";
	
	if (!empty($threshold_data)) {
		$yellow_up = $threshold_data['yellow_up'];
		$red_up = $threshold_data['red_up'];
		$yellow_inverse = $threshold_data['yellow_inverse'];
		$red_inverse = $threshold_data['red_inverse'];
	}
	else {
		// Get other required module datas to draw warning and critical
		if ($agent_module_id == 0) {
			$yellow_up = 0;
			$red_up = 0;
			$yellow_inverse = false;
			$red_inverse = false;
		} else {
			$module_data = db_get_row_sql ('SELECT * FROM tagente_modulo WHERE id_agente_modulo = ' . $agent_module_id);
			$yellow_up = $module_data['max_warning'];
			$red_up = $module_data['max_critical'];
			$yellow_inverse = !($module_data['warning_inverse'] == 0);
			$red_inverse = !($module_data['critical_inverse'] == 0);
		}
	}
	
	if ($menu) {
		$threshold = false;
		if ($yellow_threshold != $yellow_up || $red_threshold != $red_up) {
			$threshold = true;
		}
		
		$nbuttons = 3;
		
		if ($threshold) {
			$nbuttons++;
		}
		$menu_width = 25 * $nbuttons + 15;
		if ( $dashboard == false AND $vconsole == false) {
			$return .= "<div id='geneal_menu_$graph_id' class='menu_graph' style='
							width: 30px;
							height: 250px;
							left: " . $width . "px;
							position: absolute;
							top: 0px;
							background-color: white;'>";
			$return .= "<div id='menu_$graph_id' " .
				"style='display: none; " .
					"text-align: center;" .
					"position: relative;".
					"border-bottom: 0px;'>
				<a href='javascript:'><img id='menu_cancelzoom_$graph_id' src='".$homeurl."images/zoom_cross_grey.disabled.png' alt='".__('Cancel zoom')."' title='".__('Cancel zoom')."'></a>";
			if ($threshold) {
				$return .= " <a href='javascript:'><img id='menu_threshold_$graph_id' src='".$homeurl."images/chart_curve_threshold.png' alt='".__('Warning and Critical thresholds')."' title='".__('Warning and Critical thresholds')."'></a>";
			}
			$return .= " <a href='javascript:'>
				<img id='menu_overview_$graph_id' class='menu_overview' src='" . $homeurl . "images/chart_curve_overview.png' alt='" . __('Overview graph') . "' title='".__('Overview graph')."'></a>";
			
			// Export buttons
			$return .= " <a href='javascript:'><img id='menu_export_csv_$graph_id' src='".$homeurl."images/csv_grey.png' alt='".__('Export to CSV')."' title='".__('Export to CSV')."'></a>";
			// Button disabled. This feature works, but seems that is not useful enough to the final users.
			//$return .= " <a href='javascript:'><img id='menu_export_json_$graph_id' src='".$homeurl."images/json.png' alt='".__('Export to JSON')."' title='".__('Export to JSON')."'></a>";
			
			$return .= "</div>";
			$return .= "</div>";
		}

		if ($dashboard) {
			$return .= "<div id='geneal_menu_$graph_id' class='menu_graph' style='
							width: 30px;
							height: 250px;
							left: " . $width . "px;
							position: absolute;
							top: 0px;
							background-color: white;'>";

			$return .= "<div id='menu_$graph_id' " .
				"style='display: none; " .
					"text-align: center;" .
					"position: relative;".
					"border-bottom: 0px;'>
				<a href='javascript:'><img id='menu_cancelzoom_$graph_id' src='".$homeurl."images/zoom_cross_grey.disabled.png' alt='".__('Cancel zoom')."' title='".__('Cancel zoom')."'></a>";
		
			$return .= "</div>";
			$return .= "</div>";
		}
	}

	$return .= html_print_input_hidden('line_width_graph', $config['custom_graph_width'], true);
	$return .= "<div id='timestamp_$graph_id' class='timestamp_graph' style='font-size:".$font_size."pt;display:none; position:absolute; background:#fff; border: solid 1px #aaa; padding: 2px; z-index:1000;'></div>";
	$return .= "<div id='$graph_id' class='";
	if($type=='area_simple'){$return .= "noresizevc ";}
	$return .= "graph $adapt_key' style='width: ".$width."px; height: ".$height."px;'></div>";
	if ($menu) {
		$height = 100;
	}
	else {
		$height = 1;
	}
	if (!$vconsole)
		$return .= "<div id='overview_$graph_id' class='overview_graph' style='display: none; margin-left:0px; margin-top:20px; margin-bottom:50px; width: ".$width."px; height: ".$height ."px;'></div>";
	
	if ($water_mark != '') {
		$return .= "<div id='watermark_$graph_id' style='display:none; position:absolute;'><img id='watermark_image_$graph_id' src='$water_mark'></div>";
		$watermark = 'true';
	}
	else {
		$watermark = 'false';
	}
	
	// Set a weird separator to serialize and unserialize passing data from php to javascript
	$separator = ';;::;;';
	$separator2 = ':,:,,,:,:';
	
	// Transform data from our format to library format
	$legend2 = array();
	$labels = array();
	$a = array();
	$vars = array();
	$serie_types2 = array();
	
	$colors = array();
	
	$index = array_keys(reset($chart_data));
	foreach ($index as $serie_key) {
		if (isset($color[$serie_key])) {
			$colors[] = $color[$serie_key]['color'];
		}
		else {
			$colors[] = '';
		}
	}
	
	foreach ($chart_data as $label => $values) {
		$labels[] = $label;
		
		foreach($values as $key => $value) {
			$jsvar = "data_" . $graph_id . "_" . $key;
			
			
			if (!isset($serie_types[$key])) {
				switch ($type) {
					case 'line_simple':
					case 'line_stacked':
						$serie_types2[$jsvar] = 'line';
						break;
					case 'area_simple':
					case 'area_stacked':
					default:
						$serie_types2[$jsvar] = 'area';
						break;
				}
			}
			else {
				$serie_types2[$jsvar] = $serie_types[$key];
			}
			
			
			if ($serie_types2[$jsvar] == 'points' && $value == 0) {
				$data[$jsvar][] = 'null';
			}
			else {
				$data[$jsvar][] = $value;
			}
			
			if (!isset($legend[$key])) {
				$legend2[$jsvar] = 'null';
			}
			else {
				$legend2[$jsvar] = $legend[$key];
			}
		}
	}

	if (!empty($percentil_values)) {
		foreach($percentil_values as $key => $value) {
			$jsvar = "percentil_" . $graph_id . "_" . $key;
			$serie_types2[$jsvar] = 'line';
			$data[$jsvar] = $value;
		}
	}
	
	// Store data series in javascript format
	$jsvars = '';
	$jsseries = array();
	$values2 = array();
	$i = 0;
	$max_x = 0;
	foreach ($data as $jsvar => $values) {
		$n_values = count($values);
		if ($n_values > $max_x) {
			$max_x = $n_values;
		}
		
		$values2[] = implode($separator,$values);
		$i ++;
	}
	
	$values = implode($separator2, $values2);
	
	// Max is "n-1" because start with 0 
	$max_x--;
	
	$extra_width = (int)($width / 3);
	
	$return .= "<div id='extra_$graph_id' style='font-size: " . $font_size . "pt; display:none; position:absolute; overflow: auto; max-height: ".($height+50)."px; width: ".$extra_width."px; background:#fff; padding: 2px 2px 2px 2px; border: solid #000 1px;'></div>";
	
	// Process extra data
	$events = array();
	$event_ids = array();
	$alerts = array();
	$alert_ids = array();
	$legend_events = '';
	$legend_alerts = '';
	
	if (empty($chart_extra_data)) {
		$chart_extra_data = array();
	}
	
	foreach ($chart_extra_data as $i => $data) {
		switch ($i) {
			case 'legend_alerts':
				$legend_alerts = $data;
				break;
			case 'legend_events':
				$legend_events = $data;
				break;
			default:
				if (isset($data['events'])) {
					$event_ids[] = $i;
					$events[$i] = $data['events'];
				}
				if (isset($data['alerts'])) {
					$alert_ids[] = $i;
					$alerts[$i] = $data['alerts'];
				}
				break;
		}
	}
	
	// Store serialized data to use it from javascript
	$events = implode($separator,$events);
	$event_ids = implode($separator,$event_ids);
	$alerts = implode($separator,$alerts);
	$alert_ids = implode($separator,$alert_ids);
	$labels = implode($separator,$labels);
	if (!empty($long_index)) {
		$labels_long = implode($separator, $long_index);
	}
	else {
		$labels_long = $labels;
	}
	if (!empty($legend)) {
		$legend = io_safe_output(implode($separator, $legend));
	}
	$serie_types  = implode($separator, $serie_types2);
	$colors  = implode($separator, $colors);
	
	// transform into string to pass to javascript
	if ($force_integer) {
		$force_integer = 'true';
	}
	else {
		$force_integer = 'false';
	}
	
	//modify internal grid lines and legend text color
	
	if(substr($background_style, -6, 4) == '#fff'){
		$background_color = "#eee";
		$legend_color = "#151515";
		
	}
	else if(substr($background_style, -6, 4) == '#000'){
		$background_color = "#151515";
		$legend_color = "#BDBDBD";
	}
	else{
		$background_color = "#A4A4A4";
		$legend_color = "#A4A4A4";
	}
	
	// Trick to get translated string from javascript
	$return .= html_print_input_hidden('unknown_text', __('Unknown'),
		true);

	if (!isset($config["short_module_graph_data"]))
		$config["short_module_graph_data"] = '';
	
	$short_data = $config["short_module_graph_data"];

	
	
	// Javascript code
	$return .= "<script type='text/javascript'>";
	$return .= "$(document).ready( function () {";
	$return .= "pandoraFlotArea(" .
		"'$graph_id', \n" .
		"'$values', \n" .
		"'$labels', \n" .
		"'$labels_long', \n" .
		"'$legend', \n" .
		"'$colors', \n" .
		"'$type', \n" .
		"'$serie_types', \n" .
		"$watermark, \n" .
		"$width, \n" .
		"$max_x, \n" .
		"'$homeurl', \n" .
		"'$unit', \n" .
		"$font_size, \n" .
		"'$font', \n" .
		"$menu, \n" .
		"'$events', \n" .
		"'$event_ids', \n" .
		"'$legend_events', \n" .
		"'$alerts', \n" .
		"'$alert_ids', \n" .
		"'$legend_alerts', \n" .
		"'$yellow_threshold', \n" .
		"'$red_threshold', \n" .
		"$force_integer, \n" .
		"'$separator', \n" .
		"'$separator2', \n" .
		"'$yellow_up', \n" .
		"'$red_up', \n" .
		"'$yellow_inverse', \n" .
		"'$red_inverse', \n" .
		"'$series_suffix_str',
		" . json_encode($dashboard) . ",\n
		" . json_encode($vconsole) . ",\n" .
		"'$xaxisname', \n" .
		"'$background_color', \n" .
		"'$legend_color', \n" .
		"'$short_data'
	);";
	$return .= "});";
	$return .= "</script>";
	
	// Parent layer
	$return .= "</div>";
	
	return $return;
}

///////////////////////////////
///////////////////////////////
///////////////////////////////

// Prints a FLOT pie chart
function flot_pie_chart ($values, $labels, $width, $height, $water_mark,
	$font = '', $font_size = 8, $legend_position = '', $colors = '',
	$hide_labels = false) {
	
	include_javascript_dependencies_flot_graph();
	
	$series = sizeof($values);
	if (($series != sizeof ($labels)) || ($series == 0) ) {
		return;
	}
	
	$graph_id = uniqid('graph_');
	
	switch ($legend_position) {
		case 'bottom':
			$height = $height + (count($values) * 24);
			break;
		case 'right':
		default:
			//TODO FOR TOP OR LEFT OR RIGHT
			break;
	}
	
	$return = "<div id='$graph_id' class='graph' style='width: ".$width."px; height: ".$height."px;'></div>";
	
	if ($water_mark != '') {
		$return .= "<div id='watermark_$graph_id' style='display:none; position:absolute;'><img id='watermark_image_$graph_id' src='$water_mark'></div>";
		$water_mark = 'true';
	}
	else {
		$water_mark = 'false';
	}
	
	$separator = ';;::;;';
	
	$labels = implode($separator, $labels);
	$values = implode($separator, $values);
	if (!empty($colors)) {
		$colors = implode($separator, $colors);
	}
	
	$return .= "<script type='text/javascript'>";
	
	$return .= "pandoraFlotPie('$graph_id', '$values', '$labels',
		'$series', '$width', $font_size, $water_mark, '$separator',
		'$legend_position', '$height', '$colors', " . json_encode($hide_labels) . ")";
	
	$return .= "</script>";
	
	return $return;
}

// Prints a FLOT pie chart
function flot_custom_pie_chart ($flash_charts, $graph_values,
		$width, $height, $colors, $module_name_list, $long_index,
		$no_data,$xaxisname, $yaxisname, $water_mark, $fontpath, $font_size,
		$unit, $ttl, $homeurl, $background_color, $legend_position) {
	
	global $config;
	///TODO
	include_javascript_dependencies_flot_graph();
	
	$total_modules = $graph_values['total_modules'];
	unset($graph_values['total_modules']);
	
	foreach ($graph_values as $label => $value) {
		if ($value['value']) {
			if ($value['value'] > 1000000)
				$legendvalue = sprintf("%sM", remove_right_zeros(number_format($value['value'] / 1000000, $config['graph_precision'])));
			else if ($value['value'] > 1000)
				$legendvalue = sprintf("%sK", remove_right_zeros(number_format($value['value'] / 1000, $config['graph_precision'])));
			else
				$legendvalue = remove_right_zeros(number_format($value['value'], $config['graph_precision']));
		}
		else
			$legendvalue = __('No data');
		$values[] = $value['value'];
		$legend[] = $label .": " . $legendvalue . " " .$value['unit'];
		$labels[] = $label;
	}
	
	$graph_id = uniqid('graph_');
	
	$return = "<div id='$graph_id' class='graph noresizevc' style='width: ".$width."px; height: ".$height."px;'></div>";
	
	if ($water_mark != '') {
		$return .= "<div id='watermark_$graph_id' style='display:none; position:absolute;'><img id='watermark_image_$graph_id' src='".$water_mark["url"]."'></div>";
		$water_mark = 'true';
	}
	else {
		$water_mark = 'false';
	}
	
	$separator = ';;::;;';
	
	$labels = implode($separator, $labels);
	$legend = implode($separator, $legend);
	$values = implode($separator, $values);
	if (!empty($colors)) {
		foreach ($colors as $color) {
			$temp_colors[] = $color['color'];
		}
	}
	$colors = implode($separator, $temp_colors);
	
	$return .= "<script type='text/javascript'>";
	
	$return .= "pandoraFlotPieCustom('$graph_id', '$values', '$labels',
			'$width', $font_size, '$fontpath', $water_mark,
			'$separator', '$legend_position', '$height', '$colors','$legend','$background_color')";
	
	$return .= "</script>";
	
	return $return;
}

// Returns a 3D column chart
function flot_hcolumn_chart ($graph_data, $width, $height, $water_mark, $font = '', $font_size = 7, $background_color = "white", $tick_color = "white", $val_min=null, $val_max=null) {
	global $config;
	
	include_javascript_dependencies_flot_graph();
	
	$return = '';
	
	$stacked_str = '';
	$multicolor = true;
	
	// Get a unique identifier to graph
	$graph_id = uniqid('graph_');
	$graph_id2 = uniqid('graph_');
	
	// Set some containers to legend, graph, timestamp tooltip, etc.
	$return .= "<div id='$graph_id' class='graph' style='width: ".$width."px; height: ".$height."px; padding-left: 20px;'></div>";
	$return .= "<div id='value_$graph_id' style='display:none; position:absolute; background:#fff; border: solid 1px #aaa; padding: 2px'></div>";
	
	if ($water_mark != '') {
		$return .= "<div id='watermark_$graph_id' style='display:none; position:absolute;'><img id='watermark_image_$graph_id' src='$water_mark'></div>";
		$watermark = 'true';
	}
	else {
		$watermark = 'false';
	}
	
	// Set a weird separator to serialize and unserialize passing data
	// from php to javascript
	$separator = ';;::;;';
	$separator2 = ':,:,,,:,:';
	
	// Transform data from our format to library format
	$labels = array();
	$a = array();
	$vars = array();
	
	$max = PHP_INT_MIN+1;
	$min = PHP_INT_MAX-1;
	$i = count($graph_data);
	$data = array();
	
	foreach ($graph_data as $label => $values) {
		$labels[] = io_safe_output($label);
		$i--;
		
		foreach ($values as $key => $value) {
			$jsvar = "data_" . $graph_id . "_" . $key;
			
			$data[$jsvar][] = $value;
			
			
			if ($value > $max) {
				$max = $value;
			}

			if ($value < $min) {
				$min = $value;
			}
		}
	}

	if (!is_numeric($val_min)) {
		$val_min = $min;
	}
	if (!is_numeric($val_max)) {
		$val_max = $max;
	}
	
	// Store serialized data to use it from javascript
	$labels = implode($separator,$labels);
	
	// Store data series in javascript format
	$jsvars = '';
	$jsseries = array();
	
	$i = 0;
	
	$values2 = array();
	
	foreach ($data as $jsvar => $values) {
		$values2[] = implode($separator,$values);
	}
	
	$values = implode($separator2, $values2);
	
	$jsseries = implode(',', $jsseries);
	
	
	// Javascript code
	$return .= "<script type='text/javascript'>";
	
	$return .= "pandoraFlotHBars('$graph_id', '$values', '$labels',
		false, $max, '$water_mark', '$separator', '$separator2', '$font', $font_size, '$background_color', '$tick_color', $val_min, $val_max)";

	$return .= "</script>";
	
	return $return;
}

// Returns a 3D column chart
function flot_vcolumn_chart ($graph_data, $width, $height, $color, $legend, $long_index, $homeurl, $unit, $water_mark, $homedir, $font, $font_size, $from_ux, $from_wux, $background_color = 'white', $tick_color = 'white') {
	global $config;
	
	include_javascript_dependencies_flot_graph();
	
	$stacked_str = '';
	$multicolor = false;
	
	// Get a unique identifier to graph
	$graph_id = uniqid('graph_');
	$graph_id2 = uniqid('graph_');

	if ($width != 'auto') {
		$width = $width . "px";
	}
	
	// Set some containers to legend, graph, timestamp tooltip, etc.
	$return .= "<div id='$graph_id' class='graph $adapt_key' style='width: ".$width."; height: ".$height."px; padding-left: 20px;'></div>";
	$return .= "<div id='value_$graph_id' style='display:none; position:absolute; background:#fff; border: solid 1px #aaa; padding: 2px'></div>";
	
	if ($water_mark != '') {
		$return .= "<div id='watermark_$graph_id' style='display:none; position:absolute;'><img id='watermark_image_$graph_id' src='$water_mark'></div>";
		$watermark = 'true';
	}
	else {
		$watermark = 'false';
	}
	
	$colors = array_map(function ($elem) {
		return $elem['color'] ? $elem['color'] : null;
	}, $color);
	
	// Set a weird separator to serialize and unserialize passing data from php to javascript
	$separator = ';;::;;';
	$separator2 = ':,:,,,:,:';
	
	// Transform data from our format to library format
	$labels = array();
	$a = array();
	$vars = array();
	
	$max = 0;
	$i = count($graph_data);
	foreach ($graph_data as $label => $values) {
		$labels[] = $label;
		$i--;
		
		foreach ($values as $key => $value) {
			$jsvar = "data_" . $graph_id . "_" . $key;
			
			$data[$jsvar][] = $value;
			
			
			if ($value > $max) {
				$max = $value;
			}
		}
	}
	
	// Store serialized data to use it from javascript
	$labels = implode($separator,$labels);
	$colors  = implode($separator, $colors);

	// Store data series in javascript format
	$jsvars = '';
	$jsseries = array();
	
	$i = 0;
	
	$values2 = array();
	
	foreach ($data as $jsvar => $values) {
		$values2[] = implode($separator,$values);
	}
	
	$values = implode($separator2, $values2);
	
	$jsseries = implode(',', $jsseries);
	
	// Javascript code
	$return .= "<script type='text/javascript'>";

	if ($from_ux) {
		if($from_wux){
			$return .= "pandoraFlotVBars('$graph_id', '$values', '$labels', '$labels', '$legend', '$colors', false, $max, '$water_mark', '$separator', '$separator2','$font',$font_size, true, true, '$background_color', '$tick_color')";
		}
		else{
			$return .= "pandoraFlotVBars('$graph_id', '$values', '$labels', '$labels', '$legend', '$colors', false, $max, '$water_mark', '$separator', '$separator2','$font',$font_size, true, false, '$background_color', '$tick_color')";
		}
	}
	else {
		$return .= "pandoraFlotVBars('$graph_id', '$values', '$labels', '$labels', '$legend', '$colors', false, $max, '$water_mark', '$separator', '$separator2','$font',$font_size, false, false, '$background_color', '$tick_color')";
	}

	$return .= "</script>";
	
	return $return;
}

function flot_slicesbar_graph ($graph_data, $period, $width, $height, $legend, $colors, $fontpath, $round_corner, $homeurl, $watermark = '', $adapt_key = '', $stat_win = false, $id_agent = 0, $full_legend_date = array()) {
	global $config;
	
	include_javascript_dependencies_flot_graph();
		
	$stacked_str = 'stack: stack,';
	
	// Get a unique identifier to graph
	$graph_id = uniqid('graph_');
	
	// Set some containers to legend, graph, timestamp tooltip, etc.
	if ($stat_win) {
		$return = "<div id='$graph_id' class='noresizevc graph $adapt_key' style='width: ".$width."px; height: ".$height."px; display: inline-block;'></div>";
	}
	else {
		$return = "<div id='$graph_id' class='noresizevc graph $adapt_key' style='width: ".$width."px; height: ".$height."px;'></div>";
	}
	$return .= "<div id='value_$graph_id' style='display:none; position:absolute; background:#fff; border: solid 1px #aaa; padding: 2px'></div>";
	
	// Set a weird separator to serialize and unserialize passing data from php to javascript
	$separator = ';;::;;';
	$separator2 = ':,:,,,:,:';
	
	// Transform data from our format to library format
	$labels = array();
	$a = array();
	$vars = array();
	
	$datacolor = array();
	
	$max = 0;
	
	$i = count($graph_data);
	
	$intervaltick = $period / $i;
	
	$leg_max_length = 0;
	foreach ($legend as $l) {
		if (strlen($l) > $leg_max_length) {
			$leg_max_length = strlen($l);
		}
	}
	
	$fontsize = 7;
	
	$extra_height = 15;
	if (defined("METACONSOLE"))
		$extra_height = 20;
	
	$return .= "<div id='extra_$graph_id' style='font-size: ".$fontsize."pt; display:none; position:absolute; overflow: auto; height: ".$extra_height."px; background:#fff; padding: 2px 2px 2px 2px; border: solid #000 1px;'></div>";
	
	$maxticks = (int) ($width / ($fontsize * $leg_max_length));
	
	$i_aux = $i;
	while(1) {
		if ($i_aux <= $maxticks ) {
			break;
		}
		
		$intervaltick*= 2;
		
		$i_aux /= 2;
	}
	
	$intervaltick = (int) $intervaltick;
	$acumulate = 0;
	$c = 0;
	$acumulate_data = array();
	foreach ($graph_data as $label => $values) {
		$labels[] = $label;
		$i--;
		
		foreach ($values as $key => $value) {
			$jsvar = "d_".$graph_id."_".$i;
			if ($key == 'data') {
				$datacolor[$jsvar] = $colors[$value];
				continue;
			}
			$data[$jsvar][] = $value;
			
			$acumulate_data[$c] = $acumulate;
			$acumulate += $value;
			$c++;
			
			if ($value > $max) {
				$max = $value;
			}
		}
	}
	
	// Store serialized data to use it from javascript
	$labels = implode($separator,$labels);
	$datacolor = implode($separator,$datacolor);
	$legend = io_safe_output(implode($separator,$legend));
	if (!empty($full_legend_date)) {
		$full_legend_date = io_safe_output(implode($separator,$full_legend_date));
	}
	else {
		$full_legend_date = false;
	}
	$acumulate_data = io_safe_output(implode($separator,$acumulate_data));
	
	// Store data series in javascript format
	$jsvars = '';
	$jsseries = array();
	
	$date = get_system_time ();
	$datelimit = ($date - $period) * 1000;
	
	$i = 0;
	
	$values2 = array();
	
	foreach ($data as $jsvar => $values) {
		$values2[] = implode($separator,$values);
		$i ++;
	}
	
	$values = implode($separator2, $values2);
	
	// Javascript code
	$return .= "<script type='text/javascript'>";
	$return .= "//<![CDATA[\n";
	$return .= "pandoraFlotSlicebar('$graph_id', '$values', '$datacolor', '$labels', '$legend', '$acumulate_data', $intervaltick, false, $max, '$separator', '$separator2', '', $id_agent, '$full_legend_date')";
	$return .= "\n//]]>";
	$return .= "</script>";
	
	return $return;
}
?>
