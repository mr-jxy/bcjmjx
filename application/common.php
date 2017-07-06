<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
* 无级分类
*/
function tree(&$data,$pid=0,$count=0){
	if (!isset($data['old'])) {
		$data = array('new' => array(),'old' => $data);
	}
	foreach ($data['old'] as $key => $value) {
		if ($value['fid'] == $pid) {
			$value['count'] = $count;
			$data['new'][] = $value;
			unset($data['old'][$key]);
			tree($data,$value['id'],$count+1);
		}
	}
	return $data['new'];
}

/**
* 树形结构
*/
function printTree($data,$print){
	foreach ($data as $key => $value) {
		$str = '';
		for ($i=0; $i < $value['count']; $i++) { 
			$str.=$print;
		}
		$data[$key]['name'] = $str.$value['name'];
	}
	return $data;
}

/**
* 导出
*/
function exportExcel($xlsName,$xlsrow,$list){
	$xlsTitle = iconv('utf-8', 'gb2312', $xlsName);//文件名称
	$fileName = date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
	$rowNum = count($xlsrow);
	$listNum = count($list);
	vendor("PHPExcel.PHPExcel");
	$objPHPExcel = new PHPExcel();
	$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
	$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$rowNum-1].'1');//合并单元格
	for($i=0;$i<$rowNum;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $xlsrow[$i][1]);
	}
	for($i=0;$i<$listNum;$i++){
		for($j=0;$j<$rowNum;$j++){
			$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $list[$i][$xlsrow[$j][0]]);
		}
	}
	header('pragma:public');
	header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
	header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
}
?>
