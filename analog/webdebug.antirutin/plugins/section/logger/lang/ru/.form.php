<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'FORMAT'] = 'Формат файла';
	$MESS[$strHint.'FORMAT'] = 'Укажите, в каком формате должен создаваться файл.';
	$MESS[$strLang.'FORMAT_CSV'] = 'Значения через разделитель (*.csv)';
	$MESS[$strLang.'FORMAT_TXT'] = 'Текстовый формат (*.txt)';
$MESS[$strLang.'FILENAME'] = 'Имя файла';
	$MESS[$strHint.'FILENAME'] = 'Укажите, в какой файл (относительно корня сайта) должны записаться данные. Например: <code>/myfile.txt</code>.';
$MESS[$strLang.'ENCODING'] = 'Кодировка файла';
	$MESS[$strHint.'ENCODING'] = 'Укажите, в какой кодировке должен быть создан файл.';
$MESS[$strLang.'SITE_DOMAIN'] = 'Протокол и домен сайта';
	$MESS[$strHint.'SITE_DOMAIN'] = 'Укажите здесь протокол и домен сайта (например, <code>https://www.site.ru</code>).<br/><br/>
Если это поле заполнено, то в файл будут логироваться полные ссылки. Без указания ссылки выгружаются относительными (напр., /catalog/section1.html или /upload/iblock/abc/abc123.jpg).';
	$MESS[$strLang.'SITE_DOMAIN_PLACEHOLDER'] = 'Напр., https://www.site.ru';
$MESS[$strLang.'FIELDS'] = 'Колонки CSV для сохранения в файл';
	$MESS[$strHint.'FIELDS'] = 'Выберите колонки, которые будут в CSV-файле.<br/><br/>Доступен выбор сразу нескольких колонок в списке.';
	$MESS[$strLang.'FILTER_PLACEHOLDER'] = 'Фильтр';
	$MESS[$strLang.'FIELDS_ALL'] = 'Все доступные поля';
	$MESS[$strLang.'MOVE_RIGHT'] = '&gt;';
	$MESS[$strLang.'FIELDS_SELECTED'] = 'Выбранные поля';
	$MESS[$strLang.'MOVE_UP'] = 'Выше';
	$MESS[$strLang.'MOVE_DOWN'] = 'Ниже';
	$MESS[$strLang.'DELETE'] = 'Удалить';
$MESS[$strLang.'FOR_EXCEL'] = 'Оптимизировать для MS Excel';
	$MESS[$strHint.'FOR_EXCEL'] = 'Выберите, нужно ли провести оптимизацию CSV-файла для более правильного отображения в MS Excel.';

?>