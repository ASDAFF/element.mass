<?
namespace WD\Antirutin;
?>
<?if($intRowCount > 1):?>
	<textarea name="<?=$this->getInputName('value', $bMultiple);?>" rows="<?=$intRowCount;?>" cols="<?=$intColCount;?>"><?=$mValue;?></textarea>
<?else:?>
	<input type="text" name="<?=$this->getInputName('value', $bMultiple);?>" value="<?=$mValue;?>" size="<?=$intColCount;?>" />
<?endif?>
