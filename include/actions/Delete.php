<?
class CWDA_Delete extends CWDA_Plugin {
	CONST GROUP = 'GENERAL';
	CONST CODE = 'DELETE';
	CONST NAME = 'Удаление элементов';
	//
	static function GetDescription() {
		$Descr = 'Плагин для удаления элементов инфоблока.';
        $cwda = new CWDA;
		if (!$cwda->IsUtf()) {
			$Descr = $cwda->ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function AddHeadData() {
		//
	}
	static function ShowSettings($IBlockID=false) {
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
        $cwda = new CWDA;
		if(CIBlockElement::Delete($ElementID)) {
            $cwda->Log('Delete element #'.$ElementID, self::CODE);
			$bResult = true;
		}
		return $bResult;
	}
}
?>