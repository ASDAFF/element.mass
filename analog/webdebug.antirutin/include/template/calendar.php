<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$strClass = 'wda-dateicon';
if($arParams['WITH_TIME']){
	$strClass .= ' wda-dateicon-time';
}

?>
<span class="<?=$strClass;?>" onclick="BX.calendar({node:this, field:'<?=$arParams['INPUT_ID'];?>', form:'', 
	bTime:<?=($arParams['WITH_TIME']?'true':'false');?>, currentTime:'<?=time();?>', 
	bHideTime:<?=($arParams['WITH_TIME']?'false':'true');?>});"></span>
