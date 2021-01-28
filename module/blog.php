<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays blog entries
 */

$url -> addBackUrl();

$sqlSearch = ' && `group` = 1 ';

$pageList = new Page;
$countPage = $pageList -> countPageList($sqlSearch);

/**
 *	Layout
 */
$meta = [
	'title' => $url -> title,
	'description' => $url -> description,
	'keywords' => $url -> keywords,
	'robots' => (($url -> index == 1) ? 'index' : 'noindex')
];
require_once(INC_DEFAULT_TPL_HEADER);

?>

<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink());
$bc -> output();

?>
			
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">
			<?php $main -> alert(); ?>
			<section>
				<h1><?php echo($meta['title']); ?></h1>
				<?php if($countPage == 0) echo('<p>Niczego nie znaleziono.</p>'); ?>
			</section>
			<div class="cols-float cols-float-3">

<?php 

if($countPage > 0) {
	foreach($pageList -> getPageList($sqlSearch, '`page_id` DESC') as $r) {
		$pageUrl = $url -> getUrl('page/page?page_id='.$r['page_id']);
		$gal = explode(';', $r['gallery']);
		if($gal[0] <> '') {
			$ex = explode('.', $gal[0]);
			$img = $ex[0].'.400x300.'.end($ex);
		} else {
			$img = 'default.400x300.jpg';
		}
		
		echo('<section class="item item-click" data-url="'.$pageUrl.'">');
			echo('<div class="item-img">');
				echo('<a href="'.$pageUrl.'"><img src="'.$url -> getUrl('tool/image', false, '/'.$img).'" alt="'.$r['title'].'"></a>');
			echo('</div>');
			echo('<div class="item-content">');
				echo('<h3><a href="'.$pageUrl.'">'.$r['title'].'</a></h3>');
				echo('<p>'.mb_substr(trim(strip_tags($r['content'])), 0, 200).'</p>');
				echo('<div class="buttons">');
					echo('<a href="'.$pageUrl.'" class="button">Czytaj całość</a>');
				echo('</div>');
			echo('</div>');
		echo('</section>');	
	}
	paging($countPage);
}

?>

			</div>
			<div class="clear"></div>
			<?php paging($countPage); ?>
		</div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>