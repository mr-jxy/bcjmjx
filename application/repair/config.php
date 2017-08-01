<?php
	$request = request();
	return [
	    // 视图输出字符串内容替换
	    'view_replace_str'       => [
	        '__PUBLIC__' => $request->root().'/static/admin',
			'__PUBLICIMG__' => $request->root().'/uploads'
	    ]
	];
?>