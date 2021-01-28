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
 *	This file manages URL settings
 */

$countUrl = $url -> countUrlList((($url -> opd('root') == 1) ? null : 'constant=0'));

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
				<h1><span class="ri-map-2-line icon"></span><?php echo($meta['title']); ?></h1>
				
<?php

if($countUrl > 0) {
	echo('<table>');
	echo('<tr>');
	echo('<th>Adres</th>');
	echo('<th>Plik</th>');
	echo('<th>Przycisk</th>');
	echo('<th>Tytuł</th>');
	echo('<th>Opcje</th>');
	echo('</tr>');
	foreach($url -> getUrlList((($url -> opd('root') == 1) ? null : 'constant=0'), true) as $r) {
		echo('<tr>');
		echo('<td><span class="ri-link-m icon"></span> <a href="'.SITE_ADDRESS.'/'.$r['url'].'" target="_blank">'.$r['url'].'</a></td>');
		echo('<td>'.$r['file'].'</td>');
		echo('<td>'.$r['button'].'</td>');
		echo('<td>'.$r['title'].'</td>');
		echo('<td>');
			if($r['file'] == 'page/page') {
				echo('<a href="'.$url -> getUrl('page/admin/edit', false, '/'.$r['var']['page_id']).'">Zarządzaj</a>');
			} elseif($r['file'] == 'item/item') {
				echo('<a href="'.$url -> getUrl('item/admin/edit', false, '/'.$r['var']['item_id']).'">Zarządzaj</a>');
			} else {
				echo('<a href="'.$url -> getUrl('admin/setup/url', false, '/'.$r['url_id']).'">Zarządzaj</a>');
			}
		echo('</td>');
		echo('</tr>');		
	}
	echo('</table>');
	paging($countUrl);
}

?>			
					
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_ADMIN_TPL_FOOTER); ?>