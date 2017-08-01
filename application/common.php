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
function printTree($data,$print,$name = 'name'){
	foreach ($data as $key => $value) {
		$str = '';
		for ($i=0; $i < $value['count']; $i++) { 
			$str.=$print;
		}
		$data[$key][$name] = $str.$value[$name];
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

/**
* 验证手机号
*/
function isPhone($phone){
	if (!preg_match("/^1(3|4|5|7|8)\d{9}$/", $phone)) {
        return false;
    }else{
    	return true;
    }
}

/**
* 短信接口
*/
function zendSms($phone,$content){
	vendor("Sms.smstest");
	$objsmstest = new smstest();
	return $objsmstest->zendSms($phone,$content);
}

/**
* 邮件接口
*/
function zendEmail($email,$content){
	vendor("PHPMailer.PHPMailer");
	$mail = new PHPMailer();  
	$mail->SMTPDebug = 1;
	$mail->isSMTP();// 使用SMTP服务  
    $mail->CharSet = "UTF-8"; // 编码格式为utf8，不设置编码的话，中文会出现乱码  
    $mail->Host = "smtp.163.com";// 发送方的SMTP服务器地址  
    $mail->SMTPAuth = true;// 是否使用身份验证  
    $mail->Username = "15891497842@163.com";// 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱 
    $mail->Password = "jxy614272658";// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！  
    $mail->SMTPSecure = "ssl";// 使用ssl协议方式  
    $mail->Port = 465;// 163邮箱的ssl协议方式端口号是465/994
    $mail->setFrom("15891497842@163.com","xknc");// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示  
    $mail->addAddress($email);// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)  
    $mail->addReplyTo("15891497842@163.com","Reply");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址 
    $mail->Subject = "这是一个测试邮件";// 邮件标题  
    $mail->Body = $content;// 邮件正文   
    if(!$mail->send()){// 发送邮件  
        echo "Message could not be sent.";  
        echo "Mailer Error: ".$mail->ErrorInfo;// 输出错误信息  
    }else{  
        return true;  
    }   
}
?>
