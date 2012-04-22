<?php //netteCache[01]000343a:2:{s:4:"time";s:21:"0.60837000 1335116100";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:54:"C:\wamp\www\pok\app\templates\Homepage\institute.latte";i:2;i:1335115776;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\wamp\www\pok\app\templates\Homepage\institute.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, '8wsfp6v6h0')
;
// prolog NUIMacros
//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lb3939b00cae_breadcrumb')) { function _lb3939b00cae_breadcrumb($_l, $_args) { extract($_args)
?><li><a href="<?php echo htmlSpecialChars($_control->link("Homepage:default")) ?>">Domov</a></li>
<li>></li>
<li><a href="<?php echo htmlSpecialChars($_control->link("Homepage:faculty", array($faculty->id))) ?>
"><?php echo NTemplateHelpers::escapeHtml($faculty->name, ENT_NOQUOTES) ?></a></li>
<li>></li>
<li class="actual"><?php echo NTemplateHelpers::escapeHtml($institute->name, ENT_NOQUOTES) ?></li>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb7c01750bdd_content')) { function _lb7c01750bdd_content($_l, $_args) { extract($_args)
?><h2><?php echo NTemplateHelpers::escapeHtml($institute->name, ENT_NOQUOTES) ?></h2>

<div class="overview">
<?php $_ctrl = $_control->getComponent("dataGrid"); if ($_ctrl instanceof IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
</div>


<br /><br />
<div class="overview">
	<h3>Súhrnné zdroje ústavu</h3>
	<table>
		<tr>
			<th>Počet projektov</th>
			<th>Fin. zdroje</th>
			<th>Schválené fin.zdroje</th>
			<th>Spoluúčasť</th>
			<th>Schválená spoluúčasť</th>
      <th>Voľné fin.zdroje</th>		
			<th>Ludské zdroje</th>
			<th>Schválené ludské zdroje</th>	
					</tr>
		
		<tr class="total">
			<td><?php echo NTemplateHelpers::escapeHtml($institute->project_count, ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($institute->total_cost), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($institute->approved_cost), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($institute->total_participation), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($institute->approved_participation), ENT_NOQUOTES) ?></td>
      <td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($institute->free_money), ENT_NOQUOTES) ?></td>		
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyNumber($institute->total_hr), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyNumber($institute->approved_hr), ENT_NOQUOTES) ?></td>	
					</tr>
	</table>	
</div><?php
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