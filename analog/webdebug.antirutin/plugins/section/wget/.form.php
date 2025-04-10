<?
use
	WD\Antirutin\Helper,
	WD\Antirutin\IBlock;

$strSiteHomepage = $this->get('site_homepage');
if(is_null($strSiteHomepage)){
	$strSiteHomepage = static::getMessage('SITE_HOMEPAGE_CURRENT');
}

?>

<div class="plugin-form__field">
	<div class="plugin-form__field-title" style="font-weight:normal;">
		<?=$this->fieldName('SITE_HOMEPAGE', true);?>
	</div>
	<div class="plugin-form__field-value">
		<input type="text" name="<?=$this->getInputName('site_homepage');?>" data-role="wget_site_homepage"
			value="<?=htmlspecialcharsbx($strSiteHomepage);?>" size="50" />
	</div>
</div>


<input type="hidden" data-role="empty_site_homepage" value="<?=static::getMessage('EMPTY_SITE_HOMEPAGE');?>" />
