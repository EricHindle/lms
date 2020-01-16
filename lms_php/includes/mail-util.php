<?php

require 'ret-config.php';
require 'class.phpmailer.php';

function getmailer() {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = get_global_value('smtp_host');
        $mail->Port = get_global_value('smtp_port');
        $mail->Username = get_global_value('smtp_user');
        $mail->Password = get_global_value('smtp_pwd');
        $mail->SMTPSecure = 'ssl';
        $mail->SetFrom(get_global_value('smtp_from_address'), get_global_value('smtp_from_name'));
        $mail->AddReplyTo(get_global_value('smtp_reply_address'), get_global_value('smtp_reply_name'));
    return $mail;
}

function sendmail($to, $subject, $message, $name, $list) {
    $mail = getmailer();

    $body = $message;
    
    $mail->Subject = $subject;
    $mail->AltBody = $body;
    $mail->MsgHTML($body);
    foreach ($list as $bccer) {
        $mail->AddBCC($bccer);
    }
    $address = $to;
    $mail->AddAddress($address, $name);

    if ($mail->Send()) {
        return true;
    } else {
        return false;
    }
}
?>
