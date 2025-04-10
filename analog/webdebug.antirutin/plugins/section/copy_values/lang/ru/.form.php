<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'SOURCE'] = 'Откуда взять значение';
	$MESS[$strHint.'SOURCE'] = 'Выберите поле/свойство из которого будет взято значение.';
$MESS[$strLang.'TARGET'] = 'Куда сохранить';
	$MESS[$strHint.'TARGET'] = 'Выберите поле/свойство в которое будет сохранено значение.';
	
$MESS[$strLang.'SOURCE_TYPE'] = 'Какое значение копировать?';
	$MESS[$strHint.'SOURCE_TYPE'] = 'Выберите какое значение копировать.';
	
$MESS[$strLang.'SOURCE_FORMAT'] = 'Формат копируемого значения';
	$MESS[$strHint.'SOURCE_FORMAT'] = 'Выберите формат копируемого значения.';
	
$MESS[$strLang.'OPTIMAL_PRICE_SITE_ID'] = 'Сайт для расчета оптимальной цены';
	$MESS[$strHint.'OPTIMAL_PRICE_SITE_ID'] = 'Выберите сайт, в рамках которого будет пересчет цен (для каждого сайта могут быть заданы собственные скидки, которые влияют на конечные цены).<br/><br/>
<b>Используется только в случае учета скидок.</b>';
$MESS[$strLang.'OPTIMAL_PRICE_DELTA'] = 'Коррекция цены';
	$MESS[$strHint.'OPTIMAL_PRICE_DELTA'] = 'Выберите коррекцию скопированной цены, например, +100, или -25%, или 45%.';
	$MESS[$strLang.'OPTIMAL_PRICE_DELTA_PLACEHOLDER'] = 'Напр., +20%';

$MESS[$strLang.'TAGS_MULTIPLE'] = 'Копировать как множественное значение';
	$MESS[$strHint.'TAGS_MULTIPLE'] = 'Отметьте опцию, если нужно копировать теги не как строку "Тег 1, Тег 2, Тег 3", а как множественное значение:<br/>
<ul>
	<li>Тег 1,</li>
	<li>Тег 2,</li>
	<li>Тег 3</li>
</ul>
';

$MESS[$strLang.'SOURCE_TYPE_TITLE'] = 'Название';
$MESS[$strLang.'SOURCE_TYPE_CODE'] = 'Символьный код';
$MESS[$strLang.'SOURCE_TYPE_XML_ID'] = 'Внешний код';
$MESS[$strLang.'SOURCE_TYPE_ID'] = 'ID';
$MESS[$strLang.'SOURCE_TYPE_HIGHLOAD_ID'] = 'ID записи справочника';
$MESS[$strLang.'SOURCE_TYPE_FILE_ID'] = 'ID файла';
$MESS[$strLang.'SOURCE_TYPE_ENUM_ID'] = 'ID значения';
$MESS[$strLang.'SOURCE_TYPE_SRC_REL'] = 'Путь к файлу (относительный)';
$MESS[$strLang.'SOURCE_TYPE_SRC_ABS'] = 'Путь к файлу (абсолютный)';
$MESS[$strLang.'SOURCE_TYPE_SRC_URL'] = 'Ссылка на файл';
$MESS[$strLang.'SOURCE_TYPE_DESCRIPTION'] = 'Описание файла';
$MESS[$strLang.'SOURCE_TYPE_BOOLEAN_YES_NO'] = 'Да / нет';
$MESS[$strLang.'SOURCE_TYPE_BOOLEAN_Y_N'] = 'Y / N';
$MESS[$strLang.'SOURCE_TYPE_BOOLEAN_1_0'] = '1 / 0';
$MESS[$strLang.'SOURCE_TYPE_BOOLEAN_YES_NO_DEFAULT'] = 'Да / нет / по умолчанию';
$MESS[$strLang.'SOURCE_TYPE_BOOLEAN_Y_N_D'] = 'Y / N / D';
$MESS[$strLang.'SOURCE_TYPE_BOOLEAN_1_0__'] = '1 / 0 / пусто';
$MESS[$strLang.'SOURCE_TYPE_USER_DEFAULT'] = 'По умолчанию, например: [1] (admin) Иван';
$MESS[$strLang.'SOURCE_TYPE_USER_NAME_LASTNAME'] = 'Имя и фамилия';
$MESS[$strLang.'SOURCE_TYPE_USER_NAME'] = 'Имя';
$MESS[$strLang.'SOURCE_TYPE_USER_LOGIN'] = 'Логин';
$MESS[$strLang.'SOURCE_TYPE_USER_EMAIL'] = 'Email';
$MESS[$strLang.'SOURCE_TYPE_USER_ID'] = 'ID пользователя';
$MESS[$strLang.'SOURCE_TYPE_VAT_TEXT'] = 'Текст (напр., 20%)';
$MESS[$strLang.'SOURCE_TYPE_VAT_NUMBER'] = 'Число (напр., 20)';
$MESS[$strLang.'SOURCE_TYPE_VAT_ID'] = 'ID';
$MESS[$strLang.'SOURCE_TYPE_MEASURE_TITLE'] = 'Наименование';
$MESS[$strLang.'SOURCE_TYPE_MEASURE_SYMBOL_RUS'] = 'Обозначение (рус.)';
$MESS[$strLang.'SOURCE_TYPE_MEASURE_SYMBOL_INT'] = 'Обозначение (м/н.)';
$MESS[$strLang.'SOURCE_TYPE_MEASURE_CODE_LETTER'] = 'Обозначение (код)';
$MESS[$strLang.'SOURCE_TYPE_MEASURE_CODE'] = 'Числовой код';
$MESS[$strLang.'SOURCE_TYPE_MEASURE_ID'] = 'ID';
$MESS[$strLang.'SOURCE_TYPE_MONEY_DEFAULT'] = 'По умолчанию';
$MESS[$strLang.'SOURCE_TYPE_MONEY_FORMATTED'] = 'Форматированная цена';
$MESS[$strLang.'SOURCE_TYPE_MONEY_PRICE_CURRENCY'] = 'Цена|Валюта';
$MESS[$strLang.'SOURCE_TYPE_MONEY_NUMBER'] = 'Только число';
$MESS[$strLang.'SOURCE_TYPE_HTML_DEFAULT'] = 'По умолчанию';
$MESS[$strLang.'SOURCE_TYPE_HTML_TO_TEXT'] = 'Преобразовать в текст';
$MESS[$strLang.'SOURCE_TYPE_HTML_STRIP_TAGS'] = 'Вырезать теги';
	
$MESS[$strLang.'MULTIPLE_MODE'] = 'Режим копирования множественного в немножественное';
	$MESS[$strHint.'MULTIPLE_MODE'] = 'Выберите, как следует поступать при копировании множественного значения в немножественное.';
	
$MESS[$strLang.'MULTIPLE_MODE_COMMA'] = 'Перечислить через запятую';
$MESS[$strLang.'MULTIPLE_MODE_FIRST'] = 'Выбрать только первое';
$MESS[$strLang.'MULTIPLE_MODE_LAST'] = 'Выбрать только последнее';
$MESS[$strLang.'MULTIPLE_MODE_MIN'] = 'Выбрать наименьшее';
$MESS[$strLang.'MULTIPLE_MODE_MAX'] = 'Выбрать наибольшее';

$MESS[$strLang.'MULTIPLE_MODE_SAVE_CURRENT_VALUES'] = 'Добавить к текущим значениям';
	$MESS[$strHint.'MULTIPLE_MODE_SAVE_CURRENT_VALUES'] = 'Данная опция переключает режим работы заполнения значения на такой, когда выбранные здесь значения добавляются для каждого товара к уже имеющимся у него значениям.';

$MESS[$strLang.'MULTIPLE_MODE_REMOVE_DUPLICATES'] = 'Удалить дубликаты';
	$MESS[$strHint.'MULTIPLE_MODE_REMOVE_DUPLICATES'] = 'Отметьте опцию, если необходимо удалить дубликаты значений.';

$MESS[$strLang.'FILE_CONVERT_NAME'] = 'Конвертировать имя файла из имени раздела';
	$MESS[$strLang.'FILE_CONVERT_NAME_WARNING'] = '<b>Внимание!</b> Для работы опции «'.$MESS[$strLang.'FILE_CONVERT_NAME'].'» в <a href="/bitrix/admin/settings.php?lang='.LANGUAGE_ID.'&mid=main" target="_blank">настройках главного модуля</a> должна быть отмечена опция «Сохранять исходные имена загружаемых файлов».';
	$MESS[$strHint.'FILE_CONVERT_NAME'] = 'Отметьте опцию, если необходимо генерировать имя файла из имени текущего раздела.<br/><br/>'.$MESS[$strLang.'FILE_CONVERT_NAME_WARNING'];

$MESS[$strLang.'SITE_DOMAIN'] = 'Протокол и домен сайта';
	$MESS[$strHint.'SITE_DOMAIN'] = 'Укажите протокол домен сайта, например:<br/><code>https://site.ru</code>';
	$MESS[$strLang.'SITE_DOMAIN_PLACEHOLDER'] = 'https://site.ru';
	
$MESS[$strLang.'HTML_TYPE'] = 'Формат текста';
	$MESS[$strHint.'HTML_TYPE'] = 'Выберите формат текста';
	
$MESS[$strLang.'HTML_TYPE_AUTO'] = 'Авто';
$MESS[$strLang.'HTML_TYPE_HTML'] = 'HTML';
$MESS[$strLang.'HTML_TYPE_TEXT'] = 'Текст';

$MESS[$strLang.'SKIP_NULL_VALUES'] = 'Пропускать пустые значения';
	$MESS[$strHint.'SKIP_NULL_VALUES'] = 'Опция позволяет оставить в целевом поле/свойстве имеющееся значение, если исходное свойство пустое (проверка осуществляется по текстовому значению).';

$MESS[$strLang.'ERROR_NO_SOURCE'] = 'Пожалуйста, выберите поле которое будет скопировано.';
$MESS[$strLang.'ERROR_NO_TARGET'] = 'Пожалуйста, выберите поле в которое будет скопировано значение.';
?>