<?php

function create_url($uri, $show, $param = array(), $attribute = array(), $is_link = true)
{

	$CI = & get_instance();
	$CI->load->model('user');

	$user = $_SESSION['user_info'] ?? [];

	// 验证是否登录
	if(empty($user) || empty($uri)){
		return '';
	}

	// $routes = isset($CI->session->userdata['p_routes'])?$CI->session->userdata['p_routes']:[];
	// if(!$CI->session->userdata['is_super'] && !checkPermission($routes,$uri)){
	//     return '';
	// }
	if (!$CI->user->isPerm($uri, $user)) {
		return '';
	}

	$str = '';
	$url = $uri;
	if (! empty($param)) {
		$param_str = http_build_query($param);
		// 检测$link是否原本就有参数
		$temp=parse_url($uri);
		if(!empty($temp['query'])){
			$url = $uri . '&' . $param_str;
		}else{
			$url = $uri . '?' . $param_str;
		}
	}
	$url='/'.ltrim($url,'/');
	if (! $is_link) {
		$url = "javascript:;";
	}
	$attr_str = '';
	if (! empty($attribute)) {
		foreach ($attribute as $key => $value) {
			$attr_str .= " $key='$value' ";
		}
	}
	$str = <<<STR
<a href='$url' $attr_str>$show</a>
STR;
	echo $str;
}