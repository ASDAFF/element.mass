<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'NAME'] = 'Заполнение значений';
$MESS[$strLang.'HINT'] = 'Заполнение значений для полей, свойств, цен, остатков и др.';

$MESS[$strLang.'FIELD_TYPE_NOT_SUPPORTED'] = 'Тип поля <b>#FIELD_TYPE#</b> на данный момент не поддерживается. Вероятно, это свойство, созданное сторонним модулем. Пожалуйста, <a href="mailto:info@nulled.com?subject=Антирутин: новый тип поля &quot;#FIELD_TYPE#&quot;" class="wda-inline-link">свяжитесь с нами</a> по вопросу добавления поддержки данного типа поля.';
$MESS[$strLang.'SELECT_FIELD_PREASE'] = 'Пожалуйста, выберите поле.';


$MESS[$strLang.'LOG_FIELD_UNKNOWN'] = 'Поле #FIELD# недоступно для обработки.';
$MESS[$strLang.'LOG_PROPERTY_NOT_FOUND'] = 'Свойство #PROPERTY_ID# в инфоблоке #IBLOCK_ID# не найдено.';

$MESS[$strLang.'DEMO_CODE'] = '$this->set(\'description\', \'Описание свойства\');'.PHP_EOL.'return \'Значение\';';

$MESS[$strLang.'ERROR_NO_FIELD'] = 'Пожалуйста, выберите поле/свойство из списка.';
$MESS[$strLang.'ERROR_FIELD_NOT_FOUND'] = 'Поле/свойство не найдено.';
?>