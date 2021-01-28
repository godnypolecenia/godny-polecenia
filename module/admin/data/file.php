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
 *	This file manages the file
 */

if($url -> op(2) == URL_DEL && $_POST['delete'] == 1) {
	if(file_exists('./data/file/'.$url -> op(0).'.'.$url -> op(1))) {
		unlink('./data/file/'.$url -> op(0).'.'.$url -> op(1));
	}
	$main -> alertPrepare(true);
	$url -> redirect('admin/data/file-list');
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
$bc -> add($url -> getLink('admin/file-list'));
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h2><span class="ri-link-m icon"></span>Link do pliku</h2>
				<a href="<?php echo($url -> getUrl('download', false, '/'.$url -> op(0).'/'.$url -> op(1))); ?>" target="_blank"><?php echo($url -> getUrl('download', false, '/'.$url -> op(0).'/'.$url -> op(1))); ?></a>
			</section>
			<section class="toggle">
				<h2><span class="ri-delete-bin-line icon"></span>Usuń plik</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, true, '/'.URL_DEL)); ?>">
					<label>
						<input type="checkbox" name="delete" value="1" required="required">
						Potwierdzam chęć usunięcia tego pliku
					</label>
					<div class="buttons">
						<input type="submit" value="Usuń">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>