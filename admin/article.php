<?php
/**
 * The article management
 *
 * @package EMLOG (www.emlog.net)
 */

/**
 * @var string $action
 * @var object $CACHE
 */

require_once 'globals.php';

$Log_Model = new Log_Model();
$Tag_Model = new Tag_Model();
$Sort_Model = new Sort_Model();
$User_Model = new User_Model();

if (empty($action)) {
	$pid = $_GET['pid'] ?? '';
	$tagId = isset($_GET['tagid']) ? (int)$_GET['tagid'] : '';
	$sid = isset($_GET['sid']) ? (int)$_GET['sid'] : '';
	$uid = isset($_GET['uid']) ? (int)$_GET['uid'] : '';
	$keyword = isset($_GET['keyword']) ? addslashes($_GET['keyword']) : '';
	$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$checked = isset($_GET['checked']) ? addslashes($_GET['checked']) : '';

	$sortView = (isset($_GET['sortView']) && $_GET['sortView'] == 'ASC') ? 'DESC' : 'ASC';
	$sortComm = (isset($_GET['sortComm']) && $_GET['sortComm'] == 'ASC') ? 'DESC' : 'ASC';
	$sortDate = (isset($_GET['sortDate']) && $_GET['sortDate'] == 'DESC') ? 'ASC' : 'DESC';

	$sqlSegment = '';
	if ($tagId) {
		$blogIdStr = $Tag_Model->getTagById($tagId);
		$sqlSegment = "and gid IN ($blogIdStr)";
	} elseif ($sid) {
		$sqlSegment = "and sortid=$sid";
	} elseif ($uid) {
		$sqlSegment = "and author=$uid";
	} elseif ($checked) {
		$sqlSegment = "and checked='$checked'";
	} elseif ($keyword) {
		$sqlSegment = "and title like '%$keyword%'";
	}
	$sqlSegment .= ' ORDER BY ';
	if (isset($_GET['sortView'])) {
		$sqlSegment .= "views $sortView";
	} elseif (isset($_GET['sortComm'])) {
		$sqlSegment .= "comnum $sortComm";
	} elseif (isset($_GET['sortDate'])) {
		$sqlSegment .= "date $sortDate";
	} else {
		$sqlSegment .= 'top DESC, sortop DESC, date DESC';
	}

	$hide_state = $pid ? 'y' : 'n';
	if ($pid == 'draft') {
		$hide_stae = 'y';
		$sorturl = '&pid=draft';
	} else {
		$hide_stae = 'n';
		$sorturl = '';
	}

	$logNum = $Log_Model->getLogNum($hide_state, $sqlSegment, 'blog', 1);
	$logs = $Log_Model->getLogsForAdmin($sqlSegment, $hide_state, $page);
	$sorts = $CACHE->readCache('sort');
	$log_cache_tags = $CACHE->readCache('logtags');
	$tags = $Tag_Model->getTag();

	$subPage = '';
	foreach ($_GET as $key => $val) {
		$subPage .= $key != 'page' ? "&$key=$val" : '';
	}
	$pageurl = pagination($logNum, Option::get('admin_perpage_num'), $page, "article.php?{$subPage}&page=");

	include View::getView('header');
	require_once View::getView('article');
	include View::getView('footer');
	View::output();
}

if ($action == 'operate_log') {
	$operate = $_REQUEST['operate'] ?? '';
	$pid = $_POST['pid'] ?? '';
	$logs = isset($_POST['blog']) ? array_map('intval', $_POST['blog']) : array();
	$sort = isset($_POST['sort']) ? (int)$_POST['sort'] : '';
	$author = isset($_POST['author']) ? (int)$_POST['author'] : '';
	$gid = isset($_GET['gid']) ? (int)$_GET['gid'] : '';

	LoginAuth::checkToken();

	if ($operate == '') {
		emDirect("./article.php?pid=$pid&error_b=1");
	}
	if (empty($logs) && empty($gid)) {
		emDirect("./article.php?pid=$pid&error_a=1");
	}

	switch ($operate) {
		case 'del':
			foreach ($logs as $val) {
				doAction('before_del_log', $val);
				$Log_Model->deleteLog($val);
				doAction('del_log', $val);
			}
			$CACHE->updateCache();
			if ($pid == 'draft') {
				emDirect("./article.php?pid=draft&active_del=1");
			} else {
				emDirect("./article.php?active_del=1");
			}
			break;
		case 'top':
			foreach ($logs as $val) {
				$Log_Model->updateLog(array('top' => 'y'), $val);
			}
			emDirect("./article.php?active_up=1");
			break;
		case 'sortop':
			foreach ($logs as $val) {
				$Log_Model->updateLog(array('sortop' => 'y'), $val);
			}
			emDirect("./article.php?active_up=1");
			break;
		case 'notop':
			foreach ($logs as $val) {
				$Log_Model->updateLog(array('top' => 'n', 'sortop' => 'n'), $val);
			}
			emDirect("./article.php?active_down=1");
			break;
		case 'hide':
			foreach ($logs as $val) {
				$Log_Model->hideSwitch($val, 'y');
			}
			$CACHE->updateCache();
			emDirect("./article.php?active_hide=1");
			break;
		case 'pub':
			foreach ($logs as $val) {
				$Log_Model->hideSwitch($val, 'n');
				if (ROLE == ROLE_ADMIN) {
					$Log_Model->checkSwitch($val, 'y');
				}
			}
			$CACHE->updateCache();
			emDirect("./article.php?pid=draft&active_post=1");
			break;
		case 'move':
			foreach ($logs as $val) {
				$Log_Model->updateLog(array('sortid' => $sort), $val);
			}
			$CACHE->updateCache(array('sort', 'logsort'));
			emDirect("./article.php?active_move=1");
			break;
		case 'change_author':
			if (ROLE != ROLE_ADMIN) {
				emMsg('权限不足！', './');
			}
			foreach ($logs as $val) {
				$Log_Model->updateLog(array('author' => $author), $val);
			}
			$CACHE->updateCache('sta');
			emDirect("./article.php?active_change_author=1");
			break;
		case 'check':
			if (ROLE != ROLE_ADMIN) {
				emMsg('权限不足！', './');
			}
			$Log_Model->checkSwitch($gid, 'y');
			$CACHE->updateCache();
			emDirect("./article.php?active_ck=1");
			break;
		case 'uncheck':
			if (ROLE != ROLE_ADMIN) {
				emMsg('权限不足！', './');
			}
			$Log_Model->checkSwitch($gid, 'n');
			$CACHE->updateCache();
			emDirect("./article.php?active_unck=1");
			break;
	}
}

//显示撰写文章页面
if ($action === 'write') {
	$blogData = [
		'logid'    => -1,
		'title'    => '',
		'content'  => '',
		'excerpt'  => '',
		'alias'    => '',
		'author'   => '',
		'sortid'   => -1,
		'type'     => 'blog',
		'password' => '',
		'hide'     => '',
		'author'   => UID,
	];

	extract($blogData);

	$isdraft = false;
	$containertitle = '写文章';
	$orig_date = '';
	$sorts = $CACHE->readCache('sort');
	$tagStr = '';
	$is_top = '';
	$is_sortop = '';
	$is_allow_remark = '';
	$postDate = date('Y-m-d H:i:s');
	$att_frame_url = 'attachment.php?action=selectFile';

	include View::getView('header');
	require_once View::getView('article_write');
	include View::getView('footer');
	View::output();
}

//显示编辑文章页面
if ($action === 'edit') {
	$logid = isset($_GET['gid']) ? (int)$_GET['gid'] : '';
	$blogData = $Log_Model->getOneLogForAdmin($logid);
	extract($blogData);

	$isdraft = $hide == 'y' ? true : false;
	$containertitle = $isdraft ? '编辑草稿' : '编辑文章';
	$postDate = date('Y-m-d H:i:s', $date);
	$sorts = $CACHE->readCache('sort');
	//log tag
	$tags = array();
	foreach ($Tag_Model->getTag($logid) as $val) {
		$tags[] = $val['tagname'];
	}
	$tagStr = implode(',', $tags);
	//old tag
	$tags = $Tag_Model->getTag();

	$is_top = $top == 'y' ? 'checked="checked"' : '';
	$is_sortop = $sortop == 'y' ? 'checked="checked"' : '';
	$is_allow_remark = $allow_remark == 'y' ? 'checked="checked"' : '';

	$att_frame_url = 'attachment.php?action=attlib&logid=' . $logid;

	include View::getView('header');
	require_once View::getView('article_write');
	include View::getView('footer');
	View::output();
}
