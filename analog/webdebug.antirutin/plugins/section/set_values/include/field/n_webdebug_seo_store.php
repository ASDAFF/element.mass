<?
namespace WD\Antirutin;
$bAllowMultiply = false;
$arProperty = &$arField['DATA'];

$arUserField = $GLOBALS['USER_FIELD_MANAGER']->getUserFields($arProperty['ENTITY_ID'], null, LANGUAGE_ID)[$arProperty['FIELD_NAME']];
$arUserField['VALUE'] = $arProperty['MULTIPLE'] == 'Y' ? $arValues : $mValue;
$strHtml = $GLOBALS['USER_FIELD_MANAGER']->getEditFormHTML(false, $GLOBALS[$arProperty['FIELD_NAME']], $arUserField);
$strHtml = preg_replace('#<td[^>]*class="adm-detail-valign-top"[^>]*>.*?</td>#s', '', $strHtml);
$strHtml = preg_replace('#<select\s#i', '<select data-no-style="Y" ', $strHtml);
$strHtml = preg_replace('#name="UF_\w+(\[\])?"#', 'name="'.$this->getInputName('value').'$1"', $strHtml);
print '<table><tbody>'.$strHtml.'</tbody></table>';
