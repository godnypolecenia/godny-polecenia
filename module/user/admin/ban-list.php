<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyAdmin(3);

/**
 *	This file manages the IP banishment
 */
 
$search = [];
$searchCount = 0;

if($url -> opd(URL_SEARCH) == URL_SEND) {
	$tmpUrl = '/'.URL_SEARCH;
	if($_POST['ip'] <> '') $tmpUrl .=  '/ip-'.urlencode(str_replace('.', '-', $_POST['ip']));
	$url -> redirect(null, false, $tmpUrl);
} elseif($url -> issetOpd(URL_SEARCH)) {
	if($url -> opd('ip') <> '') {
		$search['ip'] = str_replace('-', '.', urldecode($url -> opd('ip')));
		if($setup -> ban <> '') {
			$banlist = explode(';', $setup -> ban);
			foreach($banlist as $k => $v) {
				if($v <> $search['ip']) {
					unset($banlist[$k]);
				}
			}
			$n = count($banlist);
		} else {
			$n = 0;
		}
		$searchCount++;
	}
} else {
	if($setup -> ban <> '') {
		$banlist = explode(';', $setup -> ban);
		$n = count($banlist);
	} else {
		$n = 0;
	}
}

/**
 *
 */
 
if($url -> opd(URL_EXEC) <> '') {
	$banlist[] = str_replace('-', '.', $url -> opd(URL_EXEC));
	$setup -> ban = implode(';', $banlist);
	$main -> alertPrepare(true);
	$url -> redirect();
}

if($url -> op(0) == URL_ADD) {
	$banlist[] = $_POST['ip'];
	$setup -> ban = implode(';', $banlist);
	$main -> alertPrepare(true);
	$url -> redirect();
}

if($url -> op(0) == URL_DEL) {
	unset($banlist[$url -> op(1)]);
	$setup -> ban = implode(';', $banlist);
	$main -> alertPrepare(true);
	$url -> redirect();
}

/**
 *
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
$bc -> add($url -> getLink('user/admin/index'));
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_ADMIN_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section id="search-container" class="open-section-container<?php if($searchCount > 0) echo(' open-section-container-show'); ?>">
				<h2><span class="ri-search-line icon"></span>Szukaj</h2>
				<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_SEARCH.'-'.URL_SEND)); ?>">
					<label>
						Adres IP
						<input type="text" name="ip" required="required" value="<?php echo($search['ip']); ?>">
					</label>
					<div class="buttons">
						<input type="submit" value="Szukaj">
						<?php if($searchCount > 0) echo('<a href="'.$url -> getUrl().'" class="underline">Anuluj wyszukiwanie</a>'); ?>
					</div>
				</form>
			</section>
			<section>
				<h1><span class="ri-close-circle-line icon"></span><?php echo($meta['title']); ?></h1>
				<a href="#" id="search-open" class="ri-search-line open-section section-top-right" title="Szukaj"></a>

<?php

if($n == 0) {
	echo('<div>Niczego nie znaleziono</div>');
} else {
	echo('<table>');
	echo('<tr>');
	echo('<th>Adres IP</th>');
	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($banlist as $k => $v) {
		echo('<tr>');
		echo('<td>'.$v.'</td>');
		echo('<td><a href="'.$url -> getUrl(null, false, '/'.URL_DEL.'/'.$k).'">Odblokuj</a></td>');
		echo('</tr>');
	}
	echo('</table>');
}
	
?>

		</section>
		<section class="toggle">
			<h2><span class="ri-add-circle-line icon"></span>Zablokuj adres IP</h2>
			<form method="post" action="<?php echo($url -> getUrl(null, false, '/'.URL_ADD)); ?>">
				<label>
					Adres IP
					<input type="text" name="ip" required="required">
				</label>
				<div class="buttons">
					<input type="submit" value="Zablokuj">
				</div>
			</form>
		</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>