<?php //netteCache[01]000341a:2:{s:4:"time";s:21:"0.46277500 1335115344";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:52:"C:\wamp\www\pok\app\templates\Homepage\faculty.latte";i:2;i:1335115338;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\wamp\www\pok\app\templates\Homepage\faculty.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, '84jlwqf7c1')
;
// prolog NUIMacros
//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lb7b0decb695_breadcrumb')) { function _lb7b0decb695_breadcrumb($_l, $_args) { extract($_args)
?><li><a href="<?php echo htmlSpecialChars($_control->link("Homepage:default")) ?>">Domov</a></li>
<li>></li>
<li class="actual"><?php echo NTemplateHelpers::escapeHtml($faculty['name'], ENT_NOQUOTES) ?></li>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lbecf9bda4f0_content')) { function _lbecf9bda4f0_content($_l, $_args) { extract($_args)
?>
<h2><?php echo NTemplateHelpers::escapeHtml($faculty['name'], ENT_NOQUOTES) ?></h2>

<div class="overview">
	<table>
		<tr style="background: gray; color: white">
			<th>Ústav</th>
			<th>Počet projektov</th>
			<th>Fin.zdroje</th>
			<th>Schválené fin.zdroje</th>
			<th>Voľné fin.zdroje</th>
			<th>Ludské zdroje</th>
			<th>Schválené ludské zdroje</th>	
			<th>Akcie</th>
		</tr>

<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($institutes) as $institute_id => $institute): ?>
		<tr class="<?php echo htmlSpecialChars($iterator->odd ? 'odd' : 'even') ?>">
			<td class="name" title="<?php echo htmlSpecialChars($institute['name']) ?>"><?php echo NTemplateHelpers::escapeHtml($institute['acronym'], ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($institute->project_count, ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($institute->total_cost), ENT_NOQUOTES) ?></td>
			<td class="approved"><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($institute->approved_cost), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($institute->free_money), ENT_NOQUOTES) ?></td>	
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyNumber($institute->total_hr), ENT_NOQUOTES) ?></td>
			<td class="approved"><?php echo NTemplateHelpers::escapeHtml($template->emptyNumber($institute->approved_hr), ENT_NOQUOTES) ?></td>
			<td class="actions">
				<div class="actions-container">
					<a class="more" href="<?php echo htmlSpecialChars($_presenter->link('Homepage:institute', array($institute_id))) ?>">
				</a>
				<div class="clearer"></div>
			</td>
		</tr>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
		
		<tr class="total">
			<td>Spolu</td>
			<td><?php echo NTemplateHelpers::escapeHtml($faculty->project_count, ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($faculty->total_cost), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($faculty->approved_cost), ENT_NOQUOTES) ?></td>
		  <td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($faculty->free_money), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyNumber($faculty->total_hr), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyNumber($faculty->approved_hr), ENT_NOQUOTES) ?></td>		
			<td></td>
		</tr>

	</table>
</div>
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

<?php call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()) ; 