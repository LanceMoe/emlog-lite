<?php
/**
 * 站点首页模板
 */
if (!defined('EMLOG_ROOT')) {
    exit('error!');
}
?>
    <div class="col-lg-8 col-md-10 mx-auto">
        <?php
        if (!empty($logs)):
            foreach ($logs as $value):
                ?>
                <div class="post-preview">
                    <a href="post.html">
                        <h2 class="post-title">
                            <a href="<?php echo $value['log_url']; ?>"><?php echo $value['log_title']; ?></a><?php topflg($value['top'], $value['sortop'], isset($sortid) ? $sortid : ''); ?>
                        </h2>
                        <h3 class="post-subtitle">
                            <?php echo $value['log_description']; ?>
                        </h3>
                    </a>
                    <p class="post-meta">Posted by <?php blog_author($value['author']); ?> on <?php echo gmdate('Y-n-j', $value['date']); ?></p>
                    <p class="tag"><?php blog_tag($value['logid']); ?></p>
                    <p class="count">
                        <a href="<?php echo $value['log_url']; ?>#comments">评论(<?php echo $value['comnum']; ?>)</a>
                        <a href="<?php echo $value['log_url']; ?>">浏览(<?php echo $value['views']; ?>)</a>
                    </p>
                </div>
                <hr>
            <?php
            endforeach;
        else:
            ?>
            <h2>未找到</h2>
            <p>抱歉，没有符合您查询条件的结果。</p>
        <?php endif; ?>

        <!-- Pager -->
        <div class="clearfix">
            <?php echo $page_url; ?>
        </div>
    </div>

<?php
include View::getView('side');
include View::getView('footer');
?>