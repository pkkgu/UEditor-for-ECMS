UEditor for ECMS
================

UEditor深度整合帝国ECMS。UEditor提供非常完善的后端通信API接口，使得UE编辑器整合CMS项目相当简单方便。所有图片、附件、视频等文件存放目录与后台数据记录，都延续使用ECMS的存放方式。本项目将持续已插件形式更新最新的UEditor和ECMS。
 

### 使用说明

- 下载UEditor编辑器(PHP版) [[下载地址]](http://ueditor.baidu.com/website/download.html "UEditor编辑器下载地址")
- 上传到帝国/e/extend/目录下
- 使用本项目上controller.php文件，替换编辑器自带的PHP文件（目录/e/extend/ueditor/php/controller.php）
- 修改帝国CMS字段HTML，替换为以下代码 [[字段管理方法]](http://www.phome.net/doc/manual/mod/html/field.html "帝国CMS字段管理方法")
```php
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
```
![部署成功](_images/ECMS-for-UEditor.jpg)

#编辑内容展示
- 修改内容模板加入下面代码（注意：#newstext为前台编辑器容器ID）[[内容模板管理方法]](http://www.phome.net/doc/manual/template/html/newstemp.html "帝国CMS内容模板管理方法")
```javascript
<script src="/e/extend/ueditor/ueditor.parse.min.js"></script>
<script>uParse('#newstext', {rootPath: '/e/extend/ueditor/'})</script>
<!-- 前台显示编辑器字段 -->
<div id="newstext">[!--newstext--]</div>
```
![部署成功](_images/show_temp.jpg)

### 说明
- controller.php 后端处理文件
- Field_html.php 帝国CMS字段HTML
- GBK版本需要服务器支持iconv函数
- 附件存放目录已经整合帝国CMS系统的配置