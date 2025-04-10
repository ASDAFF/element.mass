<?
namespace WD\Antirutin;

$intColCount = 20;

$mLat = $this->get('lat', $intValueKey);
$mLong = $this->get('long', $intValueKey);

?>
<input type="text" name="<?=$this->getInputName('value', $bMultiple);?>" value="<?=$mValue;?>" size="<?=$intColCount;?>"
	style="visibility:hidden; position:absolute; z-index:-1; opacity:0;" />
<input type="text" name="<?=$this->getInputName('lat', $bMultiple);?>" value="<?=$mLat;?>" size="<?=$intColCount;?>"
	placeholder="<?=static::getMessage('PLACEHOLDER_LAT');?>" />
<input type="text" name="<?=$this->getInputName('long', $bMultiple);?>" value="<?=$mLong;?>" size="<?=$intColCount;?>"
	placeholder="<?=static::getMessage('PLACEHOLDER_LONG');?>" />
