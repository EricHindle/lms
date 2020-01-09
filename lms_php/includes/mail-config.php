<?php

require 'ret-config.php';

function sendmail($to, $subject, $message, $name, $list) {
	$mail = new PHPMailer();
	$body = $message;
	//$mail->IsSMTP();
	$mail->SMTPAuth = FSV_MAIL_AUTH;
	$mail->Host = FSV_MAIL_HOST;
	$mail->Port = FSV_MAIL_PORT;
	$mail->Username = FSV_MAIL_USER;
	$mail->Password = FSV_MAIL_PASS;
	$mail->SMTPSecure = FSV_MAIL_SECURE;
	$mail->SetFrom(FSV_MAIL_FROMADDRESS, FSV_MAIL_FROMNAME);
	$mail->AddReplyTo(FSV_MAIL_REPLYADDRESS, FSV_MAIL_REPLYNAME);
	$mail->Subject = $subject;
	$mail->AltBody = "Any message.";
	$mail->MsgHTML($body);
	foreach ($list as $bccer) {
		$mail->AddBCC($bccer);
	}
	$address = $to;
	$mail->AddAddress($address, $name);
	if (!$mail->Send()) {
		return 0;
	} else {
		return 1;
	}
}
?>
