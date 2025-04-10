<?
namespace WD\Antirutin;

use \WD\Antirutin\Helper;

Helper::loadMessages(__FILE__);
$strLang = 'WDA_OPTIONS_';
$strHint = $strLang.'HINT_';

return [
	'NAME' => Helper::getMessage($strLang.'OPTION_MISC'),
	'OPTIONS' => [
		'disable_old_module' => [
			'NAME' => Helper::getMessage($strLang.'OPTION_DISABLE_OLD_MODULE'),
			'HINT' => Helper::getMessage($strHint.'OPTION_DISABLE_OLD_MODULE'),
			'TYPE' => 'checkbox',
		],
		'show_carefully_notice' => [
			'NAME' => Helper::getMessage($strLang.'OPTION_SHOW_CAREFULLY_NOTICE'),
			'HINT' => Helper::getMessage($strHint.'OPTION_SHOW_CAREFULLY_NOTICE'),
			'TYPE' => 'checkbox',
		],
		'pin_plugins_to_end' => [
			'NAME' => Helper::getMessage($strLang.'OPTION_PIN_PLUGINS_TO_END'),
			'HINT' => Helper::getMessage($strHint.'OPTION_PIN_PLUGINS_TO_END'),
			'TYPE' => 'checkbox',
		],
		'default_logic_is_and' => [
			'NAME' => Helper::getMessage($strLang.'DEFAULT_LOGIC_IS_AND'),
			'HINT' => Helper::getMessage($strHint.'DEFAULT_LOGIC_IS_AND'),
			'TYPE' => 'checkbox',
		],
		'default_iblock_id' => [
			'NAME' => Helper::getMessage($strLang.'DEFAULT_IBLOCK_ID'),
			'HINT' => Helper::getMessage($strHint.'DEFAULT_IBLOCK_ID'),
			'TYPE' => 'custom',
			'CALLBACK_MAIN' => function($obOptions, $arOption)use($strLang){
				?>
				<div class="wda-form" data-role="options_iblock_id_parent">
					<select name="<?=$arOption['CODE'];?>" id="wda_options_iblock_id">
						<option value=""><?=Helper::getMessage($strLang.'DEFAULT_IBLOCK_ID_EMPTY');?></option>
						<?foreach(Helper::getIBlocks() as $IBlockTypeCode => $arIBlockType):?>
							<?if(is_array($arIBlockType['ITEMS'])&&!empty($arIBlockType['ITEMS'])):?>
								<optgroup label="<?=$arIBlockType['NAME'];?>">
									<?foreach($arIBlockType['ITEMS'] as $arItem):?>
										<option value="<?=$arItem['ID'];?>"
											<?if($arOption['VALUE'] == $arItem['ID']):?> selected="selected"<?endif?>
											><?=$arItem['NAME'];?> 
											[<?=$arItem['ID'];?><?=(strlen($arItem['CODE']) ? ', '.$arItem['CODE'] : '');?>]</option>
									<?endforeach?>
								</optgroup>
							<?endif?>
						<?endforeach?>
					</select>
				</div>
				<script>
					wdaSelect2($('#wda_options_iblock_id'), {
						dropdownParent: $('div[data-role="options_iblock_id_parent"]')
					});
				</script>
				<?
			},
		],
		'section_name_chain' => [
			'NAME' => Helper::getMessage($strLang.'SECTION_NAME_CHAIN'),
			'HINT' => Helper::getMessage($strHint.'SECTION_NAME_CHAIN'),
			'TYPE' => 'checkbox',
		],
		'section_hide_margins' => [
			'NAME' => Helper::getMessage($strLang.'SECTION_HIDE_MARGINS'),
			'HINT' => Helper::getMessage($strHint.'SECTION_HIDE_MARGINS'),
			'TYPE' => 'checkbox',
		],
	],
];
?>