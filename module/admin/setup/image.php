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
 *	This file manages image settings
 */

if($setup -> image <> '') {
	$img = explode(';', $setup -> image);
	$n = count($img);
} else {
	$n = 0;
}

if($url -> opd(URL_ADD) == URL_TYPE) {
	$img[] = $_POST['width'].'x'.$_POST['height'];
	$setup -> image = implode(';', $img);
	$main -> alertPrepare(true);
	$url -> redirect();
}

if($url -> opd(URL_DEL) == URL_TYPE) {
	unset($img[$url -> op(1)]);
	$setup -> image = implode(';', $img);
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
				<h2><span class="ri-image-line icon"></span>Wymiary zdjęć</h2>
	
<?php

if($n == 0) {
	echo('<div class="no-items">Niczego nie znaleziono</div>');
} else {
	echo('<table><tr><th>Szerokość</th><th>Wysokość</th><th>Usuń</th></tr>');
	foreach($img as $k => $v) {
		$ex2 = explode('x', $v);
		echo('<tr><td>'.$ex2[0].' px</td><td>'.$ex2[1].' px</td><td><a href="'.$url -> getUrl(null, false, '/'.URL_DEL.'-'.URL_TYPE.'/'.$k).'">Usuń</a></td></tr>');
	}
	echo('</table>');
}
	
?>

			</section>
			<section class="toggle">
				<h2><span class="ri-image-add-line icon"></span>Dodaj nowy wymiar</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD.'-'.URL_TYPE)); ?>">
					<div class="cols cols-2">
						<label>
							Szerokość
							<input type="text" name="width" required="required" placeholder="Wartość w pikselach">
						</label>
						<label>
							Wysokość
							<input type="text" name="height" required="required" placeholder="Wartość w pikselach">
						</label>
					</div>
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