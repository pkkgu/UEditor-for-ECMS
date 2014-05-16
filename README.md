ECMS-for-UEditor
================

帝国ECMS7.0深度整合百度编辑器UEditor 1.4.2

### 使用说明

- 下载UEditor编辑器 https://github.com/fex-team/ueditor/releases
- 上传到帝国/e/extend/目录下
- 使用本项目上的controller.php文件，替换编辑器自带的PHP文件，目录/e/extend/ueditor/php/controller.php
- 修改帝国编辑器字端HTML，替换为以下代码（注意$isadmin前后台配置）
```php
<?php if(!isset($Field)){ ?>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/e/extend/ueditor/ueditor.all.js"></script>
<?php } ?>
<?php
/**
 * ECMS UEditor编辑器字段配置
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
	pageBreakTag:'[!--empirenews.page--]' // 分页符
	//,toolbars:[['FullScreen', 'Source', 'Undo', 'Redo','Bold]] //选择自己需要的工具按钮名称
});
ue.ready(function(){
	ue.execCommand('serverparam', {
		'classid' : '<?=$classid?>',
		'filepass': '<?=$filepass?>',
		'isadmin' : '<?=$isadmin?>',
		'userid'  : '<?=$isadmin?$logininid:$muserid?>',
		'username': '<?=$isadmin?$loginin:$musername?>',
		'rnd'     : '<?=$isadmin?$loginrnd:$mrnd?>'
	});
});
</script>
```

### 说明
- controller.php 后端处理文件
- Field_html.php 帝国CMS字段HTML
- GBK版本需要服务器支持iconv函数（GBK）
- 附件地址目录已经整合帝国系统的配置