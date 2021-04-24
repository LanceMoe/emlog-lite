<?php
/**
 * links
 * @package EMLOG (www.emlog.net)
 */

/**
 * @var string $action
 * @var object $CACHE
 */

require_once 'globals.php';

if (empty($action)) {
	$emcurl = new EmCurl();
	$emcurl->setPost(['emkey' => Option::get('emkey'), 'ver'   => Option::EMLOG_VERSION,]);
	$emcurl->request(OFFICIAL_SERVICE_HOST . 'service/store');
	$retStatus = $emcurl->getHttpStatus();
	if ($retStatus !== 200) {
		emDirect("./store.php?action=error&error=1");
	}
	$respone = $emcurl->getRespone();
	$ret = json_decode($respone, 1);
	if (empty($ret)) {
		emDirect("./store.php?action=error&error=1");
	}
	if ($ret['code'] === 1001) {
		emDirect("./store.php?action=error&error_unreg=1");
	}

	$templates = $ret['data']['templates'] ?? [];
	$plugins = $ret['data']['plugins'] ?? [];

	include View::getView('header');
	require_once(View::getView('store'));
	include View::getView('footer');
	View::output();
}

if ($action === 'error') {
	include View::getView('header');
	require_once(View::getView('store'));
	include View::getView('footer');
	View::output();
}

if ($action === 'install') {
	$source = isset($_GET['source']) ? trim($_GET['source']) : '';
	$source_type = isset($_GET['type']) ? trim($_GET['type']) : '';
	if (empty($source)) {
		emDirect("./store.php?error_param=1");
	}

	$temp_file = emFecthFile(OFFICIAL_SERVICE_HOST . $source);
	if (!$temp_file) {
		emDirect("./store.php?error_down=1");
	}

	$unzip_path = $source_type == 'tpl' ? '../content/templates/' : '../content/plugins/';
	$ret = emUnZip($temp_file, $unzip_path, $source_type);
	@unlink($temp_file);
	switch ($ret) {
		case 0:
			emDirect("./store.php?active=1");
		case 1:
		case 2:
			emDirect("./store.php?error_dir=1");
		case 3:
			emDirect("./store.php?error_zip=1");
	}
}
