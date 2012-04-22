<?php //netteCache[01]000341a:2:{s:4:"time";s:21:"0.77465400 1334918165";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:52:"C:\wamp\www\pok\app\templates\Settings\faculty.latte";i:2;i:1334918079;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\wamp\www\pok\app\templates\Settings\faculty.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, '9zzdr4h6ut')
;
// prolog NUIMacros
//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lbbde957c400_breadcrumb')) { function _lbbde957c400_breadcrumb($_l, $_args) { extract($_args)
?><li><a href="<?php echo htmlSpecialChars($_control->link("Settings:default")) ?>">Nastavenia</a></li>
<li>></li>
<li class="actual"><?php echo NTemplateHelpers::escapeHtml($faculty['name'], ENT_NOQUOTES) ?></li>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb38b3ef56dd_content')) { function _lb38b3ef56dd_content($_l, $_args) { extract($_args)
?>
<h2><?php echo NTemplateHelpers::escapeHtml($faculty['name'], ENT_NOQUOTES) ?></h2>

<div class="overview">
	<table>
		<tr style="background: gray; color: white">
                        <th>Ústav</th>
			<th>Finančné zdroje</th>
			<th>Počet študentov</th>
			<th>Akcia</th>
		</tr>

<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($institutes) as $institute_id => $institute): ?>
		<tr class="<?php echo htmlSpecialChars($iterator->odd ? 'odd' : 'even') ?>">
			<td class="name" title="<?php echo htmlSpecialChars($institute['name']) ?>"><?php echo NTemplateHelpers::escapeHtml($institute['acronym'], ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($institute->money), ENT_NOQUOTES) ?></td>
                        <td><?php echo NTemplateHelpers::escapeHtml($institute->students, ENT_NOQUOTES) ?></td>
			<td class="actions">
				<div class="actions-container">
					<a class="more" href="<?php echo htmlSpecialChars($_presenter->link('Settings:editInstitute', array($institute_id))) ?>">
				</a>
				<div class="clearer"></div>
			</td>
		</tr>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>


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