<?php
/**
<<<<<<< HEAD
 * UEditor for ECMS ǰ��˻����ϴ������ļ�
=======
 * UEditor for ECMS ǰ��˻����ϴ������ļ�
>>>>>>> 8e109d5702206c4797fcebef22b56453e4e16768
 * User: pkkgu 910111100@qq.com
 * Date: 2014��5��29��
 * ECMS 7.0
 * UEditor 1.4.3
 *
 * @param $classid   int
 * @param $filepass  int    ������ϢʱΪʱ������޸���ϢΪ��ϢID
 * @param $isadmin   int    ǰ��̨����,0ǰ̨��1��̨
 * @param $userid    int
 * @param $username  string
 * @param $rnd       string
 *
 * @param $Field     string �ֶ�����
 * @param $FieldVal  string �ֶ�����
 *
	
	�۹����ݱ� �ֶ�HTML
	<?php if(!isset($Field)){ ?>
	<script type="text/javascript" src="/e/extend/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" src="/e/extend/ueditor/ueditor.all.js"></script>
	<?php } ?>
	<?php
	$Field    = 'newstext'; //*�ֶ�����
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
		pageBreakTag:'[!--empirenews.page--]' //��ҳ��
		, serverUrl: "/e/extend/ueditor/php/controller.php?isadmin=<?=$isadmin?>"
		//,toolbars:[['FullScreen', 'Source', 'Undo', 'Redo','Bold']] //ѡ���Լ���Ҫ�Ĺ��߰�ť����
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
 */
require('../../../class/connect.php'); //�������ݿ������ļ��͹��������ļ�
require('../../../class/db_sql.php'); //�������ݿ�����ļ�
require("../../../data/dbcache/class.php");

$link=db_connect(); //����MYSQL
$empire=new mysqlquery(); //�������ݿ����

// �������
$action      = RepPostVar($_GET['action']);
$classid     = (int)$_GET['classid'];
$filepass    = (int)$_GET['filepass'];
// �û���Ϣ
$isadmin     = (int)$_GET['isadmin'];
$userid      = (int)$_GET['userid'];
$username    = RepPostVar($_GET['username']);
$username    = iconv("UTF-8","GB2312//IGNORE",$username);
$rnd         = RepPostVar($_GET['rnd']);
$loginin     = $isadmin?$username:'[Member]'.$username;
$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);

if(empty($action))
{
    Ue_Print('�������Ͳ�����ȷ');
}
else if($action!='config'&&(empty($classid)||empty($filepass)))
{
    Ue_Print("�ϴ���������ȷ����ĿID��".$classid."����ϢID��".$filepass."��action��".$action);
}
//��ȡ����
$pr=$empire->fetch1("select * from {$dbtbpre}enewspublic");
if(empty($isadmin)) // �ض���ǰ̨����
{
    if($action!='config')
	{
		if($pr['addnews_ok']==1)
		{
			Ue_Print("��վͶ�幦��δ����");
		}
		else if(($action=='uploadimage'||$action=='uploadscrawl'||$action=='catchimage')&&empty($pr['qaddtran']))
		{
			Ue_Print("ͼƬ�ϴ����ܹر�");
		}
		else if(($action=='uploadvideo'||$action=='uploadfile')&&empty($pr['qaddtranfile']))
		{
			Ue_Print("�����ϴ����ܹر�");
		}
		
		$cr=$empire->fetch1("select openadd,qaddgroupid from {$dbtbpre}enewsclass where classid='$classid'");
		if($cr['openadd']==1)
		{
			Ue_Print("��Ŀ�ر�Ͷ�幦��");
		}
		else if($action=='listimage'||$action=='listfile'||$cr['qaddgroupid']) //list�ļ����ϴ�Ȩ�޼��
		{
			if(empty($userid)||empty($username)||empty($rnd))
			{
				Ue_Print("��δ��¼");
			}
			$ur=$empire->fetch1("select userid,groupid from {$dbtbpre}enewsmember where userid='$userid' and username='$username' and rnd='$rnd'");
			if(empty($ur['userid']))
			{
				Ue_Print("������δ��¼");
			}
			if ($cr['qaddgroupid']&&!stristr($cr['qaddgroupid'],",".$ur['groupid'].","))
			{
				Ue_Print("��û���ϴ�������Ȩ��");
			}
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
else if($isadmin==1) // �ض����̨����
{
    if($action!='config')
	{
		if(empty($userid)||empty($username)||empty($rnd))
		{
			Ue_Print("��δ��¼");
		}
		$ur=$empire->fetch1("select userid from {$dbtbpre}enewsuser where userid='$userid' and username='$username' and rnd='$rnd'");
		if(empty($ur['userid']))
		{
			Ue_Print("������δ��¼");
		}
	}
    $filesize = $pr['filesize']*1024;
    $CONFIG['imageMaxSize']   = $filesize;
    $CONFIG['scrawlMaxSize']  = $filesize;
    $CONFIG['catcherMaxSize'] = $filesize;
    $CONFIG['videoMaxSize']   = $filesize;
    $CONFIG['fileMaxSize']    = $filesize;
}

//Ŀ¼
$classpath = ReturnFileSavePath($classid); //��Ŀ����Ŀ¼
$timepath  = $classpath['filepath']."{yyyy}-{mm}-{dd}/{time}{rand:6}"; //������ĿĿ¼
// �ض�����Ŀ¼
$CONFIG['imagePathFormat']      = $timepath;
$CONFIG['scrawlPathFormat']     = $timepath;
$CONFIG['snapscreenPathFormat'] = $timepath;
$CONFIG['videoPathFormat']      = $timepath;
$CONFIG['filePathFormat']       = $timepath;
$CONFIG['catcherPathFormat']    = $timepath;
// ǰ׺��ȫ
$CONFIG['imageUrlPrefix']       = $public_r['newsurl'];
$CONFIG['scrawlUrlPrefix']      = $public_r['newsurl'];
$CONFIG['snapscreenUrlPrefix']  = $public_r['newsurl'];
$CONFIG['catcherUrlPrefix']     = $public_r['newsurl'];
$CONFIG['videoUrlPrefix']       = $public_r['newsurl'];
$CONFIG['fileUrlPrefix']        = $public_r['newsurl'];

//$CONFIG['imageManagerListPath'] = $public_r['newsurl'].$classpath['filepath'];
//$CONFIG['fileManagerListPath']  = $public_r['newsurl'].$classpath['filepath'];

switch ($action) {
	case 'config':
		$result = json_encode($CONFIG);
		break;

	/* �ϴ�ͼƬ */
	case 'uploadimage':
		$type=1;
		$result = include("action_upload.php");
		break;

	/* �ϴ�Ϳѻ */
	case 'uploadscrawl':
		$type=1;
		$result = include("action_upload.php");
		break;

	/* �ϴ���Ƶ */
	case 'uploadvideo':
		$type=3;
		$result = include("action_upload.php");
		break;

	/* �ϴ��ļ� */
	case 'uploadfile':
		$type=0;
		$result = include("action_upload.php");
		break;

	/* �г�ͼƬ */
	case 'listimage':
		$result = action_list($classid,$username);
		//$result = include("action_list.php");
		break;
	/* �г��ļ� */
	case 'listfile':
		$result = action_list($classid,$username);
		//$result = include("action_list.php");
		break;

	/* ץȡԶ���ļ� */
	case 'catchimage':
		$type=1;
		$result = include("action_crawler.php");
        break;

    default:
		$result = json_encode(array('state'=> '�����ַ����'));
        break;
}
/*
 * д�����ݿ�
 * eInsertFileTable(�ļ������ļ���С���������Ŀ¼���ϴ��ߣ���Ŀid,�ļ����,�ļ�����,��ϢID,�ļ���ʱʶ����(ԭ�ļ�����),�ļ����Ŀ¼��ʽ,��Ϣ����ID,��������,��������ID)
 * 1.�ļ�����:1ΪͼƬ��2ΪFlash�ļ���3Ϊ��ý���ļ���0Ϊ����
 * 2.��������:0��Ϣ��4������5������6��Ա������
 * 3.�ļ���ʱʶ����:0��������Ϣ
 * 4.�ļ����Ŀ¼��ʽ:0Ϊ��ĿĿ¼��1Ϊ/d/file/pĿ¼��2Ϊ/d/fileĿ¼
 *
 */
if($action=="uploadimage"||$action=="uploadscrawl"||$action=="uploadvideo"||$action=="uploadfile"||$action=="catchimage")
{
	$file_r   = json_decode($result,true);
	$filepath = date("Y-m-d");
	$username = RepPostStr(trim($loginin));
	$classid  = (int)$classid;
	$type     = (int)$type;
	$filepass = (int)$filepass;
	if($action=="catchimage") //Զ�̱���д���ݿ�
	{
		for($i=0;$i<count($file_r['list']);$i++)
		{
			if($file_r['list'][$i]['state']=="SUCCESS")
			{
				$title    = RepPostStr(trim($file_r['list'][$i]['title']));
				$filesize = RepPostStr(trim($file_r['list'][$i]['size']));
				$original = RepPostStr(trim($file_r['list'][$i]['original']));
				$original = iconv("UTF-8","GB2312//IGNORE",$original);
				eInsertFileTable($title,$filesize,$filepath,$username,$classid,$original,$type,$filepass,$filepass,$public_r[fpath],0,0,0);
			}
		}
	}
	else if($file_r['state']=="SUCCESS")
	{
		$title    = RepPostStr(trim($file_r[title]));
		$filesize = RepPostStr(trim($file_r[size]));
		$original = RepPostStr(trim($file_r[original]));
		$original = iconv("UTF-8","GB2312//IGNORE",$original);
		eInsertFileTable($title,$filesize,$filepath,$username,$classid,$original,$type,$filepass,$filepass,$public_r[fpath],0,0,0);
	}
	// �����������
	//eInsertFileTable($tfr[filename],$filesize,$filepath,'[Member]'.$username,$classid,'[FB]'.addslashes(RepPostStr($add[title])),$type,$filepass,$filepass,$public_r[fpath],0,4,0);
}

/* ������ */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array('state'=> 'callback�������Ϸ�'));
    }
} else {
    echo $result;
}

db_close();
$empire=null;
exit();

// Error��ʾ
function Ue_Print($msg="SUCCESS"){
    echo '{"state": "'.$msg.'"}';
    db_close();
    $empire=null;
    exit();
}
// �г��Ѿ��ϴ����ļ�
function action_list($classid,$username){
	global $empire,$class_r,$dbtbpre,$public_r;
	$action=$_GET['action'];
	$classid= (int)$_GET['classid'];
	$list=array();
	$result = json_encode(array("state" => "no match file","list" => $list,"start" => 0,"total" => 0));
	$where = "";
	if($action=='listimage') //ͼƬ 
	{
		$where= ' and type=1';
	}
	else if($action=='listfile') //����
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
	// ͳ������
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
	/* �������� */
	if (!count($list)) { return $result; }
	return $result = json_encode(array(
		"state" => "SUCCESS",
		"list" => $list,
		"start" => $start,
		"total" => $total
	));
}
