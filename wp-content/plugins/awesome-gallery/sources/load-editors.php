<?php
foreach(array('manual', 'instagram', 'flickr', 'facebook', '500px', 'rss', 'posts') as $source){
	require(dirname(__FILE__) . "/$source/editor.class.php");
}
require(dirname(__FILE__) .'/nextgen/editor.class.php');
