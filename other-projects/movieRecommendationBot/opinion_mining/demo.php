<?php
if (PHP_SAPI != 'cli') {
	echo "<pre>";
}

$strings = array(
	1 => 'I really like the new design of your website',
	2 => 'This design is terrible',
	3 => 'She is seemingly agresive',
	);

require_once __DIR__.'/./autoload.php'; // __DIR__ or _DIR_
$sentiment = new \PHPInsight\Sentiment(); //sentiment?? or Sentiment??


$opinion = array('pos'=>0, 'neg'=>0, 'neu'=>0);
foreach ($strings as $string) {
	$scores = $sentiment->score($string);
	$class = $sentiment->categorise($string);

	echo "String: $string\n";
	echo "Dominant: $class, scores: ";
	print_r($scores);
	echo "\n";

		$opinion['pos'] += $scores['pos'];
		$opinion['neu'] += $scores['neu'];
		$opinion['neg'] += $scores['neg'];
		
		print_r($opinion);
}



?>