<?php
require_once("/wp-load.php");
$coupon = generate_coupons15();
require_once 'swift/lib/swift_required.php';

$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
  ->setUsername('lionboom113')
  ->setPassword('satthuden');

$mailer = Swift_Mailer::newInstance($transport);

$message = Swift_Message::newInstance('Your coupon arrive!')
  ->setFrom(array('abc@example.com' => 'MadLips Shop'))
  ->setTo(array($_GET['email']))
  ->setBody('Thanks for subcribe our website, your coupon is '.$coupon);

$result = $mailer->send($message);
?>

