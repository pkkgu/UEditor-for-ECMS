<?php if(empty($Field)){ ?>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.all.js"></script>
<script type="text/javascript" src="/e/extend/ueditor/lang/zh-cn/zh-cn.js"></script>
<?php } ?>
<?php
/**
 * ECMS UEditor编辑器字段配置
 * User: pkkgu 910111100@qq.com
 */
$isadmin  = 1; //0前台，1后台
$Field    = 'newstext';
$FieldVal = $ecmsfirstpost==1?"":stripSlashes($r[$Field]);
if(empty($isadmin))
{
	$logininid = $muserid;
	$loginin   = $musername;
	$loginrnd  = $mrnd;
	$FieldVal  = empty($ecmsfirstpost)?DoReqValue($mid,$Field,$FieldVal):$r[$Field];
}
?>
<script id="<?=$Field?>" name="<?=$Field?>" type="text/plain"><?=$FieldVal?></script>
<script type="text/javascript">
var editor = UE.getEditor('<?=$Field?>',{
		// 分页符
		pageBreakTag:'[!--empirenews.page--]'
		//这里可以选择自己需要的工具按钮名称,此处仅选择如下五个
		//，toolbars:[['FullScreen', 'Source', 'Undo', 'Redo','Bold','test']]
	});
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
