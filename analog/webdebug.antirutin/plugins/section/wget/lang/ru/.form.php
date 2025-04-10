<?
\WD\Antirutin\Helper::getPluginLangPrefix(__FILE__, $strLang, $strHint);
$strHomepage = (\WD\Antirutin\Helper::isHttps() ? 'https://' : 'http://').\WD\Antirutin\Helper::getCurrentDomain();

$MESS[$strLang.'SITE_HOMEPAGE_CURRENT'] = $strHomepage;

$MESS[$strLang.'SITE_HOMEPAGE'] = 'Главная страница сайта (<a href="#" class="wda-inline-link" onclick="$(this).closest(\'[data-plugin-form]\').find(\'[data-role=wget_site_homepage]\').val(\''.$strHomepage.'\'); return false;">'.$strHomepage.'</a>)';
	$MESS[$strHint.'SITE_HOMEPAGE'] = 'Укажите здесь адрес главной страницы сайта - например, '.$strHomepage;

$MESS[$strLang.'EMPTY_SITE_HOMEPAGE'] = 'Пожалуйста, укажите адрес главной страницы сайта.';

?>