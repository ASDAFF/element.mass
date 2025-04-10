<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);

$MESS[$strLang.'CODE'] = 'PHP-код';
	$MESS[$strHint.'CODE'] = 'Здесь должен быть PHP-код, обрабатывающий элементы. Доступна переменная <code>$intElementId</code>, ID инфоблока можно получить из <code>$this->intIBlockId</code>. Данные товара можно получить (не считая штатных средств Битрикса) так:<br/>
	<pre>$arSelect = [<br/>
	"FIELDS" => ["NAME", "CODE"], # OR: "FIELDS" => true,<br/>
	"PROPERTY_ID" => [123, 124, 125], # OR: "PROPERTY_ID" => true,<br/>
	"EMPTY_PROPERTIES" => true,<br/>
	"CATALOG" => true,<br/>
	"PRICES" => [1, 2, 3],<br/>
	"STORES" => true,<br/>
	"SEO" => true,<br/>
];<br/>
$arElement = IBlock::getElementArray($intElementId, $this->intIBlockId, $arSelect);</pre>
Отладочный вывод:<br/>
<pre>$this->debug("Hello world!")</pre>
Код должен вернуть true в случае успешного завершения.';
?>