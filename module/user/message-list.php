<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */
 
if(!defined('SOPICMS')) exit('Forbidden 403');

$user -> onlyUser();

/**
 *	This file manages messages
 */

$msgList = new Message;
$countUserMsg = $msgList -> countMessageListOfUser($user -> userId);

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
require_once(INC_DEFAULT_TPL_HEADER);

?>

<div id="content">
	<div class="main">

<?php

$bc = new Breadcrumb();
$bc -> add($url -> getLink('user/index'));
$bc -> add($url -> getLink());
$bc -> output();

?>
				
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">		
			<section>
				<h1><span class="ri-message-2-line icon"></span><?php echo($meta['title']); ?></h1>
<?php

if($countUserMsg > 0) {
	echo('<ul class="ul">');
	foreach($msgList -> getUserMessageListOfUser($user -> userId) as $r) {
		$msgUrl = $url -> getUrl('user/message', false, '/'.$r['message_id']);
		echo('<li'.(($r['show_date'] == 0) ? ' class="bold"' : '').'><span class="ri-mail-line'.(($r['show_date'] == 0) ? ' red' : '').'"></span> <a href="'.$msgUrl.'">Wiadomość od '.$r['sender'].' z dnia '.dateTimeFormat($r['date']).'</a></li>');
	}
	echo('</ul>');
	paging($countItems);
} else {
	echo('<p>Niczego nie znaleziono</p>');
}

?>
			
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>