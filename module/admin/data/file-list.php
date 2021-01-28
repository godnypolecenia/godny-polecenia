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
 *	This file manages the files
 */

if($url -> op(0) == URL_ADD && is_array($_POST)) {
	if(is_array($_FILES['file'])) {
		$file = new Upload($_FILES['file']);
		if($file -> save('./data/file') <> false) {
			$main -> alertPrepare(true);
			$url -> redirect();
		} else {
			$main -> alertPrepare(false, FILE_ERR_UPLOAD);
		}
	} else {
		$main -> alertPrepare(false, FILE_ERR_NULL);
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
				<h1><span class="ri-database-2-line icon"></span><?php echo($meta['title']); ?></h1>

<?php

$array = [];
$dir = './data/file/';
if($handle = opendir($dir)) {
	while(false !== ($file = readdir($handle))) {
		if(is_file($dir.$file) && $file <> 'index.html' && $file <> '.htaccess') {
			$ex = explode('.', $file);
			$array[$ex[0]] = array($file, filesize($dir.$file), end($ex));
		}
	}
}

if(count($array) == 0) {
	echo('<div class="no-items">Niczego nie znaleziono</div>');
} else {
	echo('<table><tr><th>Link</th><th>Waga</th><th>Zarządzaj</th></tr>');
	foreach($array as $k => $v) {
		echo('<tr><td><a href="'.$url -> getUrl('tool/download', false, '/'.$v[0]).'" target="_blank">'.$url -> getUrl('tool/download', false, '/'.$v[0]).'</a></td><td>'.(number_format(($v[1]/1024000), 2, ',', '.')).' MB</td><td><a href="'.$url -> getUrl('admin/data/file', false, '/'.$k.'/'.$v[2]).'">Zarządzaj</a></td></tr>'."\n");
	}
	echo('</table>');
}

?>

			</section>
			<section class="toggle">
				<h2><span class="ri-upload-2-line icon"></span>Wgraj pliki</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>" enctype="multipart/form-data" class="dropzone" id="dropzone">
					<label class="fallback">
						<input type="file" name="file" required="required">
					</label>
					<div class="buttons">
						<input type="submit" value="Dodaj">
					</div>
				</form>
				<br>
				<a href="<?php echo($url -> getUrl()); ?>" class="button">Dodaj</a>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>