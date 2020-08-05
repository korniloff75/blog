<?php

trait Curl
{
	protected static
		$proxyPath = __DIR__.'/db.proxy',
		$timeoutInSeconds = 1.5;

	/**
	 * ! Required
	 * @param proxy - полная строка для вывода в js
	 * @param stop - service stop recursion
	 */
	protected function findAnzProxy(?string $proxy=null, bool $stop=false)
	{
		$parsePath = 'https://cloudflare-ipfs.com/ipns/pacipfs2.antizapret.prostovpn.org/proxy-nossl.js';

		if(file_exists(self::$proxyPath))
		{
			$proxy = $proxy ?? file_get_contents(self::$proxyPath);

			preg_match("~PROXY\s+(.+); DIRECT~i", $proxy, $proxyURL);

			$proxyURL = "http://" . trim($proxyURL[1]);
			trigger_error("\$proxyURL = $proxyURL");

			$p = parse_url($proxyURL);
			// $p['scheme'].'://'.

			//* Если прокси из файла доступен - возвращаем его
			if($fp = fsockopen($p['host'], $p['port'], $errCode, $errStr, self::$timeoutInSeconds))
			{
				$this->log->add("Proxy $proxyURL - is <font color=green size=4><b>AVAILABLE</b></font>\n");
				return $proxyURL;
			}
			//* Если недоступен - удаляем файл + рекурсия
			else
			{
				trigger_error("$proxyURL - ERROR: $errCode - $errStr", E_USER_WARNING);
				unlink(self::$proxyPath);
				return $this->findAnzProxy(null, $stop);
			}
		}
		//* Если нет файла
		else
		{
			if(
				//* Если повторная рекурсия - тормозим
				!$stop
				//* Ищем обновлённый прокси
				&& ($anz = file_get_contents($parsePath))
				&& preg_match(
					"~return \"(PROXY.+DIRECT)\";$~im", $anz, $proxy)
			)
			{
				$proxy = trim($proxy[1]);
				file_put_contents(self::$proxyPath, $proxy);

				//* Рекурсия с новым прокси
				return $this->findAnzProxy($proxy, true);
			}

		}
		//* Полный провал
		return false;
	} //* findAnzProxy


	public function findProxy()
	{
		// if($proxyURL = $this->findAnzProxy()) return $proxyURL;

		require_once __DIR__.'/../classes/DbJSON.php';

		$arrProxy= (new DbJSON(__DIR__.'/../tg/Common/proxy.json'))->get();

		foreach($arrProxy as &$proxyURL)
		{
			trigger_error("\$proxyURL = $proxyURL");

			$p = parse_url($proxyURL);
			// $p['scheme'].'://'.

			//* Если прокси из файла доступен - возвращаем его
			if($fp = fsockopen($p['host'], $p['port'], $errCode, $errStr, self::$timeoutInSeconds))
			{
				$this->log->add(__METHOD__." Proxy $proxyURL - is <font color=green size=4><b>AVAILABLE</b></font>\n");
				return $proxyURL;
			}
		}

		$this->log->add(__METHOD__." ALL Proxies is <font color=red size=4><b>FAIL</b></font>\n");
	} // findProxy


	public function browsEmul()
	{
		return [
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			// 'Accept-Encoding: gzip',
			// 'Accept-Encoding: gzip, deflate',
			'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
			'Cache-Control: no-cache',
			'Connection: keep-alive',
			'DNT: 1',
			'Host: google.com',
			'Pragma: no-cache',
			'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:68.0) Gecko/20100101 Firefox/68.0',
		];

	}


	/**
	 * https://www.php.net/manual/ru/function.curl-setopt.php
	 */
	private function CurlSetOpt(string $url, array $opts=[])
	{
		$opts = array_merge([
			'sendMethod' => 'post',
			'headers' => ["Content-Type:multipart/form-data"]
		], $opts);

		$this->log->add(__METHOD__ . " \$url, \$opts = ", null, [$url, $opts]);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		//* Все кодировки
		curl_setopt($ch, CURLOPT_ENCODING, '');
		// curl_setopt($ch, CURLOPT_ENCODING, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		if(is_array($opts['headers']))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $opts['headers']);

		}

		if('post' === strtolower(@$opts['sendMethod']))
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			if(is_array(@$opts['params']))
			{
				foreach ($opts['params'] as &$val)
				{
					# encoding to JSON not primitive parameters
					if (!is_numeric($val) && !is_string($val)) {
						$val = json_encode($val, JSON_UNESCAPED_UNICODE);
					}
				}
				curl_setopt($ch, CURLOPT_POSTFIELDS, $opts['params']);
				$this->log->add(__METHOD__ . 'params= ', null, [$opts['params']]);
			}

		}
		else //* GET etc.
		{
			if(is_array(@$opts['params']))
				$url.= '?' . http_build_query($opts['params']);
		}

		if(!empty($opts['CURLOPT_MAXREDIRS']))
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $opts['CURLOPT_MAXREDIRS']);
			//* Битовая маска, содержащая 1 (301), 2 (302) и 4 (303 See Other)
			curl_setopt($ch, CURLOPT_POSTREDIR, $opts['CURLOPT_POSTREDIR'] ?? 3);
		}

		curl_setopt($ch, CURLOPT_URL, $url);

		// $response = curl_exec($ch);

		return $ch;
	}


	public function CurlRequest(string $url, array $opts=[])
	{
		$ch = $this->CurlSetOpt($url, $opts);

		return $this->execCurl($ch);
	}


	public function CurlRequestProxy(string $url, array $opts=[])
	{
		$ch = $this->CurlSetOpt($url, $opts);
		// curl_setopt($ch, CURLOPT_PROXY, $this->findAnzProxy());
		curl_setopt($ch, CURLOPT_PROXY, $this->findProxy());

		return $this->execCurl($ch);
	}


	public function CurlRequestBrows(string $url, array $opts=[])
	{
		$opts['headers']= $this->browsEmul();
		$opts['sendMethod']= 'get';
		$ch = $this->CurlSetOpt($url, $opts);

		return $this->execCurl($ch, $opts);
	}


	/**
	 ** Режем ответ сервера в массив
	 * @returns ['headers'=>[], 'body'=>[]]
	 */

	public function splitResponse($response)
	{
		$fullResp = ['headers'=>[]];

		if ($header_size = intval($this->curlInfo['header_size']))
		{
			$respHeaders = substr($response, 0, $header_size);
			$fullResp['body'] = trim(substr($response, $header_size));

			foreach(explode(PHP_EOL, $respHeaders) as &$header)
			{
				$tmp = explode(":",trim($header),2);
				if(count($tmp) === 2)
				{
					$fullResp['headers'][strtolower($tmp[0])] = trim(strtolower($tmp[1] ?? null));
				}
				elseif(!empty(trim($tmp[0]))) $fullResp['headers'][]= trim($tmp[0]);
			}
			//* Check json
			if(
				!empty($fullResp['headers']['content-type'])
				&& stripos($fullResp['headers']['content-type'], 'application/json') !== false
			)
				$fullResp['body'] = json_decode($fullResp['body'], 1);

			// $this->log->add(__METHOD__,null,[$fullResp['headers']['content-type'], $fullResp['body']]);
		}
		else $fullResp['body'] = $response;

		return $fullResp;
	}


	/**
	 ** Исполнение Curl, вывод и логирование результатов
	 */
	public $response = '';

	protected function execCurl($ch, $opts=[])
	{
		$opts = array_merge([
			'json' => 1,
		], $opts);

		$response = &$this->response;
		$this->log->add(__METHOD__ . " resourse, \$opts: ",null, [$ch,$opts]);

		//note deprecated
		if($opts['chunked'])
		{
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
			// curl_setopt($ch, CURLOPT_WRITEFUNCTION, [$this, 'chunkedCallback']);
		}

		$response = curl_exec($ch);

		if ($response === false)
		{
			$errno = curl_errno($ch);
			$error = curl_error($ch);
			$this->log->add("Curl returned error $errno: $error", E_USER_WARNING);
			curl_close($ch);

			return false;
		}

		$this->curlInfo = curl_getinfo($ch);
		$http_code = intval($this->curlInfo['http_code']);

		if(!is_string($response)) $this->log->add(__METHOD__ . ' $response_orig NOT STRING! = ', E_USER_WARNING, [$response]);

		$fullResp = $this->splitResponse($response);
		list('headers'=>&$respHeaders, 'body'=>&$response) = $fullResp;

		$this->log->add(__METHOD__ . ' $http_code = ', null, [$http_code, $respHeaders /* , $fullResp, gzdecode($response), $this->curlInfo */]);

		if ($http_code >= 500)
		{
			// do not wat to DDOS server if something goes wrong
			usleep(500);
		}
		elseif (!empty($respHeaders['location']) && ($http_code === 301 || $http_code === 302))
		{
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			$response = $this->execCurlRedir($ch, $fullResp);

		}
		elseif ($http_code === 401)
		{
			$this->log->add('Invalid access token provided', E_USER_WARNING);
		}
		elseif ($http_code === 200)
		{
			$this->log->add(__METHOD__ . " was SUCCESSFUL");
		}

		curl_close($ch);

		//note deprecated
		if($opts['json'] && is_string($response))
		{
			$response = json_decode($response, 1) ?? [
				'description' => 'cURL is failed convert to JSON array in ' . __METHOD__ . __LINE__,
				'curlInfo' => $this->curlInfo
			];
			$this->log->add(__METHOD__ . " response must be JSON string", null, [$response]);
		}

		return $response;
	} // execCurl


	// todo
	function execCurlRedir($ch, $fullResp=null)
	{
		static $curl_loops = 0;
		static $curl_max_loops = 20;

		if (++$curl_loops >= $curl_max_loops)
		{
			$curl_loops = 0;
			return false;
		}

		$fullResp = $fullResp ?? $this->splitResponse(curl_exec($ch));

		list('headers'=>&$respHeaders, 'body'=>&$response) = $fullResp;

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if (
			isset($respHeaders['location'])
			&& ($http_code === 301 || $http_code === 302)
			&& count($url = parse_url($respHeaders['location']))
		)
		{
			$last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));

			foreach(['scheme','host','path'] as $p)
			{
				$url[$p] = $url[$p] ?? $last_url[$p] ?? '';
			}

			$new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . (empty($url['query']) ? '': '?' . $url['query']);

			curl_setopt($ch, CURLOPT_URL, $new_url);

			return $this->execCurlRedir($ch);
		}
		else
		{
			$curl_loops = 0;
			return $response;
		}
	}

	// not use
	private function chunkedCallback($ch, $str){
		$this->response .= $str;
		return strlen($str);
	}
} //* Curl
