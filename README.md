ECMS-for-UEditor
================

帝国ECMS7.0
百度编辑器UEditor 1.4.0

### 一、文件说明
1.controller.php 后端处理文件
2.Field_html.php 帝国CMS字端HTML

### 二、使用说明

```<?php if(empty($Field)){ ?>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.all.js"></script>
<script type="text/javascript" src="/e/extend/ueditor/lang/zh-cn/zh-cn.js"></script>
<?php } ?>
<?php
/**
 * ECMS UEditor编辑器字段配置
 * User: pkkgu 910111100@qq.com
 * Date: 2014年5月10日
 *
 * @param $isadmin  int    前后台控制
 * @param $Field    string 字段名称
 * @param $FieldVal string 字段内容
 *
 */
$isadmin  = 1; //0前台，1后台
$Field    = 'newstext';
$FieldVal = $ecmsfirstpost==1?"":stripSlashes($r[$Field]);
if(empty($isadmin))
{
	$logininid = $muserid;
	$loginin   = $musername;
	$loginrnd  = $mrnd;
	$FieldVal = empty($ecmsfirstpost)?DoReqValue($mid,$Field,$FieldVal):$r[$Field];
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
</script>```