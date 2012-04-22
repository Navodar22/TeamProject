<?php //netteCache[01]000332a:2:{s:4:"time";s:21:"0.45339500 1334918131";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:43:"C:\wamp\www\pok\app\templates\@layout.latte";i:2;i:1334918078;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\wamp\www\pok\app\templates\@layout.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, 'xfl71ddg01')
;
// prolog NUIMacros
//
// block head
//
if (!function_exists($_l->blocks['head'][] = '_lba838abbab3_head')) { function _lba838abbab3_head($_l, $_args) { extract($_args)
;
}}

//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lbb2a6dd7c8a_breadcrumb')) { function _lbb2a6dd7c8a_breadcrumb($_l, $_args) { extract($_args)
;
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
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<meta name="description" content="Nette Framework web application skeleton" />
<?php if (isset($robots)): ?>	<meta name="robots" content="<?php echo htmlSpecialChars($robots) ?>" />
<?php endif ?>

	<title><?php echo NTemplateHelpers::escapeHtml($title, ENT_NOQUOTES) ?></title>

	<link rel="stylesheet" media="screen,projection,tv" href="<?php echo htmlSpecialChars($basePath) ?>/css/screen.css" type="text/css" />
	<link rel="stylesheet" media="screen,projection,tv" href="<?php echo htmlSpecialChars($basePath) ?>/css/jquery-ui-1.8.18.custom.css" type="text/css" />
	<link rel="stylesheet" media="print" href="<?php echo htmlSpecialChars($basePath) ?>/css/print.css" type="text/css" />
	<link rel="shortcut icon" href="<?php echo htmlSpecialChars($basePath) ?>/favicon.ico" type="image/x-icon" />

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo htmlSpecialChars($basePath) ?>/js/netteForms.js"></script>
	<script type="text/javascript" src="<?php echo htmlSpecialChars($basePath) ?>/js/jquery-ui-1.8.18.custom.min.js"></script>
	<?php if ($_l->extends) { ob_end_clean(); return NCoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['head']), $_l, get_defined_vars())  ?>

</head>

<body>	
	<div class="envelope">
	  <div class="header">
		<div class="user-info">
			<a href="http://www.stuba.sk" class="feilogo"></a>
			<a class="logout" href="<?php echo htmlSpecialChars($_control->link("sign:out")) ?>" title="Odhlásiť"></a>
			<div class="f_right"><span class="bold">Prihlásený: </span><span class="c-green bold"><?php echo NTemplateHelpers::escapeHtml($user->name, ENT_NOQUOTES) ?>
 | <?php echo NTemplateHelpers::escapeHtml($user->role, ENT_NOQUOTES) ?> </span></div>
<?php $iterations = 0; foreach ($flashes as $flash): ?>			<div class="flash <?php echo htmlSpecialChars($flash->type) ?>
"><?php echo NTemplateHelpers::escapeHtml($flash->message, ENT_NOQUOTES) ?></div>
<?php $iterations++; endforeach ?>
			<div class="clearer"></div>
		</div>
		<div class="header-content">
<?php if ($user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3]): ?>
                        <a href="<?php echo htmlSpecialChars($_presenter->link("Homepage:default")) ?>">Úvod</a>
<?php endif ?>
			<a href="<?php echo htmlSpecialChars($_presenter->link("Projects:myProjects")) ?>">Moje projekty</a>
			<a href="<?php echo htmlSpecialChars($_presenter->link("Projects:add")) ?>">Pridať projekt</a>
<?php if ($user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3]): ?>
			<a href="<?php echo htmlSpecialChars($_presenter->link("Faculties:default")) ?>">Fakulty</a>
<?php endif ;if ($user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3]): ?>
			<a href="<?php echo htmlSpecialChars($_presenter->link("Institutes:default")) ?>">Ústavy</a>
<?php endif ;if ($user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3]): ?>
			<a href="<?php echo htmlSpecialChars($_presenter->link("Statistics:default")) ?>">Štatistiky</a>
<?php endif ;if ($user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3]): ?>
                        <a href="<?php echo htmlSpecialChars($_presenter->link("Settings:default")) ?>">Nastavenia</a>
<?php endif ?>
			
<?php if ($user->privileges[0] | $user->privileges[1] | $user->privileges[2] | $user->privileges[3]): ?>			<div class="date-range">
				<a href="<?php echo htmlSpecialChars($_control->link("Homepage:dateRange")) ?>">Zobrazované obdobie: </a>
<?php if (!empty($dateRange)): ?>
					<span class="c-green"><?php echo NTemplateHelpers::escapeHtml($template->date($dateRange->from, 'd.m.y'), ENT_NOQUOTES) ?>
 - <?php echo NTemplateHelpers::escapeHtml($template->date($dateRange->to, 'd.m.y'), ENT_NOQUOTES) ?></span>
<?php else: ?>
					<span class="c-green">Všetko</span>
<?php endif ?>
			</div>
<?php endif ?>
			<div class="clearer"></div>
		</div><!-- header-content -->
        
	
	</div><!-- header -->

	<div  class="content">
		<ul  class="breadcrumb">
			<?php call_user_func(reset($_l->blocks['breadcrumb']), $_l, get_defined_vars())  ?>

		</ul>
                
<?php NUIMacros::callBlock($_l, 'content', $template->getParameters()) ?>
	</div> <!-- content -->
	
	<div class="footer">
		Created by FEI STU
	</div>
		
    </div> <!-- envelope -->
	
	
	<script>
	$(function() {
		$(".datepicker").datepicker({
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true
		});
		$( ".datepicker").datepicker( "option", "dateFormat", 'dd.mm.yy' );
<?php if (isset($project_dates)): ?>
			$('.datepicker[name=start]').datepicker("setDate", <?php echo NTemplateHelpers::escapeJs($project_dates['start']) ?> );
			$('.datepicker[name=end]').datepicker("setDate", <?php echo NTemplateHelpers::escapeJs($project_dates['end']) ?> );
<?php else: ?>
			
<?php endif ?>
			
<?php if (isset($date_range_data)): ?>
			$('.datepicker[name=from]').datepicker("setDate", <?php echo NTemplateHelpers::escapeJs($date_range_data['from']) ?> );
			$('.datepicker[name=to]').datepicker("setDate", <?php echo NTemplateHelpers::escapeJs($date_range_data['to']) ?> );
<?php else: ?>
			$('.datepicker[name=to]').datepicker("setDate", 'm.d.y');
<?php endif ?>
		$(".flash").delay(3000).fadeOut(2000);
		

		$(".design").button();

	});
	</script>
</body>
</html>

