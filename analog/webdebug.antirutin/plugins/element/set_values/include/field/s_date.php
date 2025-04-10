<?
namespace WD\Antirutin;

$strId = Helper::randString(true);

?>
<input type="text" name="<?=$this->getInputName('value', $bMultiple);?>" value="<?=$mValue;?>" size="15" 
	id="<?=$strId;?>" />
<?=Helper::calendar($strId, $bTime ? true : false);?>
&nbsp;
