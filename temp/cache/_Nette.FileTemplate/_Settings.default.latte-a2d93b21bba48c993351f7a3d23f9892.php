<?php //netteCache[01]000341a:2:{s:4:"time";s:21:"0.90633200 1335041124";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:52:"C:\wamp\www\pok\app\templates\Settings\default.latte";i:2;i:1335040392;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\wamp\www\pok\app\templates\Settings\default.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, 'xsma84jleq')
;
// prolog NUIMacros
//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lb3238584a04_breadcrumb')) { function _lb3238584a04_breadcrumb($_l, $_args) { extract($_args)
?><li><a href="<?php echo htmlSpecialChars($_control->link("Homepage:default")) ?>">Domov</a></li>
<li>></li>
<li class="actual"><a href="<?php echo htmlSpecialChars($_control->link("Settings:default")) ?>">Nastavenia</a></li>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb1fa56407dd_content')) { function _lb1fa56407dd_content($_l, $_args) { extract($_args)
;if ($user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3]): ?><h2 >Nastavenia</h2>
<?php endif ?>

<div class="overview">
	<table>
		<tr>
			<th>Finančné zdroje</th>
			<th>Počet študentov</th>
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
<br />
<br />

<?php if ($user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3]): ?><div  class="overview">
	<table>
		<tr>
			<th>Fakulta</th>
			<th>Finančné zdroje</th>
			<th>Počet študentov</th>
			<th>Akcie</th>
		</tr>

<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($school_data) as $faculty_id => $school_faculty): ?>
		<tr class="<?php echo htmlSpecialChars($iterator->odd ? 'odd' : 'even') ?>">
			<td class="name" title="<?php echo htmlSpecialChars($school_faculty['name']) ?>
"><?php echo NTemplateHelpers::escapeHtml($school_faculty['acronym'], ENT_NOQUOTES) ?></td>
                        <td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($school_faculty->total_money), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($school_faculty->total_students, ENT_NOQUOTES) ?></td>
			
			<td class="actions">
				<div class="actions-container">
					<a class="more" href="<?php echo htmlSpecialChars($_presenter->link("Settings:faculty", array($faculty_id))) ?>"></a>
					<div class="clearer"></div>
				</div>
			</td>
		</tr>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>


	</table>
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