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
 *	This file manages SEO settings
 */

if($url -> op(0) == URL_SAVE && is_array($_POST)) {
	foreach($_POST as $k => $v) {
		$setup -> $k = $v;
	}
	$main -> alertPrepare(true);
	$url -> redirect();
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
				<h1><span class="ri-search-line icon"></span><?php echo($meta['title']); ?></h1>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SAVE)); ?>">
					<label>
						Nazwa serwisu
						<input type="text" name="name" value="<?php echo($setup -> name); ?>" placeholder="" required="required" maxlength="255">
					</label>
					<label>
						Tytuł strony
						<input type="text" name="title" value="<?php echo($setup -> title); ?>" placeholder="Znacznik <title>" required="required" maxlength="255">
					</label>
					<label>
						Opis strony
						<input type="text" name="description" value="<?php echo($setup -> description); ?>" placeholder="Znacznik <meta description>" maxlength="255">
					</label>
					<label>
						Słowa kluczowe
						<input type="text" name="keywords" value="<?php echo($setup -> keywords); ?>" placeholder="Znacznik <meta keywords>" maxlength="255">
					</label>
					<input type="hidden" name="index" value="0">
					<label>
						<input type="checkbox" name="index" value="1"<?php if($setup -> index == 1) echo(' checked="checked"'); ?>> Indeksuj w przeglądarkach
					</label>
					<input type="hidden" name="sitemap" value="0">
					<label>
						<input type="checkbox" name="sitemap" value="1"<?php if($setup -> sitemap == 1) echo(' checked="checked"'); ?>> Uruchom <a href="./sitemap.xml" target="_blank">mapę strony</a>
					</label>
					<input type="hidden" name="rss" value="0">
					<label>
						<input type="checkbox" name="rss" value="1"<?php if($setup -> rss == 1) echo(' checked="checked"'); ?>> Uruchom <a href="./feed" target="_blank">kanał RSS</a>
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