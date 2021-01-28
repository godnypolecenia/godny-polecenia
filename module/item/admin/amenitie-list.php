<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(2);

/**
 *	
 */

if($url -> op(0) == URL_ADD && $_POST['name'] <> '') {
	$amenitie[] = $_POST['name'];
	$setup -> amenitie = implode(';', $amenitie);
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
$bc -> add($url -> getLink('item/admin/index'));
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><span class="ri-folders-line icon"></span><?php echo($meta['title']); ?></h1>
				<table>
					<tr>
						<th>Nazwa</th>
						<th>Opcje</th>
					</tr>

<?php

foreach($amenitie as $k => $v) {
	echo('<tr>');
	echo('<td>'.$v.'</td>');
	echo('<td><a href="'.$url -> getUrl('item/admin/amenitie', false, '/'.$k).'">Zarządzaj</a></td>');
	echo('</tr>');
}

?>

				</table>
			</section>
			<section class="toggle">
				<h2><span class="ri-folder-add-line icon"></span>Dodaj pozycję</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>">
					<label>
						Nazwa udogodnienia 
						<input type="text" name="name" required="required" value="<?php echo($_POST['name']); ?>">
					</label>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>