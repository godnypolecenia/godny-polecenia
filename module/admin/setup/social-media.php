<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(7);

/**
 *	This file manages social media settings
 */

if($url -> op(0) == URL_SAVE && is_array($_POST)) {
	$vd = new Validate;
	foreach($_POST as $k => $v) {
		if($v <> '') {
			if($vd -> isUrl($v)) {
				$setup -> $k = $v;
			}
		} else {
			$setup -> $k = '';
		}
	}
	$main -> alertPrepare($vd -> pass(), $vd -> resultArray());
	
	if($vd -> pass() == true) {		
		$url -> redirect();
	}	
}

/**
 *	Add URL to history
 */
$url -> addBackUrl();

/**
 *	Layout
 */
$meta = [
	'title' => $url -> title,
	'description' => $url -> description,
	'keywords' => $url -> keywords,
	'robots' => (($url -> index == 1) ? 'index' : 'noindex')
];
require_once(INC_ADMIN_TPL_HEADER);

?>

<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink('admin/index'));
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-facebook-circle-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Facebook fanpage
						<input type="text" name="facebook" value="<?php echo($setup -> facebook); ?>" placeholder="Link do fanpage na Facebooku">
					</label>
					<label>
						Google Plus
						<input type="text" name="google" value="<?php echo($setup -> google); ?>" placeholder="Link do strony w Ggoogle Plus">
					</label>
					<label>
						Twitter
						<input type="text" name="twitter" value="<?php echo($setup -> twitter); ?>" placeholder="Link do kanału na Twitterze">
					</label>
					<label>
						Youtube
						<input type="text" name="youtube" value="<?php echo($setup -> youtube); ?>" placeholder="Link do kanału na Youtube">
					</label>
					<label>
						Instagram
						<input type="text" name="instagram" value="<?php echo($setup -> instagram); ?>" placeholder="Link do kanału na Instagramie">
					</label>
					<div class="buttons">
						<input type="submit" value="Zapisz">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>