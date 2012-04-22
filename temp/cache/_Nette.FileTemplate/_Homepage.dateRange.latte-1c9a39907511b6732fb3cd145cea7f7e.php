<?php //netteCache[01]000343a:2:{s:4:"time";s:21:"0.22416600 1335024317";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:9:"checkFile";}i:1;s:54:"C:\wamp\www\pok\app\templates\Homepage\dateRange.latte";i:2;i:1334918078;}i:1;a:3:{i:0;a:2:{i:0;s:6:"NCache";i:1;s:10:"checkConst";}i:1;s:20:"NFramework::REVISION";i:2;s:30:"013c8ee released on 2012-02-03";}}}?><?php

// source file: C:\wamp\www\pok\app\templates\Homepage\dateRange.latte

?><?php
// prolog NCoreMacros
list($_l, $_g) = NCoreMacros::initRuntime($template, 'fwr50io9da')
;
// prolog NUIMacros
//
// block breadcrumb
//
if (!function_exists($_l->blocks['breadcrumb'][] = '_lbf02fe7d2cd_breadcrumb')) { function _lbf02fe7d2cd_breadcrumb($_l, $_args) { extract($_args)
?><li class="actual">Domov</li>
<?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb349c87d5b4_content')) { function _lb349c87d5b4_content($_l, $_args) { extract($_args)
?>
<h2>Zobrazované obdobie</h2>

<div class="forms">
<?php $_ctrl = $_control->getComponent("dateRangeForm"); if ($_ctrl instanceof IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
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