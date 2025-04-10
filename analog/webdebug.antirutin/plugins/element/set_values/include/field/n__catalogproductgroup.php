<?
namespace WD\Antirutin;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\IBlock;

?>
<?=$this->selectBox($this->getInputName('value', $bMultiple), IBlock::getProductGroups(), $mValue, null);?>