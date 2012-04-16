<?php //netteCache[01]000349a:2:{s:4:"time";s:21:"0.24036600 1334524381";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:60:"C:\xampp\htdocs\Timak\app\templates\Institutes\default.latte";i:2;i:1334482612;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\xampp\htdocs\Timak\app\templates\Institutes\default.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, 'lcnudtzmfz')
;
// prolog NUIMacros
//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lb5eba64ee6d_breadcrumb')) { function _lb5eba64ee6d_breadcrumb($_l, $_args) { extract($_args)
?><li><a href="<?php echo htmlSpecialChars($_control->link("Homepage:default")) ?>">Domov</a></li>
<li>></li>
<li class="actual">Ústavy</li>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb4790a94c66_content')) { function _lb4790a94c66_content($_l, $_args) { extract($_args)
?><h2>Ústavy (<?php echo NTemplateHelpers::escapeHtml(isSet($faculty) ? $faculty->name : 'všetky', ENT_NOQUOTES) ?>)</h2>

<div class="overview">
	<a class="add" href="<?php echo htmlSpecialChars($_presenter->link("Institutes:add")) ?>">Pridať ústav</a>
	<table>
		<tr>
			<th>Názov</th>
			<th>Akronym</th>
			<th>Akcia</th>
		</tr>

<?php if (isset($faculty)): $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($faculty->related('institute')) as $institute): ?>
				<tr class="<?php echo htmlSpecialChars($iterator->odd ? 'odd' : 'even') ?>">
					<td class="name"><?php echo NTemplateHelpers::escapeHtml($institute->name, ENT_NOQUOTES) ?></td>
					<td><?php echo NTemplateHelpers::escapeHtml($institute->acronym, ENT_NOQUOTES) ?></td>
					<td class="actions">
						<div class="actions-container">
							<a class="more" href="<?php echo htmlSpecialChars($_presenter->link("Institutes:edit", array($institute->id, $faculty->id))) ?>"></a>
							<a class="delete" href="<?php echo htmlSpecialChars($_presenter->link("Institutes:delete", array($institute->id, $faculty->id))) ?>
" onclick="return confirm('Naozaj chcete zmazať <?php echo htmlSpecialChars(NTemplateHelpers::escapeJs($institute->name)) ?>?')"></a>
						</div>
					</td>
				</tr>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ;else: $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($all_institutes) as $institute): ?>
				<tr class="<?php echo htmlSpecialChars($iterator->odd ? 'odd' : 'even') ?>">
					<td class="name"><?php echo NTemplateHelpers::escapeHtml($institute->name, ENT_NOQUOTES) ?></td>
					<td><?php echo NTemplateHelpers::escapeHtml($institute->acronym, ENT_NOQUOTES) ?></td>
					<td class="actions">
						<div class="actions-container">
							<a class="more" href="<?php echo htmlSpecialChars($_presenter->link("Institutes:edit", array($institute->id))) ?>"></a>
							<a class="delete" href="<?php echo htmlSpecialChars($_presenter->link("Institutes:delete", array($institute->id))) ?>
" onclick="return confirm('Naozaj chcete zmazať <?php echo htmlSpecialChars(NTemplateHelpers::escapeJs($institute->name)) ?>?')"></a>
						</div>
					</td>
				</tr>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ;endif ?>
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