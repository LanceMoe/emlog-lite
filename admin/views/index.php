<?php if (!defined('EMLOG_ROOT')) {
	exit('error!');
} ?>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">欢迎，<?php echo $user_cache[UID]['name'] ?></h1>
		<?php doAction('adm_main_top'); ?>
    </div>
<?php if (ROLE == ROLE_ADMIN): ?>
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <h6 class="card-header">站点信息</h6>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">文章
                            <a href="./article.php"><span class="badge badge-primary badge-pill"><?php echo $sta_cache['lognum']; ?></span></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">草稿
                            <a href="./article.php?draft=1"><span class="badge badge-primary badge-pill"><?php echo $sta_cache['draftnum']; ?></span></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">评论
                            <a href="./comment.php"><span class="badge badge-primary badge-pill"><?php echo $sta_cache['comnum_all']; ?></span></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">待审评论
                            <a href="./comment.php?hide=y"><span class="badge badge-warning badge-pill"><?php echo $sta_cache['hidecomnum']; ?></span></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">用户数
                            <a href="./user.php"><span class="badge badge-warning badge-pill"><?php echo count($user_cache); ?></span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <h6 class="card-header">软件信息</h6>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            PHP
                            <span><?php echo $php_ver; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            MySQL
                            <span><?php echo $mysql_ver; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Web Server
                            <span><?php echo $serverapp; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            EMLOG <span class="badge badge-success"><?php echo Option::EMLOG_VERSION; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>