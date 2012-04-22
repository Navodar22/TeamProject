<?php //netteCache[01]000342a:2:{s:4:"time";s:21:"0.95910400 1334918141";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:53:"C:\wamp\www\pok\app\templates\Faculties\default.latte";i:2;i:1334918078;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\wamp\www\pok\app\templates\Faculties\default.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, 'csqgiz7dgm')
;
// prolog NUIMacros
//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lbd7e3bc69da_breadcrumb')) { function _lbd7e3bc69da_breadcrumb($_l, $_args) { extract($_args)
?><li><a href="<?php echo htmlSpecialChars($_control->link("Homepage:default")) ?>">Domov</a></li>
<li>></li>
<li class="actual"><a href="<?php echo htmlSpecialChars($_control->link("Faculties:default")) ?>">Fakulty</a></li>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lbabbd9b233c_content')) { function _lbabbd9b233c_content($_l, $_args) { extract($_args)
?><h2>Fakulty</h2>

<div class="overview">
	<a class="add" href="<?php echo htmlSpecialChars($_presenter->link("Faculties:add")) ?>">Pridať fakultu</a>
	<table>
		<tr>
			<th>Názov</th>
			<th>Akronym</th>
			<th>Akcia</th>
		</tr>

<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($faculties) as $faculty): ?>
		<tr class="<?php echo htmlSpecialChars($iterator->odd ? 'odd' : 'even') ?>">
			<td class="name"><a href="<?php echo htmlSpecialChars($_presenter->link("Institutes:default", array($faculty->id))) ?>
"><?php echo NTemplateHelpers::escapeHtml($faculty->name, ENT_NOQUOTES) ?></a></td>
			<td><?php echo NTemplateHelpers::escapeHtml($faculty->acronym, ENT_NOQUOTES) ?></td>
			<td class="actions">
				<div class="actions-container">
					<a class="more" href="<?php echo htmlSpecialChars($_presenter->link("Faculties:edit", array($faculty->id))) ?>"></a>
					<a class="delete" href="<?php echo htmlSpecialChars($_presenter->link("Faculties:delete", array($faculty->id))) ?>
" onclick="return confirm('Naozaj chcete zmazať <?php echo htmlSpecialChars(NTemplateHelpers::escapeJs($faculty->name)) ?>?')"></a>
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