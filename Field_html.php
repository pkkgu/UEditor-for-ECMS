<?php if(empty($Field)){ ?>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.all.js"></script>
<script type="text/javascript" src="/e/extend/ueditor/lang/zh-cn/zh-cn.js"></script>
<?php } ?>
<?php
/**
 * User: pkkgu 910111100@qq.com
 * ECMS UEditor编辑器字段配置
 *
 * @param $isadmin  int    前后台控制
 * @param $Field    string 字段名称
 * @param $FieldVal string 字段内容
 *
 */
$isadmin  = 1; //0前台，1后台
$Field    = 'newstext';
$FieldVal = $ecmsfirstpost==1?"":stripSlashes($r[$Field]);
if(empty($isadmin)&&empty($ecmsfirstpost)) //前台修改信息时
{
	$FieldVal=DoReqValue($mid,$Field,$FieldVal);
}
?>
<script id="<?=$Field?>" name="<?=$Field?>" type="text/plain"><?=$FieldVal?></script>
<script type="text/javascript">
	var editor = UE.getEditor('<?=$Field?>');
	ue.ready(function(){
		ue.execCommand('serverparam', {
			'classid' : '<?=$classid?>',
			'filepass': '<?=$filepass?>',
			'isadmin' : '<?=$isadmin?>',
			'userid'  : '<?=$logininid?>',
			'username': '<?=$loginin?>',
			'rnd'     : '<?=$loginrnd?>'
		});
	});
</script>



