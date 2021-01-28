<?php

/**
 *	SopiCMS Framework System v5
 *	---------------------------
 *	@author    Michał Wrzesiński <kontakt@webhome.pl>
 *	@website   webhome.pl
 */
 
if(!defined('SOPICMS')) exit('Forbidden 403');

/**
 *
 */
define('DIR_USER', 'konto');
define('DIR_USER_TITLE', 'Twoje konto');

define('DIR_ADMIN', 'admin');
define('DIR_ADMIN_TITLE', 'Panel administracyjny');

/**
 *
 */
define('ITEM_LIST_TITLE', 'Lista firm');
define('ITEM_TITLE', 'Firma');
define('ITEM_ADD_TITLE', 'Dodaj firmę');
define('ITEM_ADDED_TITLE', 'Dodane firmy');
define('ITEM_EDIT_TITLE', 'Zarządzaj firmą');
define('ITEM_DEL_TITLE', 'Usuń firmę');
define('ITEM_FAV_TITLE', 'Obserwowane firmy');
define('ITEM_BUTTON', 'Pokaż szczegóły');


define('INC_DEFAULT_TPL_HEADER', './template/default/header.php');
define('INC_DEFAULT_TPL_FOOTER', './template/default/footer.php');
define('INC_DEFAULT_TPL_MENU', './template/default/menu.php');

define('INC_ADMIN_TPL_HEADER', './template/admin/header.php');
define('INC_ADMIN_TPL_FOOTER', './template/admin/footer.php');
define('INC_ADMIN_TPL_MENU', './template/admin/menu.php');

/**
 *	Variables supported in the URL
 */
define('URL_PAGE', 'strona');
define('URL_EDIT', 'edytuj');
define('URL_ACTIVE', 'aktywuj');
define('URL_DEACTIVE', 'dezaktywuj');
define('URL_OPEN', 'otworz');
define('URL_CLOSE', 'zamknij');
define('URL_COPY', 'kopiuj');
define('URL_SAVE', 'zapisz');
define('URL_EXEC', 'wykonaj');
define('URL_DEL', 'usun');
define('URL_CLEAR', 'wyczysc');
define('URL_ADD', 'dodaj');
define('URL_SEND', 'wyslij');
define('URL_SET', 'ustaw');
define('URL_DATA', 'dane');
define('URL_EMAIL', 'email');
define('URL_PASS', 'haslo');
define('URL_IMAGE', 'zdjecie');
define('URL_TYPE', 'typ');
define('URL_NAME', 'nazwa');
define('URL_SEARCH', 'szukaj');
define('URL_CANCEL', 'anuluj');
define('URL_BACK', 'cofnij');
define('URL_NEXT', 'dalej');
define('URL_DONE', 'gotowe');
define('URL_LOGIN', 'logowanie');
define('URL_COOKIE', 'cookie');
define('URL_LANG', 'jezyk');
define('URL_NEW', 'nowe');
define('URL_UP', 'do-gory');
define('URL_DOWN', 'w-dol');
define('URL_UPLOAD', 'wgraj');
define('URL_DOWNLOAD', 'pobierz');
define('URL_BILL', 'faktura');
define('URL_REFRESH', 'odswiez');
define('URL_FROM', 'od');
define('URL_TO', 'do');
define('URL_QUERY', 'q');
define('URL_CATEGORY', 'c');
define('URL_REGION', 'w');
define('URL_CITY', 'miejscowosc');
define('URL_PRICE', 'cena');
define('URL_RADIUS', 'r');
define('URL_FAVORITE', 'obserwuj');
define('URL_CHANGE', 'zmien');
define('URL_CLEAR', 'wyczysc');
define('URL_CARD', 'koszyk');
define('URL_FEATURE', 'cechy');
define('URL_YES', 'tak');
define('URL_NO', 'nie');
define('URL_BOOKMARK', 'zakladka');
define('URL_EXPORT', 'eksportuj');
define('URL_IMPORT', 'importuj');

/**
 *	File upload messages
 */
define('FILE_ERR_NULL', 'Nie załączono pliku');
define('FILE_ERR_SEND', 'Nie przesłano pliku');
define('FILE_ERR_WEIGHT', 'Zbyt duża waga pliku');
define('FILE_ERR_FORMAT', 'Zły format pliku');
define('FILE_ERR_UPLOAD', 'Nie udało się wgrać pliku na serwer');

/**
 *	User messages
 */
define('USER_ERR_BAD', 'Zły adres e-mail lub hasło');
define('USER_ERR_CAPTCHA', 'Potwierdź, że nie jesteś robotem');
define('USER_ERR_NULL', 'Uzupełnij wszystkie pola');
define('USER_ERR_BUSY', 'Podany adres e-mail jest już zajęty przez innego użytkownika');
define('USER_ERR_DIFFERENT_PASS', 'Podane hasła różnią się');
define('USER_ERR_CURRENT_PASS', 'Podano błędne obecne hasło');
define('USER_ERR_NOT_EXISTS', 'Podany adres e-mail nie znajduje się w naszej bazie danych');

/**
 *	Validate Class message
 */
define('VALIDATE_IS_VALUE', 'Pole obowiązkowe');
define('VALIDATE_IS_STRING', 'Wpisz tekst');
define('VALIDATE_IS_STRING_MIN', 'Tekst jest za krótki');
define('VALIDATE_IS_STRING_MAX', 'Tekst jest za długi');
define('VALIDATE_EMAIL', 'Błędny adres e-mail');
define('VALIDATE_URL', 'Błędny adres www');
define('VALIDATE_INT', 'Wartość nie jest liczbą całkowitą');
define('VALIDATE_FLOAT', 'Wartość nie jest liczbą zmiennoprzecinkową');
define('VALIDATE_IS_POSSIBLE', 'Niedozwolona wartość');
define('VALIDATE_IS_CAPTCHA', 'Potwierdź, że nie jesteś robotem');

/**
 *	Other messages
 */
define('FORM_ERR_NULL', 'Uzupełnij wszystkie pola');
define('FORM_ERR_CAPTCHA', 'Potwierdź, że nie jesteś robotem');
define('FORM_ERR_UNKNOWN', 'Operacja niepowiodła się');
define('PAGE_TITLE_ERR_BUSY', 'Tytuł jest już zajęty');

/**
 *	The array with payment
 */
$paymentType = [
	1 => 'Pakiet Standard',
	2 => 'Pakiet Premium'
];

$paymentStatus = [
	-1 => 'Anulowane',
	0 => 'Nieopłacone',
	1 => 'Opłacone'
];

/**
 *	The array with feature types
 */
$featureType = [
	1 => 'Pole tekstowe',
	2 => 'Pole liczbowe',
	3 => 'Lista rozwijana',
	4 => 'Pole zaznaczane'
];

/**
 *	The array with region names
 */
$regionName = [
    1 => 'Dolnośląskie',
    2 => 'Kujawsko-pomorskie',
    3 => 'Lubelskie',
    4 => 'Lubuskie',
    5 => 'Łódzkie',
    6 => 'Małopolskie',
    7 => 'Mazowieckie',
    8 => 'Opolskie',
    9 => 'Podkarpackie',
    10 => 'Podlaskie',
    11 => 'Pomorskie',
    12 => 'Śląskie',
    13 => 'Świętokrzyskie',
    14 => 'Warmińsko-mazurskie',
    15 => 'Wielkopolskie',
    16 => 'Zachodniopomorskie',
	17 => 'Zagranica'
];

/**
 *	The array with big city names
 */
$cityName = [
	1 => 'Warszawa',
	2 => 'Kraków',
	3 => 'Łódź',
	4 => 'Wrocław',
	5 => 'Poznań',
	6 => 'Gdańsk',
	7 => 'Szczecin',
	8 => 'Bydgoszcz',
	9 => 'Lublin',
	10 => 'Białystok'
];

/**
 *	The array with month names
 */
$monthName = [
	1 => 'Styczeń',
	2 => 'Luty',
	3 => 'Marzec',
	4 => 'Kwiecień',
	5 => 'Maj',
	6 => 'Czerwiec',
	7 => 'Lipiec',
	8 => 'Sierpień',
	9 => 'Wrzesień',
	10 => 'Październik',
	11 => 'Listopad',
	12 => 'Grudzień'
];

/**
 *	The array with day names
 */
$dayName = [
	0 => 'Niedziela',
	1 => 'Poniedziałek',
	2 => 'Wtorek',
	3 => 'Środa',
	4 => 'Czwartek',
	5 => 'Piątek',
	6 => 'Sobota',
	7 => 'Niedziela'
];


?>