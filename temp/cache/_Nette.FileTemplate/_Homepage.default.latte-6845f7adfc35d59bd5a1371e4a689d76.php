<?php //netteCache[01]000347a:2:{s:4:"time";s:21:"0.92573100 1334528158";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:58:"C:\xampp\htdocs\Timak\app\templates\Homepage\default.latte";i:2;i:1334527998;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\xampp\htdocs\Timak\app\templates\Homepage\default.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, 'tvm10czwnw')
;
// prolog NUIMacros
//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lbc3d0873ff0_breadcrumb')) { function _lbc3d0873ff0_breadcrumb($_l, $_args) { extract($_args)
?><li class="actual">Domov</li>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb82ffcbcd75_content')) { function _lb82ffcbcd75_content($_l, $_args) { extract($_args)
;if ($user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3]): ?><h2 >Rozpočtový plán STU / celkový prehľad</h2>
<?php endif ?>

<?php if ($user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3]): ?><div  class="overview">
	<table>
		<tr>
			<th>Fakulta</th>
			<th>Počet projektov</th>
			<th>Fin.zdroje</th>
			<th>Schválené fin.zdroje</th>
			<th>Ludské zdroje</th>
			<th>Schválené ludské zdroje</th>
			<th>Akcie</th>			
		</tr>

<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($school_data) as $faculty_id => $school_faculty): ?>
		<tr class="<?php echo htmlSpecialChars($iterator->odd ? 'odd' : 'even') ?>">
			<td class="name" title="<?php echo htmlSpecialChars($school_faculty['name']) ?>
"><?php echo NTemplateHelpers::escapeHtml($school_faculty['acronym'], ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($school_faculty->project_count, ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($school_faculty->total_cost), ENT_NOQUOTES) ?></td>
			<td class="approved"><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($school_faculty->approved_cost), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyNumber($school_faculty->total_hr), ENT_NOQUOTES) ?></td>
			<td class="approved"><?php echo NTemplateHelpers::escapeHtml($template->emptyNumber($school_faculty->approved_hr), ENT_NOQUOTES) ?></td>
			<td class="actions">
				<div class="actions-container">
					<a class="more" href="<?php echo htmlSpecialChars($_presenter->link("Homepage:faculty", array($faculty_id))) ?>"></a>
					<div class="clearer"></div>
				</div>
			</td>
		</tr>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>

		<tr class="total">
			<td>Spolu</td>
			<td><?php echo NTemplateHelpers::escapeHtml($total_data->project_count, ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($total_data->total_cost), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($total_data->approved_cost), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyNumber($total_data->total_hr), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyNumber($total_data->approved_hr), ENT_NOQUOTES) ?></td>		
			<td></td>
		</tr>
		
	</table>
</div>	
<?php endif ?>
	
<?php if ($user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3]): ?><div class="bottom_menu">
	<a class="design" href="<?php echo htmlSpecialChars($_control->link("Homepage:dateRange")) ?>">Zmeniť zobrazené obdobie</a>
	<a class="design" href="<?php echo htmlSpecialChars($_control->link("Projects:default")) ?>">Zobraziť všetky projekty</a>
</div>
<?php endif ;
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

<?php call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()) ; 