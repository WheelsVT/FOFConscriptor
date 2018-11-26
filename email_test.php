<?php

include "includes/classes.inc.php";

require_once './includes/lib/swift_required.php';
$transport = Swift_MailTransport::newInstance();

if ( $settings->get_value(kSettingEmailType)==kEmailTypeSendmail ){
    // Sendmail
    $transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
}
else if ( $settings->get_value(kSettingEmailType)==kEmailTypeSMTP ){
    // Create the Transport
    $transport = Swift_SmtpTransport::newInstance($settings->get_value(kSettingSMTPServer), $settings->get_value(kSettingSMTPPort))
      ->setUsername($settings->get_value(kSettingSMTPUser))
      ->setPassword($settings->get_value(kSettingSMTPPassword));
    if ( $settings->get_value(kSettingSMTPEncryptType)==kSettingSMTPEncryptTypeSSL )
        $transport->setEncryption('ssl');
    else if ( $settings->get_value(kSettingSMTPEncryptType)==kSettingSMTPEncryptTypeTLS )
        $transport->setEncryption('tls');
}

$statement = "select * from team where team_id = '".kAdminUser."'";
$row = mysql_fetch_array(mysql_query($statement));
$fromaddress = $row['team_email'];
// Create the Mailer using your created Transport
$mailer = Swift_Mailer::newInstance($transport);
// Create the message
$messagetosend = Swift_Message::newInstance()
  // Give the message a subject
  ->setSubject('Test Email')
  // Set the From address with an associative array
  ->setFrom(array($fromaddress => 'FOF Draft Admin'))
  // Set the To addresses with an associative array
  ->setTo(array($fromaddress))
  // Give it a body
  ->setBody('This is a test email message.');
// Send the message
try{
    $emailresult = $mailer->send($messagetosend);
} catch(Exception $e){
    $_SESSION['message'] = "Error sending email: ".$e->getMessage();
}

if (!$_SESSION['message']) 
    $_SESSION['message'] = "Test email sent. Response: ".$result;

// Return to the main page
header("Location: draft_options.php");
?>
