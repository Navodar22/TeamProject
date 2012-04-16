<?php //netteCache[01]000362a:2:{s:4:"time";s:21:"0.86309300 1334527863";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:73:"C:\xampp\htdocs\Timak\libs\Nette.addons\DataGrid\Templates\dataGrid.latte";i:2;i:1334482612;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\xampp\htdocs\Timak\libs\Nette.addons\DataGrid\Templates\dataGrid.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, '8wjldt9yyk')
;
// prolog NUIMacros

// snippets support
if (!empty($_control->snippetMode)) {
	return NUIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
$iterations = 0; foreach ($flashes as $flash): ?><div class="flash <?php echo htmlSpecialChars($flash->type) ?>
"><?php echo NTemplateHelpers::escapeHtml($flash->message, ENT_NOQUOTES) ?></div>
<?php $iterations++; endforeach ?>

<table class="component-grid">
<?php if (isset($empty)): ?>
	<thead>
		<tr>
			<td></td>
		</tr>
	</thead>
	<tr>
		<td>Aktuálne tu niesu žiadne projekty</td>
	</tr>
	</table>
<?php else: ?>
	<thead>
		<tr>
<?php $iterations = 0; foreach ($columns as $key => $column): ?>
			<th><a href="<?php echo htmlSpecialChars($_control->link("order", array($key))) ?>
"><?php echo NTemplateHelpers::escapeHtml($column->caption, ENT_NOQUOTES) ?></a></th>
<?php $iterations++; endforeach ?>
			
<?php if (isset($global_actions)): ?>
			<th class="grid-actions">
				<div class="grid-actions-envelope">
<?php $iterations = 0; foreach ($global_actions as $key => $global_action): if (isset($global_action['data'])): ?>
							<a href="<?php echo htmlSpecialChars($_presenter->link($global_action['redirect'], array_merge(array(), $global_action['data'], array()))) ?>
" class="grid-icon <?php echo htmlSpecialChars($key) ?>" title="<?php echo htmlSpecialChars($global_action['title']) ?>"></a>
<?php else: ?>
							<a href="<?php echo htmlSpecialChars($_presenter->link($global_action['redirect'])) ?>
" class="grid-icon <?php echo htmlSpecialChars($key) ?>" title="<?php echo htmlSpecialChars($global_action['title']) ?>"></a>
<?php endif ;$iterations++; endforeach ?>
					</div>
			</th>
<?php elseif (isset($actions)): ?>
			<th class="grid-actions">Actions</th>
<?php endif ?>
		</tr>
		
<?php if ($hasFilter): ?>
			<tr>		
			<?php echo NTemplateHelpers::escapeHtml($form->render('begin'), ENT_NOQUOTES) ?>

<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($columns) as $key => $column): ?>
				<th>
<?php if (isset($form[$key])): ?>
						<?php echo NTemplateHelpers::escapeHtml($form[$key]->control, ENT_NOQUOTES) ?>

<?php endif ?>

<?php if ($iterator->isLast()): if (isset($actions)): else: ?>
							<th class="grid-hidden"><?php echo NTemplateHelpers::escapeHtml($form['filter']->control, ENT_NOQUOTES) ?></a></th>
<?php endif ;endif ?>
				</th>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ;if (isset($actions)): ?>
				<th><?php echo NTemplateHelpers::escapeHtml($form['filter']->control, ENT_NOQUOTES) ?></a></th>
<?php endif ?>
			<?php echo NTemplateHelpers::escapeHtml($form->render('end'), ENT_NOQUOTES) ?>

			</tr>
<?php endif ?>
	</thead>
	
	<tbody>
<?php $iterations = 0; foreach ($iterator = $_l->its[] = new NSmartCachingIterator($data) as $row): ?>
			<tr class="<?php echo htmlSpecialChars($iterator->isEven() ? 'grid-even' : 'grid-odd') ?>">
<?php $iterations = 0; foreach ($row['columns'] as $key => $data): if ($data['kind'] == 'bool'): ?>
						<td class="<?php echo htmlSpecialChars($gridName) ?>-<?php echo htmlSpecialChars($key) ?>
"><div class="<?php echo htmlSpecialChars($data['value'] == 'Áno' ? 'grid-yes' : 'grid-no') ?>"></div></td>
<?php elseif ($data['kind'] == 'date'): ?>
						<td class="<?php echo htmlSpecialChars($gridName) ?>-<?php echo htmlSpecialChars($key) ?>
"><?php echo NTemplateHelpers::escapeHtml($template->date($data['value'], $data['date_format']), ENT_NOQUOTES) ?></td>
<?php else: ?>
						<td class="<?php echo htmlSpecialChars($gridName) ?>-<?php echo htmlSpecialChars($key) ?>
"><?php echo NTemplateHelpers::escapeHtml($data['value'], ENT_NOQUOTES) ?></td>
<?php endif ;$iterations++; endforeach ;if (isset($actions)): ?>
					<td class="grid-row-actions">
						<div class="grid-actions-envelope">
<?php $iterations = 0; foreach ($row['actions'] as $key => $action_data): if (isset($action_data['params'])): ?>
									<a href="<?php echo htmlSpecialChars($_presenter->link($action_data['redirect'], array_merge(array(), $action_data['params'], array()))) ?>
" class="grid-icon <?php echo htmlSpecialChars($key) ?>" title="<?php echo htmlSpecialChars($action_data['title']) ?>"></a>
<?php else: ?>
									<a href="<?php echo htmlSpecialChars($_presenter->link($action_data['redirect'])) ?>
" class="grid-icon <?php echo htmlSpecialChars($key) ?>" title="<?php echo htmlSpecialChars($action_data['title']) ?>"></a>
<?php endif ;$iterations++; endforeach ?>
							<div class="grid-clear"></div>
						</div>
					</td>
<?php endif ?>
			</tr>
<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>
	</tbody>
		
	<tfoot>
		<tr>
<?php $columns_count = isset($actions) ? count($columns) + 1 : count($columns) ?>
			<td colspan="<?php echo htmlSpecialChars($columns_count) ?>">			
				<div class="grid-reset"><a href="<?php echo htmlSpecialChars($_control->link("reset")) ?>">Reset</a></div>		

				<div class="grid-paginator">
					<?php echo NTemplateHelpers::escapeHtml($pagingForm->render('begin'), ENT_NOQUOTES) ?>


<?php if (!$paginator->isFirst()): ?>
						<a class="grid-p-left-first" href="<?php echo htmlSpecialChars($_control->link("page", array($paginator->firstPage))) ?>"></a>
						<a class="grid-p-left" href="<?php echo htmlSpecialChars($_control->link("page", array($paginator->page - 1))) ?>"></a>				
<?php else: ?>
						<span class="grid-p-left-first"></span>
						<span class="grid-p-left"></span>
<?php endif ?>

						Stránka <?php echo NTemplateHelpers::escapeHtml($pagingForm['page']->control, ENT_NOQUOTES) ?>
 z <?php echo NTemplateHelpers::escapeHtml($paginator->lastPage, ENT_NOQUOTES) ?>


<?php if (!$paginator->isLast()): ?>
						<a class="grid-p-right-last" href="<?php echo htmlSpecialChars($_control->link("page", array($paginator->lastPage))) ?>"></a>
						<a class="grid-p-right" href="<?php echo htmlSpecialChars($_control->link("page", array($paginator->page + 1))) ?>"></a>
<?php else: ?>
						<span class="grid-p-right-last"></span>
						<span class="grid-p-right"></span>
<?php endif ?>
					<?php echo NTemplateHelpers::escapeHtml($pagingForm->render('end'), ENT_NOQUOTES) ?>

				</div>			

				<div class="grid-itemsPerPage">
					<?php echo NTemplateHelpers::escapeHtml($dropdownForm->render('begin'), ENT_NOQUOTES) ?>

						<?php echo NTemplateHelpers::escapeHtml($dropdownForm['itemsPerPage']->control, ENT_NOQUOTES) ?>

					<?php echo NTemplateHelpers::escapeHtml($dropdownForm->render('end'), ENT_NOQUOTES) ?>

				</div>

<?php $paginator_to = $paginator->offset + $paginator->itemsPerPage > $paginator->itemCount ? $paginator->itemCount : $paginator->offset + $paginator->itemsPerPage ?>
				<div class="grid-items"> Projekty <?php echo NTemplateHelpers::escapeHtml($paginator->offset + 1, ENT_NOQUOTES) ?>
 - <?php echo NTemplateHelpers::escapeHtml($paginator_to, ENT_NOQUOTES) ?> z <?php echo NTemplateHelpers::escapeHtml($paginator->itemCount, ENT_NOQUOTES) ?></div>	
				<div class="grid-clear"></div>
			</td>
		</tr>
	</tfoot>
</table>



<style>
<?php $iterations = 0; foreach ($columns as $key => $column): if (!empty($column->style)): ?>
			.component-grid .<?php echo NTemplateHelpers::escapeCss($gridName) ?>-<?php echo NTemplateHelpers::escapeCss($key) ?>
 { <?php echo $column->style ?> }
<?php endif ;$iterations++; endforeach ?>
</style>



<script>
	$(function() {
		$( ".filter-date").datepicker({
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true
		});
		$( ".filter-date").datepicker( "option", "dateFormat", 'yy-mm-dd' );
	});
</script>
<?php endif ;