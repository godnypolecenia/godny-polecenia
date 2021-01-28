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
 *	This file manages messsage
 */

$msgList = new Message;

$msg = $msgList -> getMessageById($url -> op(0), $user -> userId);

if(!is_array($msg)) {
	$url -> redirect('404');
}

$msgList -> saveShow($msg['message_id']);

$url -> addBackUrl();

/**
 *	Layout
 */
$meta = [
	'title' => $url -> title.' z '.$msg['sender'],
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
$bc -> add($url -> getLink('user/message-list'));
$bc -> add($url -> getLink(null, true));
$bc -> output();

?>
				
		<div id="left">
			<?php require_once(INC_DEFAULT_TPL_MENU); ?>
		</div>
		<div id="right">	
			<?php $main -> alert(); ?>
			<section>
				<h1><?php echo($meta['title']); ?></h1>
				<p><?php echo('Data: '.dateTImeFormat($msg['date']).'<br>'.textFormat($msg['content'])); ?></p>
			</section>
		</div>
		<div class="clear"></div>
	</div>
</div>

<?php require_once(INC_DEFAULT_TPL_FOOTER); ?>