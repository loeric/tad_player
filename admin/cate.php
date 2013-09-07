<?php
//  ------------------------------------------------------------------------ //
// ���Ҳե� tad �s�@
// �s�@����G2008-02-28
// $Id: cate.php,v 1.2 2008/05/14 01:22:58 tad Exp $
// ------------------------------------------------------------------------- //

/*-----------�ޤJ�ɮװ�--------------*/
$xoopsOption['template_main'] = "tp_adm_cate.html";
include_once "header.php";
include_once "../function.php";


/*-----------function��--------------*/
//tad_player_cate�s�����
function tad_player_cate_form($pcsn="",$show_border=true){
	global $xoopsDB,$xoopsModuleConfig;
	include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");

	
	//����w�]��
	if(!empty($pcsn)){
		$DBV=get_tad_player_cate($pcsn);
	}else{
		$DBV=array();
	}

	//�w�]�ȳ]�w

	$pcsn=(!isset($DBV['pcsn']))?"":$DBV['pcsn'];
	$of_csn=(!isset($DBV['of_csn']))?"":$DBV['of_csn'];
	$title=(!isset($DBV['title']))?"":$DBV['title'];
	$enable_group=(!isset($DBV['enable_group']))?"":explode(",",$DBV['enable_group']);
  $enable_upload_group=(!isset($DBV['enable_upload_group']))?array('1'):explode(",",$DBV['enable_upload_group']);
	$sort=(!isset($DBV['sort']))?auto_get_csn_sort():$DBV['sort'];

	$op=(empty($pcsn))?"insert_tad_player_cate":"update_tad_player_cate";

	$cate_select=get_tad_player_cate_option(0,0,$of_csn,1,false);
	
	//�i���s��
	$SelectGroup_name = new XoopsFormSelectGroup("", "enable_group", false,$enable_group, 3, true);
	$SelectGroup_name->addOption("", _MA_TADPLAYER_ALL_OK, false);
	$SelectGroup_name->setExtra("class='span3'");
	$enable_group = $SelectGroup_name->render();

  //�i�W�Ǹs��
  $SelectGroup_name = new XoopsFormSelectGroup("", "enable_upload_group", false,$enable_upload_group, 3, true);
	$SelectGroup_name->setExtra("class='span3'");
  $enable_upload_group = $SelectGroup_name->render();

  //$jquery=($show_border)?get_jquery():"";
	$main="
  <form action='{$_SERVER['PHP_SELF']}' method='post' id='myForm' enctype='multipart/form-data'>
  <div class='controls controls-row'>
  	<input type='text' name='title' class='span9' value='{$title}' placeholder='"._MA_TADPLAYER_TITLE."'>
    <button type='submit' class='btn btn-primary'>"._TAD_SAVE."</button>
  </div>
  
  <div class='controls controls-row'>
    <div class='span3'>
    "._MA_TADPLAYER_OF_CSN."
    <select name='of_csn' size=3 class='span3'>
  	$cate_select
  	</select></div>
    
    <div class='span3'>"._MA_TADPLAYER_ENABLE_GROUP."
    $enable_group
    </div>
    
    <div class='span3'>"._MA_TADPLAYER_ENABLE_UPLOAD_GROUP."
    $enable_upload_group
    </div>
    

    <input type='hidden' name='sort' size='2' value='{$sort}'>
  	<input type='hidden' name='pcsn' value='{$pcsn}'>
    <input type='hidden' name='op' value='{$op}'>
  </div>

  </form>";

  if($show_border){
    $main=div_3d(_MA_INPUT_CATE_FORM,$main,"raised","display:inline;float:left;");
  }

	return $main;
}

//�s�W��ƨ�tad_player_cate��
function insert_tad_player_cate(){
	global $xoopsDB;
	if(empty($_POST['title']))return;
	if(empty($_POST['enable_group']) or in_array("",$_POST['enable_group'])){
    $enable_group="";
  }else{
    $enable_group=implode(",",$_POST['enable_group']);
  }

  if(empty($_POST['enable_upload_group'])){
    $enable_upload_group="1";
  }else{
    $enable_upload_group=implode(",",$_POST['enable_upload_group']);
  }
  
	$sql = "insert into ".$xoopsDB->prefix("tad_player_cate")." (of_csn,title,enable_group,enable_upload_group,sort,width,height) values('{$_POST['of_csn']}','{$_POST['title']}','{$enable_group}','{$enable_upload_group}','{$_POST['sort']}','{$_POST['width']}','{$_POST['height']}')";
	$xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	//���o�̫�s�W��ƪ��y���s��
	$pcsn=$xoopsDB->getInsertId();
	mk_list_xml($pcsn);
	return $pcsn;
}



//�C�X�Ҧ�tad_player_cate���
function list_tad_player_cate($of_csn=1,$level=0,$modify_pcsn=''){
	global $xoopsDB,$xoopsConfig;
	$old_level=$level;
	$left=$level*12+4;
	$level++;

	
	$sql = "select * from ".$xoopsDB->prefix("tad_player_cate")." where of_csn='{$of_csn}' order by sort";

	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());


	if($old_level==0){
    $form=tad_player_cate_form($modify_pcsn,false);
    

    //�[�J�Ϥ�����
    if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/bubblepopup.php")){
     redirect_header("index.php",3, _MA_NEED_TADTOOLS);
    }
    include_once XOOPS_ROOT_PATH."/modules/tadtools/bubblepopup.php";
    $bubblepopup = new bubblepopup(false);

    $sql_cover="select pcsn from ".$xoopsDB->prefix("tad_player_cate")." order by sort";
    $result_cover = $xoopsDB->query($sql_cover) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
    while(list($pcsn)=$xoopsDB->fetchRow($result_cover)){

      //���U�v����
    	$video=count_video_num($pcsn);
      $img=empty($video['img'])?"images/empty_cate_{$xoopsConfig['language']}.png":$video['img'];
    
      if(!empty($img)){
        $bubblepopup->add_tip("#cover_{$pcsn}","<img src=\'"._TAD_PLAYER_IMG_URL."s_{$img}.png\'>",'right');
      }
    }
    $bubblepopup_code=$bubblepopup->render();

    //�[�J�����
    if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/treetable.php")){
       redirect_header("index.php",3, _MA_NEED_TADTOOLS);
      }
    include_once XOOPS_ROOT_PATH."/modules/tadtools/treetable.php";
    $treetable=new treetable(true , "pcsn" , "of_csn" , "#treetbl" , "save_drag.php" , ".folder" , "#save_msg" , true , ".sort", "save_cate_sort.php" , "#save_msg");
    $treetable_code=$treetable->render();


		$data="
    $treetable_code
    $bubblepopup_code
		<script>
		function delete_tad_player_cate_func(pcsn){
			var sure = window.confirm('"._TAD_DEL_CONFIRM."');
			if (!sure)	return;
			location.href=\"{$_SERVER['PHP_SELF']}?op=delete_tad_player_cate&pcsn=\" + pcsn;
		}
		</script>
    <div id='save_msg' style='float:right;'></div>
		<table id='treetbl' class='table table-striped table-bordered'>

    <tr><td colspan=5>$form</td></tr>
		<tr>
		<th>"._MA_TADPLAYER_TITLE."</th>
		<th>"._MA_TADPLAYER_VIDEO_AMOUNT."</th>
		<th>"._MA_TADPLAYER_ENABLE_GROUP."</th>
		<th>"._MA_TADPLAYER_ENABLE_UPLOAD_GROUP."</th>
		<th>"._TAD_FUNCTION."</th></tr>
		<tbody class='sort'>";
	}else{
		$data="";
	}
	
	while($all=$xoopsDB->fetchArray($result)){
    foreach($all as $k=>$v){
      $$k=$v;
    }

    $g_txt=txt_to_group_name($enable_group,_MA_TADPLAYER_ALL_OK);
    $gu_txt=txt_to_group_name($enable_upload_group,_MA_TADPLAYER_ALL_OK);
    
    //��z�v������
    $video=count_video_num($pcsn);
    $img=empty($video['img'])?"images/empty_cate_{$xoopsConfig['language']}.png":$video['img'];
    $counter=$video['rel_num'];
		if(!empty($img) and file_exists(_TAD_PLAYER_IMG_DIR."s_{$img}.png")){
      $pic="<img src='../images/image.png' id='cover_{$pcsn}'>";
		}else{
      $pic="";
    }

    $class=(empty($of_csn))?"":"class='child-of-node-_{$of_csn}'";

		$data.="
    <tr id='node-_{$pcsn}' $class style='letter-spacing: 0em;'>
		<td nowrap>
    <img src='".XOOPS_URL."/modules/tadtools/treeTable/images/move_s.png' class='folder' alt='"._MA_TREETABLE_MOVE_PIC."' title='"._MA_TREETABLE_MOVE_PIC."'>
    <a href='../index.php?pcsn=$pcsn' target='_blank'>{$title}</a>
    {$pic}
    </td>
		<td>{$counter}</td>
		<td>{$g_txt}</td>
		<td>{$gu_txt}</td>
		<td>

    <img src='".XOOPS_URL."/modules/tadtools/treeTable/images/updown_s.png' style='cursor: s-resize;' alt='"._MA_TREETABLE_SORT_PIC."' title='"._MA_TREETABLE_SORT_PIC."'>
    
		<a href='{$_SERVER['PHP_SELF']}?op=tad_player_cate_form&pcsn=$pcsn' class='btn btn-mini btn-warning'>"._TAD_EDIT."</a>
		<a href=\"javascript:delete_tad_player_cate_func($pcsn);\" class='btn btn-mini btn-danger'>"._TAD_DEL."</a>
		<a href='{$_SERVER['PHP_SELF']}?op=mk_thumb&pcsn=$pcsn' class='btn btn-mini btn-info'>thumb</a>
    </td></tr>";
		$data.=list_tad_player_cate($pcsn,$level);
	}
	
	if($old_level==0){
		$data.="
		</tbody>
		</table>";
	}
	
	
	
 return $data;
}

//���@�Y��
function mk_thumb($pcsn=""){
  global $xoopsDB;
  set_time_limit (0);
	$sql = "select `psn`,`image` from ".$xoopsDB->prefix("tad_player")." where pcsn='{$pcsn}' order by sort";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	while($all=$xoopsDB->fetchArray($result)){
    foreach($all as $k=>$v){
      $$k=$v;
    }
    if(!file_exists(_TAD_PLAYER_IMG_DIR."s_{$psn}.png")){
   		$filename = basename($image);
   		$type=getimagesize ($image);
   		$pic_s_file=_TAD_PLAYER_IMG_DIR."s_".$psn.".png";
      mk_video_thumbnail($image,$pic_s_file,$type['mime'],"120");
    }
  }
}

//��stad_player_cate�Y�@�����
function update_tad_player_cate($pcsn=""){
	global $xoopsDB;
	if(empty($_POST['enable_group']) or in_array("",$_POST['enable_group'])){
    $enable_group="";
  }else{
    $enable_group=implode(",",$_POST['enable_group']);
  }

  if(empty($_POST['enable_upload_group'])){
    $enable_upload_group="1";
  }else{
    $enable_upload_group=implode(",",$_POST['enable_upload_group']);
  }
	$sql = "update ".$xoopsDB->prefix("tad_player_cate")." set  of_csn = '{$_POST['of_csn']}', title = '{$_POST['title']}', enable_group = '{$enable_group}', enable_upload_group = '{$enable_upload_group}', sort = '{$_POST['sort']}', width = '{$_POST['width']}', height = '{$_POST['height']}' where pcsn='$pcsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	mk_list_xml($pcsn);
	$log="update $pcsn OK!";
	return $log;
}

//�R��tad_player_cate�Y����Ƹ��
function delete_tad_player_cate($pcsn=""){
	global $xoopsDB;
	
  //����X���U�Ҧ��v��
  $sql="select psn from ".$xoopsDB->prefix("tad_player")." where pcsn='$pcsn'";
  $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  while(list($psn)=$xoopsDB->fetchRow($result)){
    delete_tad_player($psn);
  }

  //��X���U�����A�ñN���������ݤ����M��
  $sql="update ".$xoopsDB->prefix("tad_player_cate")." set  of_csn='' where of_csn='$pcsn'";
  $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());


	$sql = "delete from ".$xoopsDB->prefix("tad_player_cate")." where pcsn='$pcsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	
	unlink(_TAD_PLAYER_UPLOAD_DIR."{$psn}_list.xml");
}

//�۰ʨ��o�Y�����U�̤j���Ƨ�
function auto_get_csn_sort($pcsn=""){
  global $xoopsDB;
  $sql = "select max(`sort`) from ".$xoopsDB->prefix("tad_player_cate")." where of_csn='{$pcsn}' group by of_csn";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  list($max_sort)=$xoopsDB->fetchRow($result);

  return ++$max_sort;
}
/*-----------����ʧ@�P�_��----------*/
$op = (!isset($_REQUEST['op']))? "":$_REQUEST['op'];
$pcsn = (!isset($_REQUEST['pcsn'])) ? 0 : intval($_REQUEST['pcsn']);

switch($op){

	//�s�W���
	case "insert_tad_player_cate":
	insert_tad_player_cate();
	header("location: {$_SERVER['PHP_SELF']}");
	break;


	//�R�����
	case "delete_tad_player_cate";
	delete_tad_player_cate($pcsn);
	header("location: {$_SERVER['PHP_SELF']}");
	break;
	

	//��s���
	case "update_tad_player_cate";
	$log=update_tad_player_cate($pcsn);
	redirect_header($_SERVER['PHP_SELF'],3, $log);
	break;


	//���@�Y��
	case "mk_thumb";
	mk_thumb($pcsn);
	header("location: {$_SERVER['PHP_SELF']}");
	break;

	//�w�]�ʧ@
	default:
	$main=list_tad_player_cate(0,0,$pcsn);
	break;

}

/*-----------�q�X���G��--------------*/

$xoopsTpl->assign('main',$main);
include_once 'footer.php';
?>