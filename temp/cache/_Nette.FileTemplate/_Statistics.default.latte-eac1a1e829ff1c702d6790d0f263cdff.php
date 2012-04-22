<?php //netteCache[01]000343a:2:{s:4:"time";s:21:"0.94653400 1334918144";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:54:"C:\wamp\www\pok\app\templates\Statistics\default.latte";i:2;i:1334918079;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\wamp\www\pok\app\templates\Statistics\default.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, '2vvnr1gfjc')
;
// prolog NUIMacros
//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lb3bde3181cf_breadcrumb')) { function _lb3bde3181cf_breadcrumb($_l, $_args) { extract($_args)
?><li><a href="<?php echo htmlSpecialChars($_control->link("Homepage:default")) ?>">Domov</a></li>
<li>></li>
<li class="actual">Štatistiky</li>
<?php
}}

//
// block head
//
if (!function_exists($_l->blocks['head'][] = '_lb181170bca8_head')) { function _lb181170bca8_head($_l, $_args) { extract($_args)
?><!--Load the AJAX API-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

  // Load the Visualization API and the piechart package.
  google.load('visualization', '1.0', {'packages':['corechart']});  
  
  function drawChart(element_id, title, chartData) {

	// Create the data table.
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Ústav');
	data.addColumn('number', 'Pridelené financie');
	data.addRows(chartData);

	// Set chart options
	var options = { 'width': 400,
				    'height': 300,
					'is3D': true,
					'backgroundColor':'transparent',
					'colors': [
						'#305abc',	
						'#c3000e',	
						'#32c300',	
						'#6f24cc',	
						'#e07432',	
						'#8cd108',	
						'#b810cc',
						'#397d99',
						'#de5909',
						'#920156',
						'#530192',
						'#014592',
						'#459201',
						'#cf39d4',
						'#3982d4',
						'#39d485',
						'#b45e3c',
						'#3c74b4',
						'#6c3cb4',
						'#b43c6f',
						'#b43c3c',
						'#91b43c',
						'#e885a3',
						'#e8c185',
						'#8985e8',
					],
					'legend': { 
						'position': 'none'
					},
					'tooltip': {
						'showColorCode': true
					},
					'chartArea': {
						'top': 20
					}
				};

	// Instantiate and draw our chart, passing in some options.
	var chart = new google.visualization.PieChart(document.getElementById(element_id));
	chart.draw(data, options);
  }
</script>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb528ececfa8_content')) { function _lb528ececfa8_content($_l, $_args) { extract($_args)
?><h2>Štatistiky</h2>
<a class="design" href="<?php echo htmlSpecialChars($_control->link("Statistics:default")) ?>">Štatistiky</a>
<a class="design" href="<?php echo htmlSpecialChars($_control->link("Statistics:facultystat")) ?>">Štatistiky projektov</a>
<script>
	chartData_faculties_students = [];
<?php $iterations = 0; foreach ($school as $faculty_values): ?>
		chartData_faculties_students.push([<?php echo NTemplateHelpers::escapeJs($faculty_values['acronym']) ?>
, <?php echo NTemplateHelpers::escapeJs($faculty_values['money']) ?>]);
<?php $iterations++; endforeach ?>
		
	// Set a callback to run when the Google Visualization API is loaded.	
	google.setOnLoadCallback(function() { drawChart('chart_div_faculties_students', 'Fakulty students', chartData_faculties_students) });
</script>

<h3>Fakulty</h3>
<div class="statistics-pie" id="chart_div_faculties_students"></div>

<div class="legend">
	<div class="statistics">
		<table class="statistics-table">
			<tr>
				<th style="width:100px">Fakulta</th> 
				<th style="width:130px">Finančné prostriedky</th>
				<th style="width:120px">Počet študentov</th>
			</tr>
<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($school) as $faculty_values): ?>
				<tr>
					<td style="text-align: left; padding-left: 10px">
						<div style="width: 15px; height: 15px; background: <?php echo $colors[$iterator->counter-1] ?>; float: left; margin-right: 10px"></div>
						<?php echo NTemplateHelpers::escapeHtml($faculty_values['acronym'], ENT_NOQUOTES) ?>

					</td> 
					<td><?php echo NTemplateHelpers::escapeHtml($faculty_values['money'], ENT_NOQUOTES) ?> €</td>
					<td><?php echo NTemplateHelpers::escapeHtml($faculty_values['students'], ENT_NOQUOTES) ?></td>
				</tr>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
		</table>	
	</div>	
</div>
<div class="clearer"></div>


<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($faculties) as $faculty): ?>
<script>
	chartData_<?php echo NTemplateHelpers::escapeJs($faculty->id) ?> = [];
<?php $iterations = 0; foreach ($faculty->related('institute')->order('students DESC') as $institute): ?>
		chartData_<?php echo NTemplateHelpers::escapeJs($faculty->id) ?>.push([<?php echo NTemplateHelpers::escapeJs($institute->acronym) ?>
, <?php echo NTemplateHelpers::escapeJs($institute->money) ?>]);
<?php $iterations++; endforeach ?>
		
	// Set a callback to run when the Google Visualization API is loaded.	
	google.setOnLoadCallback(function() { drawChart('chart_div_<?php echo NTemplateHelpers::escapeJs($faculty->id) ?>
', <?php echo NTemplateHelpers::escapeJs($faculty->name) ?>, chartData_<?php echo NTemplateHelpers::escapeJs($faculty->id) ?>) });
</script>

<div>
	<h3><?php echo NTemplateHelpers::escapeHtml($faculty->name, ENT_NOQUOTES) ?></h3>
	<div style="float: left" id="chart_div_<?php echo htmlSpecialChars($faculty->id) ?>"></div>

	<div class="legend">
		<div class="statistics">
			<table class="statistics-table">
				<tr>
					<th style="width:100px">Ústav</th> 
					<th style="width:130px">Finančné prostriedky</th>
					<th style="width:120px">Počet študentov</th>
				</tr>
<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($faculty->related('institute')->order('students DESC')) as $institute): ?>
					<tr>
						<td style="text-align: left; padding-left: 10px" title="<?php echo htmlSpecialChars($institute->name) ?>">
							<div style="width: 15px; height: 15px; background: <?php echo $colors[$iterator->counter-1] ?>; float: left; margin-right: 10px"></div>
							<a href="<?php echo htmlSpecialChars($_presenter->link("Institutes:edit", array($institute->id, NULL, $backlink))) ?>
"><?php echo NTemplateHelpers::escapeHtml($institute->acronym, ENT_NOQUOTES) ?></a>
						</td> 
						<td><?php echo NTemplateHelpers::escapeHtml($institute->money, ENT_NOQUOTES) ?> €</td>
						<td><?php echo NTemplateHelpers::escapeHtml($institute->students, ENT_NOQUOTES) ?></td>
					</tr>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
			</table>
		</div>	
	</div>
	<div class="clearer"></div>
</div>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>

<?php
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = empty($template->_extended) && isset($_control) && $_control instanceof NPresenter ? $_control->findLayoutTemplateFile() : NULL; $template->_extended = $_extended = TRUE;


if ($_l->extends) {
	ob_start();

} elseif (!empty($_control->snippetMode)) {
	return NUIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
if ($_l->extends) { ob_end_clean(); return NCoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['breadcrumb']), $_l, get_defined_vars())  ?>

<?php call_user_func(reset($_l->blocks['head']), $_l, get_defined_vars())  ?>
	
<?php call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()) ; 