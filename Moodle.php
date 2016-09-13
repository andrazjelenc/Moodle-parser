<?php

class Moodle
{
	public $username = "";
	public $password = "";
	
	public $cert = "";

	public $token = "";
	public $sesskey = "";
	
	public function login()
	{
		$parameters = array('username' => $this->username, 'password' => $this->password);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://ucilnica.XXXX.si/login/index.php');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //sledimo preusmeritvam
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //rezultat shranimo pri exec
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //koliko sekund cakamo na povezavo
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);  //koliko sekund cakamo na odgovor
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters)); //parametri
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Host: ucilnica.XXXX.si',
				'Connection: keep-alive',
				'Cache-Control: max-age=0',
				'Origin: https://ucilnica.XXXX.si',
				'Upgrade-Insecure-Requests: 1',
				'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
				'Content-Type: application/x-www-form-urlencoded',
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
				'Referer: https://ucilnica.XXXX.si/login/index.php',
				'Accept-Encoding: gzip, deflate, br',
				'Accept-Language: sl-SI,sl;q=0.8,en-GB;q=0.6,en;q=0.4',
		));
		curl_setopt($ch, CURLOPT_HEADER, 1);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CAINFO, $this->cert);

		$result = curl_exec($ch); //pridobimo podatke
		curl_close($ch); //zapremo curl_close


		//print_r($result);
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
		
		$part = $matches[0];
		$part = $part[1];
		if (strpos($part, 'Set-Cookie: MoodleSession=') !== false) 
		{
			$part =  trim(str_replace("Set-Cookie: MoodleSession=","",$part));
			if(strlen($part) > 0)
			{
				$this->token = $part;
			}
		}
	}

	public function parse($id)
	{
		$url = 'https://ucilnica.XXXX.si/course/view.php?id='.$id;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //sledimo preusmeritvam
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //rezultat shranimo pri exec
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //koliko sekund cakamo na povezavo
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);  //koliko sekund cakamo na odgovor
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Host: ucilnica.XXXX.si',
				'Connection: keep-alive',
				'Cache-Control: max-age=0',
				'Upgrade-Insecure-Requests: 1',
				'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
				'Referer: https://ucilnica.XXXX.si/login/index.php',
				'Accept-Encoding: gzip, deflate, sdch, br',
				'Accept-Language: sl-SI,sl;q=0.8,en-GB;q=0.6,en;q=0.4',
				'Cookie: MoodleSession='.$this->token

		));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CAINFO, $this->cert);


		$result = curl_exec($ch); //pridobimo podatke
		curl_close($ch); //zapremo curl_close
		
		
		//potegnemo sesskey, ki ga rabimo za logout
		$pos = strpos($result, '"sesskey"');
		if($pos === false){
			echo "NAPAKA";
		} 
		$pos = $pos + 11;

		$sKey = "";
		for($i = 0; $i < 20; $i++) //safety
		{
			$c = $result[$pos + $i];
			if($c == '"') break;
				
			$sKey.= $c;
		}
		$this->sesskey = $sKey;

		return $result;
	}

	public function logout()
	{
		$url = 'https://ucilnica.XXXX.si/login/logout.php';//?sesskey='.$this->sesskey;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); //sledimo preusmeritvam
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //rezultat shranimo pri exec
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //koliko sekund cakamo na povezavo
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);  //koliko sekund cakamo na odgovor
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Host: ucilnica.XXXX.si',
			'Connection: keep-alive',
			'Upgrade-Insecure-Requests: 1',
			'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
			'Referer: https://ucilnica.XXXX.si/',
			'Accept-Encoding: gzip, deflate, sdch, br',
			'Accept-Language: sl-SI,sl;q=0.8,en-GB;q=0.6,en;q=0.4',
			'Cookie: MoodleSession='.$this->token


		));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'sesskey='.$this->sesskey); //parametri
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CAINFO, $this->cert);


		$result = curl_exec($ch); //pridobimo podatke
		curl_close($ch); //zapremo curl_close
		var_dump($result);
		var_dump($this->sesskey);
	}
}


?>
