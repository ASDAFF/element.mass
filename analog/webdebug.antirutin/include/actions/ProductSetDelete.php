<?
class CWDA_ProductSetDelete extends CWDA_Plugin {
	CONST GROUP = 'CATALOG';
	CONST CODE = 'PRODUCT_SET_DELEET';
	CONST NAME = 'Удаление наборов/комплектов';
	//
	static function GetDescription() {
		$Descr = 'Плагин позволяет удалить наборы/комплекты у товара.';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'SET_TYPE' => 'Набор или комплект:',
			'SET_TYPE_0' => 'наборы и комплекты',
			'SET_TYPE_2' => 'только наборы',
			'SET_TYPE_1' => 'только комплекты',
		);
		$MESS = trim($MESS[$Code]);
		if ($ConvertCharset && !CWDA::IsUtf()) {
			$MESS = CWDA::ConvertCharset($MESS);
		}
		return $MESS;
	}
	//
	static function AddHeadData() {
		?>
		<style>
		#wda_product_set_type input[type=radio] {margin:0 0 0 5px; vertical-align:middle;}
		#wda_product_set_type span {vertical-align:middle;}
		</style>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<div id="wda_settings_<?=self::CODE?>">
			<div id="wda_product_set_type">
				<?=self::GetMessage('SET_TYPE');?>
				<label><input type="radio" name="params[set_type]" value="0" checked="checked" /> <span><?=self::GetMessage('SET_TYPE_0');?></span></label>
				<label><input type="radio" name="params[set_type]" value="2" /> <span><?=self::GetMessage('SET_TYPE_2');?></span></label>
				<label><input type="radio" name="params[set_type]" value="1" /> <span><?=self::GetMessage('SET_TYPE_1');?></span></label>
			</div>
		</div>
		<?
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		if(CModule::IncludeModule('catalog') && class_exists('CCatalogProductSet')) {
			$intType = $Params['set_type']==1 ? CCatalogProductSet::TYPE_SET : CCatalogProductSet::TYPE_GROUP;
			if($Params['set_type']==0 || $Params['set_type']==CCatalogProductSet::TYPE_SET) {
				CCatalogProductSetAll::deleteAllSetsByProduct($ElementID, CCatalogProductSet::TYPE_SET);
			}
			if($Params['set_type']==0 || $Params['set_type']==CCatalogProductSet::TYPE_GROUP) {
				CCatalogProductSetAll::deleteAllSetsByProduct($ElementID, CCatalogProductSet::TYPE_GROUP);
			}
			return true;
		}
		return $bResult;
	}
}
?>