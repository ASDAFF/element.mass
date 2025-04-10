<?
namespace WD\Antirutin\Plugins\Element;

use
	\WD\Antirutin\Helper,
	\WD\Antirutin\PluginElement;

class BarcodeGenerate extends PluginElement {

	protected $arFieldsFilter = [
		'FIELDS' => ['TYPE_FULL' => 'S', 'IS_WRITEABLE' => 'Y'],
		'PROPERTIES' => ['TYPE' => 'S', 'IS_WRITEABLE' => 'Y'],
		'CATALOG' => ['CODE' => 'BARCODE'],
	];
	
	// *******************************************************************************************************************
	// Main methods
	// *******************************************************************************************************************
	
	/**
	 *	Execute!
	 */
	public function processElement($elementId){
		if(strlen($mode = $this->get('mode'))){
			$method = sprintf('generator_%s', $mode);
			if(method_exists($this, $method)){
				$barcode = call_user_func([$this, $method]);
				if(is_string($barcode) && strlen($barcode) > 0){
					if(!$this->saveValue($elementId, $barcode)){
						$this->setError(sprintf('Error save barcode %s to element %s', $barcode, $elementId));
					}
				}
				else{
					$this->setError('Error generate barcode');
				}
			}
			else{
				$this->setError(sprintf('Generate handler is not found: %s', $method));
			}
		}
		else{
			$this->setError('Generate handler is empty');
		}
		return true;
	}

	protected function saveValue($elementId, $barcode){
		$bResult = false;
		$field = $this->get('field');
		if($this->isField($field)){
			$bResult = $this->update($elementId, [$field => $barcode]);
		}
		elseif($propertyId = $this->isProperty($field)){
			$bResult = $this->setPropertyValue($elementId, $propertyId, $barcode);
		}
		elseif($catalogField = $this->isCatalogField($field)){
			if($catalogField == 'BARCODE'){
				$bResult = Helper::setProductBarcodes($elementId, $barcode);
			}
		}
		return $bResult;
	}

	protected function getModes(){
		return [
			'EAN13_RUS' => static::getMessage('MODE_EAN13_RUS'),
			'EAN13' => static::getMessage('MODE_EAN13'),
		];
	}

	protected function generator_EAN13(){
		$random = $this->generateRandomNumber(12);
		return $this->generateEAN13($random);

	}

	protected function generator_EAN13_RUS(){
		$random = '46'.substr($this->generateRandomNumber(12), 2);
		return $this->generateEAN13($random);
	}

	private function generateRandomNumber(int $length){
		return str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
	}

	private function generateEAN13(string $number){
		$sum = 0;
		for($i = 0; $i < 12; $i++) {
			$sum += $number[$i] * (($i % 2 === 0) ? 1 : 3);
		}
		$checkDigit = (10 - ($sum % 10)) % 10;
		return $number.$checkDigit;
	}
	
}

?>