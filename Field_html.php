<?php if(!isset($Field)){ ?>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.all.js"></script>
<script type="text/javascript" src="/e/extend/ueditor/lang/zh-cn/zh-cn.js"></script>
<?php } ?>
<?php
/**
 * ECMS UEditor编辑器字段配置
 * User: pkkgu 910111100@qq.com
 */
$Field    = 'newstext';
$FieldVal = $ecmsfirstpost==1?"":stripSlashes($r[$Field]);
$isadmin  = 0;
if($enews=='AddNews'||$enews=='EditNews')
{ $isadmin=1; }
else
{ $FieldVal  = empty($ecmsfirstpost)?DoReqValue($mid,$Field,$FieldVal):$r[$Field]; }
?>
<script id="<?=$Field?>" name="<?=$Field?>" type="text/plain"><?=$FieldVal?></script>
<script type="text/javascript">
var editor = UE.getEditor('<?=$Field?>',{
		pageBreakTag:'[!--empirenews.page--]' // 分页符
		//，toolbars:[['FullScreen', 'Source', 'Undo', 'Redo','Bold','test']] //选择自己需要的工具按钮名称
	});
ue.ready(function(){
	ue.execCommand('serverparam', {'classid':'<?=$classid?>','filepass':'<?=$filepass?>','isadmin':'<?=$isadmin?>','userid':'<?=$isadmin?$logininid:$muserid?>','username':'<?=$isadmin?$loginin:$musername?>','rnd':'<?=$isadmin?$loginrnd:$mrnd?>'});
});
</script>
