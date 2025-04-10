<?
namespace WD\Antirutin\Plugins\Element;
class CopyValuesCurrencyLang extends \CCurrencyLang {
	public static function isAllowUseHideZero():bool{
		return true;
	}
	public static function CurrencyFormat($price, $currency, $useTemplate = true){
			static $eventExists = null;

			$useTemplate = !!$useTemplate;
			if ($useTemplate)
			{
					if ($eventExists === true || $eventExists === null)
					{
							foreach (GetModuleEvents('currency', 'CurrencyFormat', true) as $arEvent)
							{
									$eventExists = true;
									$result = ExecuteModuleEventEx($arEvent, array($price, $currency));
									if ($result != '')
											return $result;
							}
							if ($eventExists === null)
									$eventExists = false;
					}
			}

			if (!isset($price) || $price === '')
					return '';

			$currency = \Bitrix\Currency\CurrencyManager::checkCurrencyID($currency);
			if ($currency === false)
					return '';

			$price = (float)$price;
			$arCurFormat = (isset(self::$arCurrencyFormat[$currency]) ? self::$arCurrencyFormat[$currency] : self::GetFormatDescription($currency));
			$intDecimals = $arCurFormat['DECIMALS'];
			if (self::isAllowUseHideZero() && $arCurFormat['HIDE_ZERO'] == 'Y')
			{
					if (round($price, $arCurFormat["DECIMALS"]) == round($price, 0))
							$intDecimals = 0;
			}
			$price = number_format($price, $intDecimals, $arCurFormat['DEC_POINT'], $arCurFormat['THOUSANDS_SEP']);

			return (
					$useTemplate
					? self::applyTemplate($price, $arCurFormat['FORMAT_STRING'])
					: $price
			);
	}
	public static function applyTemplate($value, $template):string{
			return preg_replace('/(^|[^&])#/', '${1}'.$value, $template);
	}
}
?>