<?
namespace WD\Antirutin;

use \WD\Antirutin\Helper;

Helper::loadMessages(__FILE__);
$strLang = 'WDA_OPTIONS_';
$strHint = $strLang.'HINT_';

return [
	'NAME' => Helper::getMessage($strLang.'OPTION_MANUAL'),
	'OPTIONS' => [
		'step_time' => [
			'NAME' => Helper::getMessage($strLang.'OPTION_STEP_TIME'),
			'HINT' => Helper::getMessage($strHint.'OPTION_STEP_TIME'),
			'TYPE' => 'text',
			'ATTR' => 'size="5" maxlength="5"',
		],
		'show_results' => [
			'NAME' => Helper::getMessage($strLang.'OPTION_SHOW_RESULTS'),
			'HINT' => Helper::getMessage($strHint.'OPTION_SHOW_RESULTS'),
			'TYPE' => 'select',
			'VALUES' => \WD\Antirutin\Worker::getOptionsShowResults(),
		],
		'email_enabled' => [
			'NAME' => Helper::getMessage($strLang.'OPTION_EMAIL_ENABLED'),
			'HINT' => Helper::getMessage($strHint.'OPTION_EMAIL_ENABLED'),
			'TYPE' => 'checkbox',
			'CALLBACK_MORE' => function($obOptions, $arOption)use($strLang){
				$strOptionEmail = sprintf('%s_option_%s', str_replace('.', '_', WDA_MODULE), 'email_address');
				?>
				<script>
					$('#<?=$arOption['INPUT_ID'];?>').bind('change', function(e){
						e.preventDefault();
						$('#<?=$strOptionEmail;?>').closest('tr').toggle($(this).prop('checked'));
					});
					$(document).ready(function(){
						$('#<?=$arOption['INPUT_ID'];?>').trigger('change');
					});
				</script>
				<?
			}
		],
		'email_address' => [
			'NAME' => Helper::getMessage($strLang.'OPTION_EMAIL_ADDRESS'),
			'HINT' => Helper::getMessage($strHint.'OPTION_EMAIL_ADDRESS'),
			'TYPE' => 'text',
			'ATTR' => 'size="32"',
		],
	],
];
?>