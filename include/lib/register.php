<?php
/**
 * register check
 * @package EMLOG (www.emlog.net)
 */

class Register {

	/**
	 * Check user is registered on the local side
	 */
	public static function isRegLocal() {
		$CACHE = Cache::getInstance();
		$options_cache = $CACHE->readCache('options');
		$emkey = $options_cache['emkey'] ?? '';

		if (empty($emkey)) {
			return false;
		}
		return true;
	}

	/**
	 * Check user is registered on the server side
	 */
	public static function isRegServer() {
		$CACHE = Cache::getInstance();
		$options_cache = $CACHE->readCache('options');
		$emkey = $options_cache['emkey'] ?? '';
		return self::checkEmKey($emkey);
	}

	/**
	 * check emkey
	 */
	public static function checkEmKey($emkey) {
		if (empty($emkey)) {
			return false;
		}

		$emcurl = new EmCurl();
		$emcurl->setPost(['emkey' => $emkey]);
		$emcurl->request(OFFICIAL_SERVICE_HOST . 'service/register');
		if ($emcurl->getHttpStatus() !== 200) {
			return false;
		}
		$respone = $emcurl->getRespone();
		$respone = json_decode($respone, 1);
		if ($respone['code'] !== 200) {
			$CACHE = Cache::getInstance();
			Option::updateOption('emkey', '');
			$CACHE->updateCache('options');
			return false;
		}

		return true;
	}

}
