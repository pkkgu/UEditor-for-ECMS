<?php
/**
 * ECMS for UEditor 前后端互交上传处理文件
 * User: pkkgu 910111100@qq.com
 * Date: 2014年5月16日
 * ECMS 7.0
 * UEditor 1.4.0
 *
 * @param $classid   int
 * @param $filepass  int    增加信息时为时间戳，修改信息为信息ID
 * @param $isadmin   int    前后台控制,0前台、1后台
 * @param $userid    int
 * @param $username  string
 * @param $rnd       string
 *
 * @param $Field     string 字段名称
 * @param $FieldVal  string 字段内容
 *
	
	帝国数据表 字段HTML
	<?php if(!isset($Field)){ ?>
	<script type="text/javascript" src="/e/extend/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" src="/e/extend/ueditor/ueditor.all.js"></script>
	<?php } ?>
	<?php
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
		//,toolbars:[['FullScreen', 'Source', 'Undo', 'Redo','Bold']] //选择自己需要的工具按钮名称
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
 */
require('../../../class/connect.php'); //引入数据库配置文件和公共函数文件
require('../../../class/db_sql.php'); //引入数据库操作文件
require("../../../data/dbcache/class.php");

$link=db_connect(); //连接MYSQL
$empire=new mysqlquery(); //声明数据库操作类

// 必须参数
$action      = RepPostVar($_GET['action']);
$classid     = (int)$_GET['classid'];
$filepass    = (int)$_GET['filepass'];
// 用户信息
$isadmin     = (int)$_GET['isadmin'];
$userid      = (int)$_GET['userid'];
$username    = RepPostVar($_GET['username']);
$rnd         = RepPostVar($_GET['rnd']);
$loginin     = $isadmin?$username:'[Member]'.$username;
// 配置
$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);
if(empty($action))
{
    Ue_Print('请求类型不能明确');
}
else if($action!='config'&&(empty($classid)||empty($filepass)))
{
    Ue_Print("上传参数不正确！栏目ID：$classid，信息ID：$filepass，action：$action");
}
//获取配置
$pr=$empire->fetch1("select * from {$dbtbpre}enewspublic");
if(empty($isadmin)) // 重定义前台配置
{
    if($pr['addnews_ok']==1)
    {
        Ue_Print("网站投稿功能未开启");
    }
    else if(($action=='uploadimage'||$action=='uploadscrawl'||$action=='catchimage')&&empty($pr['qaddtran']))
    {
        Ue_Print("图片上传功能关闭");
    }
    else if(($action=='uploadvideo'||$action=='uploadfile')&&empty($pr['qaddtranfile']))
    {
        Ue_Print("附件上传功能关闭");
    }
	
	$cr=$empire->fetch1("select openadd,qaddgroupid from {$dbtbpre}enewsclass where classid='$classid'");
	if($cr['openadd']==1)
	{
        Ue_Print("栏目关闭投稿功能");
	}
	else if($action=='listimage'||$action=='listfile'||$cr['qaddgroupid']) //list文件、上传权限检测
	{
		if(empty($userid)||empty($username)||empty($rnd))
		{
			Ue_Print("请未登录");
		}
		$ur=$empire->fetch1("select userid,groupid from {$dbtbpre}enewsmember where userid='$userid' and username='$username' and rnd='$rnd'");
		if(empty($ur['userid']))
		{
			Ue_Print("请重新未登录");
		}
		if ($cr['qaddgroupid']&&!stristr($cr['qaddgroupid'],",".$ur['groupid'].","))
		{
			Ue_Print("您没有上传附件的权限");
		}
	}
    $qaddtransize = $pr['qaddtransize']*1024;
    $CONFIG['imageMaxSize'] = $qaddtransize;
    $CONFIG['scrawlMaxSize'] = $qaddtransize;
    $CONFIG['catcherMaxSize'] = $qaddtransize;
    $qaddtranimgtype = substr($pr['qaddtranimgtype'],1,strlen($pr['qaddtranimgtype'])-2);
    $qaddtranimgtype = explode('|',$qaddtranimgtype);
    $CONFIG['imageAllowFiles'] = $qaddtranimgtype;
    $CONFIG['imageManagerAllowFiles'] = $qaddtranimgtype;
    $CONFIG['catcherAllowFiles'] = $qaddtranimgtype;
    
    $qaddtranfilesize = $pr['qaddtranfilesize']*1024;
    $CONFIG['fileMaxSize'] = $qaddtranfilesize;
    $CONFIG['videoMaxSize'] = $qaddtranfilesize;
    $qaddtranfiletype = substr($pr['qaddtranfiletype'],1,strlen($pr['qaddtranfiletype'])-2);
    $qaddtranfiletype = explode('|',$qaddtranfiletype);
    $CONFIG['fileAllowFiles'] = $qaddtranfiletype;
    $CONFIG['fileManagerAllowFiles'] = $qaddtranfiletype;
    $CONFIG['videoAllowFiles'] = array(".flv",".swf",".mkv",".avi",".rm",".rmvb",".mpeg",".mpg",".ogg",".ogv",".mov",".wmv",".mp4",".webm",".mp3",".wav",".mid");
}
else if($isadmin==1) // 重定义后台配置
{
	if(empty($userid)||empty($username)||empty($rnd))
	{
		Ue_Print("请未登录");
	}
	$ur=$empire->fetch1("select userid from {$dbtbpre}enewsuser where userid='$userid' and username='$username' and rnd='$rnd'");
	if(empty($ur['userid']))
	{
		Ue_Print("请重新未登录");
	}
    $filesize = $pr['filesize']*1024;
    $CONFIG['imageMaxSize']   = $filesize;
    $CONFIG['scrawlMaxSize']  = $filesize;
    $CONFIG['catcherMaxSize'] = $filesize;
    $CONFIG['fileMaxSize']    = $filesize;
    $CONFIG['videoMaxSize']   = $filesize;
}

//目录
$classpath = ReturnFileSavePath($classid); //栏目附件目录
$timepath  = $classpath['filepath']."{yyyy}-{mm}-{dd}/{time}{rand:6}"; //日期栏目目录
// 重定义存放目录
$CONFIG['imagePathFormat']  = $timepath;
$CONFIG['scrawlPathFormat'] = $timepath;
$CONFIG['videoPathFormat']  = $timepath;
$CONFIG['filePathFormat']   = $timepath;
$CONFIG['catcherPathFormat']= $timepath;
//$CONFIG['imageManagerListPath'] = "/".$classpath['filepath'];
//$CONFIG['fileManagerListPath']  = "/".$classpath['filepath'];

switch ($action) {
	case 'config':
		$result = json_encode($CONFIG);
		break;

	/* 上传图片 */
	case 'uploadimage':
		$type=1;
		$result = include("action_upload.php");
		$result = Ue_File_Url($action,$result);
		break;

	/* 上传涂鸦 */
	case 'uploadscrawl':
		$type=1;
		$result = include("action_upload.php");
		$result = Ue_File_Url($action,$result);
		break;

	/* 上传视频 */
	case 'uploadvideo':
		$type=3;
		$result = include("action_upload.php");
		$result = Ue_File_Url($action,$result);
		break;

	/* 上传文件 */
	case 'uploadfile':
		$type=0;
		$result = include("action_upload.php");
		$result = Ue_File_Url($action,$result);
		break;

	/* 列出图片 */
	case 'listimage':
		$result = action_list($classid,$username);
		//$result = include("action_list.php");
		break;
	/* 列出文件 */
	case 'listfile':
		$result = action_list($classid,$username);
		//$result = include("action_list.php");
		break;

	/* 抓取远程文件 */
	case 'catchimage':
		$type=1;
		$result = include("action_crawler.php");
		$result = Ue_File_Url($action,$result);
		break;

	default:
		$result = json_encode(array('state'=> '请求地址出错'));
		break;
}

/*
 * 写入数据库
 * eInsertFileTable(文件名、文件大小，存放日期目录，上传者，栏目id,文件编号,文件类型,信息ID,文件临时识别编号(原文件名称),文件存放目录方式,信息公共ID,归属类型,附件副表ID)
 * 1.文件类型:1为图片，2为Flash文件，3为多媒体文件，0为附件
 * 2.归属类型:0信息，4反馈，5公共，6会员，其他
 * 3.文件临时识别编号:0非垃圾信息
 * 4.文件存放目录方式:0为栏目目录，1为/d/file/p目录，2为/d/file目录
 *
 */
$file_r   = json_decode($result,true);
if(($action=="uploadimage"||$action=="uploadscrawl"||$action=="uploadvideo"||$action=="uploadfile")&&$file_r['state']=="SUCCESS")
{
	$title    = RepPostStr(trim($file_r[title]));
	$filesize = (int)$file_r[size];
	$filepath = date("Y-m-d");
	$username = RepPostStr(trim($loginin));
	$classid  = (int)$classid;
	$original = RepPostStr(trim($file_r[original]));
	$type     = (int)$type;
	$filepass = (int)$filepass;
	eInsertFileTable($title,$filesize,$filepath,$username,$classid,$original,$type,$filepass,$filepass,$public_r[fpath],0,0,0);
	// 反馈附件入库
	//eInsertFileTable($tfr[filename],$filesize,$filepath,'[Member]'.$username,$classid,'[FB]'.addslashes(RepPostStr($add[title])),$type,$filepass,$filepass,$public_r[fpath],0,4,0);
}

/* 输出结果 */
if (isset($_GET["callback"])) {
    echo $_GET["callback"] . '(' . $result . ')';
} else {
    echo $result;
}

db_close();
$empire=null;
exit();

// Error提示
function Ue_Print($msg="SUCCESS"){
    echo '{"state": "'.$msg.'"}';
    db_close();
    $empire=null;
    exit();
}

// 修正附件绝对路径
function Ue_File_Url($action,$result){
	global $public_r;
	$result = json_decode($result,true);
	if($action=='catchimage') //保存远程图片
	{
		for($i;$i<count($result['list']);$i++)
		{
			$result['list'][$i]['url']=$public_r['newsurl'].$result['list'][$i]['url'];
		}
	}
	else
	{
		$result['url']=$public_r['newsurl'].$result['url'];
	}
	return json_encode($result);
}
// 列出已经上传的文件
function action_list($classid,$username){
	global $empire,$public_r,$class_r,$dbtbpre,$public_r;
	$action=$_GET['action'];
	$classid= (int)$_GET['classid'];
	$list=array();
	$result = json_encode(array("state" => "no match file","list" => $list,"start" => 0,"total" => 0));
	
	$where = "";
	if($action=='listimage') //图片 
	{
		$where= ' and type=1';
	}
	else if($action=='listfile') //附件
	{
		$where= ' and type!=1';
	}
	else
	{
		return $result;
	}
	$size=(int)$_GET['size'];
	$start=(int)$_GET['start'];
	$limit=$start.",".$size;
	// 统计总数
	$total=$empire->gettotal("select count(*) as total from {$dbtbpre}enewsfile_1 where adduser='$username'".$where);
	$sql=$empire->query("select * from {$dbtbpre}enewsfile_1 where adduser='$username'".$where." limit ".$limit);
	$bqno=0;
	while($r=$empire->fetch($sql))
	{
		$classpath = ReturnFileSavePath($r['classid']);
		$list[$bqno]['url'] = $public_r['newsurl'].$classpath['filepath'].$r['path'].'/'.$r['filename'];
		$list[$bqno]['mtime'] =$r['filetime'];
		$bqno++;
	}
	/* 返回数据 */
	if (!count($list)) {
		return $result;
	}
	return $result = json_encode(array(
		"state" => "SUCCESS",
		"list" => $list,
		"start" => $start,
		"total" => $total
	));
}
