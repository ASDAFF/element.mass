<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper;

$strMessage1 = 'Работа данного поля модулем не поддерживается по причине отсутствия нормальной возможности встраивания в форму молуля.';
$strMessage2 = '
Во-первых, данное свойство зависит от категории, которая должна быть выбрана на текущей же странице в свойстве, которое ожидает Avito, т.е. это рассчитано только на работу на странице редактирования товара.<br/>
Во-вторых, данное свойство собирает итоговый HTML через JavaScript, и повлиять на это (а это необходимо) без изменения самого скрипта Avito - нет возможности.
';
if(!Helper::isUtf()){
	$strMessage1 = Helper::convertCharset($strMessage1, 'UTF-8', 'CP1251');
	$strMessage2 = Helper::convertCharset($strMessage2, 'UTF-8', 'CP1251');
}
print Helper::showError($strMessage1, $strMessage2, true);