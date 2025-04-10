<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

if(!isset($arParams)){
	#// If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
	# So, we make hack in Helper::includeFile(): $GLOBALS['arParams'] = $arParams;
	global $arParams;
}

$arParams['PREFIX'] = is_string($arParams['PREFIX']) ? $arParams['PREFIX'] : '';

?>
<?if(is_array($arParams['PHRASES'])):?>
	<script>
	BX.message({
		<?foreach($arParams['PHRASES'] as $strPhrase):?>
			<?if(is_array($strPhrase)):?>
				<?=$strPhrase[0];?>: '<?=$strPhrase[1];?>',
			<?else:?>
				<?=$arParams['PREFIX'];?><?=$strPhrase;?>: '<?=Helper::getMessage($arParams['PREFIX'].$strPhrase);?>',
			<?endif?>
		<?endforeach?>
		WDA_JS_LANG_PHRASE_END: ''
	});
	</script>
<?endif?>
