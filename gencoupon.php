<?php
require_once("/wp-load.php");
require_once("/wp-config.php");
require_once 'swift/lib/swift_required.php';
global $wpdb;
$email = $_GET['email'];
$oldEmail = $wpdb->get_row( $wpdb->prepare("SELECT * FROM face_coupon_email WHERE email = %s", $email));
if (!is_null($oldEmail)) {
	echo "fail";
} else {
	
$coupon = generate_coupons15();
$wpdb->insert( 
	'face_coupon_email', 
	array( 
		'email' => $_GET['email'], 
		'coupon' => $coupon,
		'type' => 'first10' 
	), 
	array( 
		'%s', 
		'%s',
		'%s' 
	) 
);



$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
  ->setUsername('lionboom113')
  ->setPassword('satthuden');

$mailer = Swift_Mailer::newInstance($transport);

$message = Swift_Message::newInstance('Your coupon arrive!')
  ->setFrom(array('abc@example.com' => 'MadLips Shop'))
  ->setTo(array($_GET['email']))
  ->setBody('Thanks for subcribe our website, your coupon is '.$coupon);

$result = $mailer->send($message);
}

?>

