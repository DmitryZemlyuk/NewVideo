<?php 

	function send_sms($to, $msg, $login, $password){
		$u = 'http://www.websms.ru/http_in5.asp';
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'Http_username='.urlencode($login). '&Http_password='.urlencode($password).  '&Phone_list='.$to.'&Message='.urlencode($msg));
		curl_setopt($ch, CURLOPT_URL, $u);

		$u = trim(curl_exec($ch));
		curl_close($ch);

		preg_match("/message_id\s*=\s*[0-9]+/i", $u, $arr_id );
		$id = preg_replace("/message_id\s*=\s*/i", "", @strval($arr_id[0]) );

		return $id;
	}

	// some defaults
	$url = 'https://gdata.youtube.com/feeds/users/PTXofficial/uploads'; //ссылка на канал

	$filename = './lastvideoi.txt';
	$find = file_get_contents($filename);
	$find = !empty($find) ? $find : '<published>2014-03-17T15:52:31.000Z</published>'; //время публикации последнего видео на канале пользователя

	$mob = '****'; //твой мобильный в формате +380123456789
	$login = '****'; //твой логин на сервисе отправки смс
	$pass = '****';//твой пароль на сервисе отправки смс

	// запрос к youtube
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11');
	curl_setopt($ch, CURLOPT_URL, $url);
	$page = curl_exec($ch);
	curl_close($ch);

	// parse
	if ($page == false) {
		exit('Empty curl response.');
	}

	preg_match_all('/(<published>.*?<\/published>)/ui', $page, $matches);
	if (empty($matches[1])) {
		exit('Empty parse results.');
	}

	if ($matches[1][0] != $find) {	// если новое видео вверху, то оно должно быть нулевым матчем, иначе - появилось что-то новое
		file_put_contents($filename, $matches[1][0]); // пишем дату публикации нового видео, чтоб не было смс постоянно

		$msg = 'You have a new video!!!';
		send_sms($mob, $msg, $login, $pass);	
	}

	else {
		die;
	} 

?>