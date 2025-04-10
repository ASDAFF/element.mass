<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

$strModuleId = 'webdebug.antirutin';
if(\Bitrix\Main\Loader::includeModule($strModuleId) && class_exists('\WD\Antirutin\Helper')){
	if($APPLICATION->getGroupRight($strModuleId) >= 'R') {
		Helper::loadMessages(__FILE__);
		$strDefaultIBlockId = '';
		if($intDefaultIBlockId = Helper::getOption('default_iblock_id')){
			$strDefaultIBlockId = sprintf('iblock_id=%d&', $intDefaultIBlockId);
		}
		$arMenu = [
			'parent_menu' => 'global_menu_content',
			'section' => 'webdebug_antirutin',
			'sort' => 990,
			'text' => Helper::getMessage('WD_ANTIRUTIN_MAIN'),
			'icon' => 'wda_icon_main',
			'items_id' => 'wd_antirutin',
			'items' => [
				[
					'text' => Helper::getMessage('WD_ANTIRUTIN_NEW_ELEMENT'),
					'url' => '/bitrix/admin/wda_new.php?'.$strDefaultIBlockId.'lang='.LANGUAGE_ID,
					'more_url' => ['/bitrix/admin/wda_new.php'],
				],
				[
					'text' => Helper::getMessage('WD_ANTIRUTIN_OLD'),
					'url' => '/bitrix/admin/wda.php?lang='.LANGUAGE_ID,
					'more_url' => ['/bitrix/admin/wda_profiles.php?lang='.LANGUAGE_ID],
				],
			],
		];
		if(Helper::getOption('disable_old_module') == 'Y'){
			$arNewModuleMenu = reset($arMenu['items']);
			$arMenu['url'] = $arNewModuleMenu['url'];
			$arMenu['more_url'] = $arNewModuleMenu['more_url'];
			unset($arMenu['items']);
		}
		return $arMenu;
	}
}

?>