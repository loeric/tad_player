<?php
/*-----------引入檔案區--------------*/
include_once __DIR__ . '/header.php';
$xoopsOption['template_main'] = set_bootstrap("tad_player_playlist.html");
include_once XOOPS_ROOT_PATH . "/header.php";
include_once XOOPS_ROOT_PATH . "/modules/tadtools/star_rating.php";
/*-----------function區--------------*/

//清單播放
function playlist($pcsn = "0")
{
    global $xoopsModuleConfig, $xoopsUser, $xoopsTpl;
    if (empty($pcsn)) {
        $pcsn = 0;
    }
    $cate   = get_tad_player_cate($pcsn);
    $ok_cat = chk_cate_power();

    $user_group = array();
    if ($xoopsUser) {
        $user_group = $xoopsUser->getGroups();
    }
    if (!empty($pcsn) and !in_array($pcsn, $ok_cat)) {
        redirect_header("index.php", 3, sprintf(_MD_TADPLAYER_NO_POWER, $cate['title']));
    }

    $playcode = play_code_jwplayer("tad_player_list{$pcsn}", $cate, $pcsn, "playlist", false, null, null, $xoopsModuleConfig['display_max'], $xoopsModuleConfig['display']);

    $title = (empty($cate[$pcsn])) ? "" : $cate[$pcsn];

    $xoopsTpl->assign("mode", "list");
    $xoopsTpl->assign("title", $title);
    $xoopsTpl->assign("playcode", $playcode);
}

/*-----------執行動作判斷區----------*/
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op   = system_CleanVars($_REQUEST, 'op', '', 'string');
$psn  = system_CleanVars($_REQUEST, 'psn', 0, 'int');
$pcsn = system_CleanVars($_REQUEST, 'pcsn', 0, 'int');

$xoops_module_header = "";
get_jquery(true);

switch ($op) {

    //預設動作
    default:
        playlist($pcsn);
        break;
}

/*-----------秀出結果區--------------*/

// $arr=get_tad_player_cate_path($pcsn);
// $jBreadCrumbPath=tad_player_breadcrumb($pcsn,$arr);
// $xoopsTpl->assign( "path_bar" , $jBreadCrumbPath);
$xoopsTpl->assign("push", push_url($xoopsModuleConfig['use_social_tools']));
$xoopsTpl->assign("toolbar", toolbar_bootstrap($interface_menu));
$xoopsTpl->assign("psn", $psn);
$xoopsTpl->assign("pcsn", $pcsn);

$cate_select = get_tad_player_cate_option(0, 0, $pcsn);
$xoopsTpl->assign('cate_select', $cate_select);

if (isset($title) and !empty($title)) {
    $xoopsTpl->assign("xoops_pagetitle", $title);
    if (is_object($xoTheme)) {
        $xoTheme->addMeta('meta', 'keywords', $title);
        $xoTheme->addMeta('meta', 'description', $info);
    } else {
        $xoopsTpl->assign('xoops_meta_keywords', 'keywords', $title);
        $xoopsTpl->assign('xoops_meta_description', $info);
    }
}

include_once XOOPS_ROOT_PATH . '/footer.php';
