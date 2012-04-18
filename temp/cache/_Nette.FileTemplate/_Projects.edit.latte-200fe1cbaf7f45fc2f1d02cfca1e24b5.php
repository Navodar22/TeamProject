<?php //netteCache[01]000344a:2:{s:4:"time";s:21:"0.71584700 1334527814";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:55:"C:\xampp\htdocs\Timak\app\templates\Projects\edit.latte";i:2;i:1334482612;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\xampp\htdocs\Timak\app\templates\Projects\edit.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, '1mt56a9b2n')
;
// prolog NUIMacros
//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lbd470fa0dca_breadcrumb')) { function _lbd470fa0dca_breadcrumb($_l, $_args) { extract($_args)
?><li><a href="<?php echo htmlSpecialChars($_control->link("Homepage:default")) ?>">Domov</a></li>
<li>></li>
<li><a href="<?php echo htmlSpecialChars($_control->link("Projects:default")) ?>">Projekty</a></li>
<li>></li>
<li class="actual"><?php echo NTemplateHelpers::escapeHtml($project->name, ENT_NOQUOTES) ?></li>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb6cb7046b1e_content')) { function _lb6cb7046b1e_content($_l, $_args) { extract($_args)
?><h2>Úprava projektu</h2>

<div class="forms">	
<?php if (is_object($form)) $_ctrl = $form; else $_ctrl = $_control->getComponent($form); if ($_ctrl instanceof IRenderable) $_ctrl->validateControl(); $_ctrl->render('begin') ?>
	<fieldset>
		<table>
			<tr>
				<th><?php echo NTemplateHelpers::escapeHtml($form['name']->label, ENT_NOQUOTES) ?></th>
				<td><?php echo NTemplateHelpers::escapeHtml($form['name']->control, ENT_NOQUOTES) ?></td>
			</tr>
			<tr>
				<th><?php echo NTemplateHelpers::escapeHtml($form['description']->label, ENT_NOQUOTES) ?></th>
				<td><?php echo NTemplateHelpers::escapeHtml($form['description']->control, ENT_NOQUOTES) ?></td>
			</tr>
		</table>
	</fieldset>
	
	<table>
		<tr>
			<th></th>
			<td><?php echo NTemplateHelpers::escapeHtml($form['process']->control, ENT_NOQUOTES) ?>
 <?php echo NTemplateHelpers::escapeHtml($form['back']->control, ENT_NOQUOTES) ?>
 <?php echo NTemplateHelpers::escapeHtml($form['add_institute']->control, ENT_NOQUOTES) ?></td>
		</tr>
	</table>
</div>

<br /><br />
<div class="institutes">
<?php if ($project->related('project_institute')->count('*') > 0): ?>
		<h3>Ústavy zapojené do projektu</h3><br />
		<table style="text-align: center">
<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($participate_faculties) as $participate_faculty): ?>
				<tr>
					<th class="head" colspan="9"><?php echo NTemplateHelpers::escapeHtml($participate_faculty->name, ENT_NOQUOTES) ?></th>
				</tr>

				<tr style="background: gray; color: white">
					<th style="width: 50px">Ústav</th>
					<th style="width: 80px">Fin.zdroje</th>
					<th style="width: 80px">Spoluúčasť</th>
					<th style="width: 80px">Ľud.zdroje</th>
					<th style="width: 80px">Začiatok</th>
					<th style="width: 80px">Koniec</th>
					<th >Fondy</th>
					<th style="width: 100px">Stav</th>
					<th style="width: 80px">Akcie</th>			
				</tr>
<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($project->related('project_institute')->where('institute.faculty.id', $participate_faculty->id)) as $project_institute): ?>
				<tr class="<?php echo htmlSpecialChars($iterator->odd ? 'odd' : 'even') ?>">
					<td class="name" title="<?php echo htmlSpecialChars($project_institute->institute->name) ?>
"><?php echo NTemplateHelpers::escapeHtml($project_institute->institute->acronym, ENT_NOQUOTES) ?></td>
					<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($project_institute->cost), ENT_NOQUOTES) ?></td>
					<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($project_institute->participation), ENT_NOQUOTES) ?></td>
					<td><?php echo NTemplateHelpers::escapeHtml($template->emptyNumber($project_institute->hr), ENT_NOQUOTES) ?></td>
					<td><?php echo NTemplateHelpers::escapeHtml($template->emptyDate($project_institute->start, 'd.m.Y'), ENT_NOQUOTES) ?></td>
					<td><?php echo NTemplateHelpers::escapeHtml($template->emptyDate($project_institute->end, 'd.m.Y'), ENT_NOQUOTES) ?></td>
					<td><?php echo NTemplateHelpers::escapeHtml($project_institute->fonds, ENT_NOQUOTES) ?></td>
					<td><?php echo NTemplateHelpers::escapeHtml($project_institute->state->name, ENT_NOQUOTES) ?></td>
					<td class="actions">
						<div class="actions-container">
							<?php echo NTemplateHelpers::escapeHtml($form["edit_$project_institute->id"]->control, ENT_NOQUOTES) ?>

							<?php echo NTemplateHelpers::escapeHtml($form["delete_$project_institute->id"]->control, ENT_NOQUOTES) ?>

						</div>
					</td>
				</tr>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ;$iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
				
		<tr>
			<th class="head" colspan="9">Sumár nákladov projektu</th>
		</tr>		
				
		<tr class="total">
			<td class="name">Spolu</td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($total_values['total']['cost']), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($total_values['total']['participation']), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($total_values['total']['hr']), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyDate($total_values['total']['start'], 'd.m.Y'), ENT_NOQUOTES) ?></td>
			<td><?php echo NTemplateHelpers::escapeHtml($template->emptyDate($total_values['total']['end'], 'd.m.Y'), ENT_NOQUOTES) ?></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>

<?php if (isset($total_values['approved'])): ?>
			<tr class="total-approved">
				<td class="name">Schválené</td>
				<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($total_values['approved']['cost']), ENT_NOQUOTES) ?></td>
				<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($total_values['approved']['participation']), ENT_NOQUOTES) ?></td>
				<td><?php echo NTemplateHelpers::escapeHtml($template->emptyPrice($total_values['approved']['hr']), ENT_NOQUOTES) ?></td>
				<td><?php echo NTemplateHelpers::escapeHtml($template->emptyDate($total_values['approved']['start'], 'd.m.Y'), ENT_NOQUOTES) ?></td>
				<td><?php echo NTemplateHelpers::escapeHtml($template->emptyDate($total_values['approved']['end'], 'd.m.Y'), ENT_NOQUOTES) ?></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
<?php endif ?>

		</table>
<?php if (is_object($form)) $_ctrl = $form; else $_ctrl = $_control->getComponent($form); if ($_ctrl instanceof IRenderable) $_ctrl->validateControl(); $_ctrl->render('end') ?>
		<br />
<?php endif ?>
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