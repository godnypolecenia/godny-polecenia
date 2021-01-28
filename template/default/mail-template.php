<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */

if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *	This file displays mail message
 */

?>

<!DOCTYPE html>

<html lang="pl-PL">
	<head>
		<meta charset="utf-8">
		<style>
			body {
				
				margin: 0;
				padding: 0;
				
				font: 17px sans-serif;
				line-height: 1.5em;
				font-weight: 300;
			}
			
			#wrapper {
				
				padding: 50px;
				
				color: #111;
				background: #f5f5f5;
			}
			
			#message {
				
				max-width: 1300px;
				margin: 0 auto;
				padding: 30px;
				
				background: #fff;
				border: 1px solid #ccc;
			}
			
			h1 {
				
				margin: 0;
				padding: 0;
				
				font-size: 26px;
			}
			
			hr {
				
				margin: 30px 0;
				height: 1px;
				
				border: 0;
				background: #ccc;
			}
			
			p {
				
				margin: 0;
			}
			
			a {
				
				color: #4084c0;
			}
			
			a:hover {
				
				color: #cc1519;
			}
			
			.button {

				display: inline-block;
				margin: 0 5px 0 0;
				padding: 0 20px;
				height: 2.7em;

				background: #cc1519;
				color: #fff !important;
				font: 17px sans-serif;
				line-height: 2.7em;
				font-weight: 600;
				text-decoration: none !important;
				border-radius: 10px;
			}
			
			.button:hover {
				
				background: #ffbc3c;
				color: #fff !important;
			}
		</style>
	</head>
	<body>
		<div id="wrapper">
			<div id="message">
				<h1>{title}</h1>
				<hr>
				{content}
				<hr>
				{footer}
			</div>
		</div>
	</body>
</html>