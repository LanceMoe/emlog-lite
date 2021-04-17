<?php if (!defined('EMLOG_ROOT')) {
	exit('error!');
} ?>
<?php if (isset($_GET['active_del'])): ?>
    <div class="alert alert-success">删除成功</div><?php endif; ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">资源管理</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-success shadow-sm" data-toggle="modal" data-target="#exampleModal"><i class="icofont-plus"></i> 上传图片/文件</a>
</div>

<div class="card-columns">
	<?php foreach ($attach as $key => $value):
		$extension = strtolower(substr(strrchr($value['filepath'], "."), 1));
		$atturl = BLOG_URL . substr($value['filepath'], 3);
		$name =  $value['filename'];
		if (in_array($extension, array('gif', 'jpg', 'jpeg', 'png'))) {
			$imgpath = $value['filepath'];
			if (isset($value['thum_filepath'])) {
				$imgpath = BLOG_URL . substr($value['thum_filepath'], 3);
			}
		} else {
			$imgpath = "./views/images/fnone.png";
		}
		?>
        <div class="card">
            <a href="<?php echo $atturl; ?>" target="_blank" title="<?php echo $name; ?>">
                <img class="card-img-top" src="<?php echo $imgpath; ?>" style="height: 150px;object-fit: cover;"/>
            </a>
            <div class="card-body">
                <p class="card-text text-muted small">
                    时间：<?php echo $value['addtime']; ?><br>
                    大小：<?php echo $value['attsize']; ?>
					<?php if ($value['width'] && $value['height']): ?>
                        尺寸：<?php echo $value['width'] ?>x<?php echo $value['height'] ?>
					<?php endif; ?>
                </p>
                <p class="card-text">
                    <a href="javascript: em_confirm(<?php echo $value['aid']; ?>, 'media', '<?php echo LoginAuth::genToken(); ?>');" class="badge badge-danger">删除</a>
                </p>
            </div>
        </div>
	<?php endforeach; ?>
</div>
<div class="page my-5"><?php echo $pageurl; ?> （有 <?php echo $count; ?> 个资源）</div>

<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">上传图片/文件</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="./media.php?action=upload_multi" class="dropzone" id="my-awesome-dropzone"></form>
            </div>

        </div>
    </div>
</div>
<script src="./views/js/dropzone.min.js"></script>
<script>
    $("#menu_media").addClass('active');
    setTimeout(hideActived, 3600);
    $('#exampleModal').on('hidden.bs.modal', function (e) {
        window.location.reload();
    })
</script>
