<?php //netteCache[01]000347a:2:{s:4:"time";s:21:"0.32386400 1334506333";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:58:"C:\xampp\htdocs\Timak\app\templates\Settings\default.latte";i:2;i:1334506331;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\xampp\htdocs\Timak\app\templates\Settings\default.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, '4zx0035ak6')
;
// prolog NUIMacros
//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lb9c18b9c85c_breadcrumb')) { function _lb9c18b9c85c_breadcrumb($_l, $_args) { extract($_args)
?><li><a href="<?php echo htmlSpecialChars($_control->link("Homepage:default")) ?>">Domov</a></li>
<li>></li>
<li class="actual"><a href="<?php echo htmlSpecialChars($_control->link("Settings:default")) ?>">Nastavenia</a></li>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lba741f48b1e_content')) { function _lba741f48b1e_content($_l, $_args) { extract($_args)
?><h2>Nastavenia</h2>

<div class="overview">
	<table>
		<tr>
			<th>Financie</th>
			<th>Študenti</th>
			<th>Akcia</th>
		</tr>

<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($schools) as $school): ?>
		<tr class="<?php echo htmlSpecialChars($iterator->odd ? 'odd' : 'even') ?>">
			<td><?php echo NTemplateHelpers::escapeHtml($school->money, ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($school->students, ENT_NOQUOTES) ?></td>
			<td class="actions">
				<div class="actions-container">
                                    <a class="more" href="<?php echo htmlSpecialChars($_presenter->link("Settings:edit", array($school->id))) ?>"></a>
				</div>
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