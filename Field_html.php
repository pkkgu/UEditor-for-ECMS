<?php if(!isset($Field)){ ?>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.all.min.js"></script>
<?php } ?>
<?php
/**
 * UEditor for ECMS编辑器字段配置 
 * User: pkkgu 910111100@qq.com
 */
$Field    = 'newstext'; //*字段名称
$FieldVal = $ecmsfirstpost==1?"":stripSlashes($r[$Field]);
$isadmin  = 0;
if($enews=='AddNews'||$enews=='EditNews')
{ $isadmin=1; }
else
{ $FieldVal  = empty($ecmsfirstpost)?DoReqValue($mid,$Field,$FieldVal):$r[$Field]; }
?>
<script id="<?=$Field?>" name="<?=$Field?>" type="text/plain"><?=$FieldVal?></script>
<script type="text/javascript">
var ue = UE.getEditor('<?=$Field?>',{
	pageBreakTag:'[!--empirenews.page--]' //分页符
	, serverUrl: "/e/extend/ueditor/php/controller.php?isadmin=<?=$isadmin?>"
	//,toolbars:[['FullScreen', 'Source', 'Undo', 'Redo','Bold']] //选择自己需要的工具按钮名称
});
ue.ready(function(){
	ue.execCommand('serverparam', {
		'classid' : '<?=$classid?>',
		'filepass': '<?=$filepass?>',
		'userid'  : '<?=$isadmin?$logininid:$muserid?>',
		'username': '<?=$isadmin?$loginin:$musername?>',
		'rnd'     : '<?=$isadmin?$loginrnd:$mrnd?>'
	});
});
</script>
