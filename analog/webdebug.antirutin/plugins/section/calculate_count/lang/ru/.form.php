<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'FIELD'] = 'Куда сохранить?';
	$MESS[$strHint.'FIELD'] = 'Выберите свойство раздела, в которое нужно сохранять расчитанное значение.';
$MESS[$strLang.'INCLUDE_SUBSECTIONS'] = 'Включая подразделы';
	$MESS[$strHint.'INCLUDE_SUBSECTIONS'] = 'Отметьте опцию, если при подсчете кол-ва товаров в разделе необходимо считать также кол-во товаров во всех его подразделах.';
$MESS[$strLang.'FILTER_ACTIVE'] = 'Учитывать активность товаров';
	$MESS[$strHint.'FILTER_ACTIVE'] = 'Отметьте опцию, если нужно считать только активные товары.';
$MESS[$strLang.'FILTER_ACTIVE_DATE'] = 'Учитывать дату активности товаров';
	$MESS[$strHint.'FILTER_ACTIVE_DATE'] = 'Отметьте опцию, если нужно считать только товары, активные по дате.';
$MESS[$strLang.'ADDITIONAL_FILTER'] = 'Дополнительный фильтр';
	$MESS[$strHint.'ADDITIONAL_FILTER'] = 'Отметьте галочку, если необходимо дополнить получаемый массив фильтра собственным массивом.';
$MESS[$strLang.'ADDITIONAL_FILTER_PHP'] = 'PHP-код дополнительного фильтра';
	$MESS[$strHint.'ADDITIONAL_FILTER_PHP'] = 'Здесь Вы можете указать PHP-код для получения дополнительного фильтра. Фильтрация осуществляется в соответствии с правилами для метода <a href="#" target="_blank">CIBlockElement::getList()</a>. Т.е. результатом кода должен быть массив, который будет объединен с массивом фильтра на основе параметров данной формы.<br/><br/>
	Варианты передачи массива фильтра:
	<ol>
		<li>код должен вернуть массив, например:<br/>
			<code>return [\'&gt;SORT\' =&gt; 500];</code></li>
		<li>код должен содержать массив $arrFilter:<br/>
			<code>$arrFilter = [\'&gt;SORT\' =&gt; 500];</code></li>
		<li>код должен содержать массив $arFilter:<br/>
			<code>$arFilter = [\'&gt;SORT\' =&gt; 500];</code></li>
	</ol>';
?>