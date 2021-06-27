<?php
if(version_compare(PHP_VERSION,'5.3.0','<'))  
	die('require PHP > 5.3.0!');

require_once __DIR__.'/opinion_mining/autoload.php'; //this is for opinion mining

require 'vendor/autoload.php'; //this is for web crawler
use QL\QueryList;
	//get  data
	$movieCsv = array_map('str_getcsv', file('./IMDB-movies.csv'));

	//empty array to add $data into
	$showing = array();
	//Get the currently showing movies from IMDB using web Crawler
	$url = "https://www.imdb.com/movies-in-theaters/";
	$reg=array(
		"image" => array(".image img", "src"),
		"title"=>array(".overview-top h4 a", "title"),
		"genre"=>array(".cert-runtime-genre",'text')
		);
	$data = QueryList::Query($url,$reg)->data;

	$showing += $data;

	//get 3 random number to run test
	$test1 = rand(1, 1000);
	$test2 = rand(1, 1000);
	while ($test2 == $test1)
	{
		$test2 = rand(1, 1000); //reset if they are the same
	}
	$test3 = rand(1, 1000);
	while ($test3 == $test2 || $test3 == $test1)
	{
		$test3 = rand(1, 1000); //reset if they are the same
	}

	$likedGenre = array();

	//update likedGenre based on opinion mining
	function updatePreference($completed, $opinionMined) 
	{	
		//allow global variable
		global $test1, $test2, $test3, $movieCsv, $likedGenre;

		$tested = 0;

		if ($completed == 1) 
		{
			$tested = $test1;
		}
		else if ($completed == 2)
		{
			$tested = $test2;
		}
		else 
		{
			$tested = $test3;
		}
		
		$getGenre = $movieCsv[$tested][2];
		$genreHolder = explode(",", $getGenre);
		foreach ($genreHolder as $value)
		{	
			if (array_key_exists($value, $likedGenre) == true) 
			{
				$likedGenre[$value] += $opinionMined;
			}
			else 
			{
				$likedGenre += array($value => $opinionMined);
			}	
		}
		$m = recommendMovie($likedGenre); //run liked movie and return it 
		return $m;
	}

	function recommendMovie($likedGenre) 
	{
		global $showing;

		$recommendationIndex = array(); //placeholder

		foreach ($showing as $movie)
		{
			$movieTitle = $movie['title'];
			$movieGenre = $movie['genre'];
			$recommendationIndex += array($movieTitle=> 0);
			foreach ($likedGenre as $genre => $score) // likedGenre = array('Drama'=> 5);
			{	
				if (strpos($movieGenre, $genre))
					{
						
						if ($score >= 0) 
						{
							$recommendationIndex[$movieTitle] += 3;
						}
						else if ($score < 0)
						{
							$recommendationIndex[$movieTitle] -= 3;
						}
						else if ($score == 0)
						{
							return "more information needed";
						}
					}
			}
		}

		$maxValue = max($recommendationIndex);
		$bestMatch = array_search($maxValue, $recommendationIndex);
		$key = array_search($bestMatch, array_column($showing, 'title'));
		$image = $showing[$key]['image'];


		$return = "<h4>Recommendation:</h4>
				<img src=".$image."> ".$bestMatch;
		return $return;
	}
//sentence seperator function
function RequestCURL($url, $header = array(), $postArr = '') { 
 
    $post = http_build_query($postArr, '&'); 
 
    $curl = curl_init($url); 
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);     
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);     
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($curl, CURLOPT_HEADER, false);     
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); 
 
    if (!empty($post)) { 
        curl_setopt($curl, CURLOPT_POST, true); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post); 
    } 
	$data = curl_exec($curl);     
	curl_close($curl);     
	return $data; 
}

//THIS IS SENTENCE SEGMENTER 
function seperator($text)
{
	$response = RequestCURL(
		"https://textanalysis.p.rapidapi.com/spacy-sentence-segmentation",
		array(
			"X-Mashape-key: bc810764bdmshcdf4d5da951fc56p1e193cjsn11aaafbbbb5f",
			"Content-Type: application/x-www-form-urlencoded",
        	"Accept: application/json"), 
		array(
			"text"=> $text)
	);
	return $response; 
}




function operator($index, $dialog)
{
	$opinion = array('pos'=>0, 'neg'=>0, 'neu'=>0); //initialize NO OPINION
	
	$sentences = array(seperator($dialog)); //return sentence segmenter in array

	
	$sentiment = new \PHPInsight\Sentiment();
	foreach ($sentences as $string)
	{
		$scores = $sentiment->score($string);
		$class = $sentiment->categorise($string);
		$opinion['pos'] += $scores['pos'];
		$opinion['neu'] += $scores['neu'];
		$opinion['neg'] += $scores['neg'];
	}

	if ($opinion['pos'] > $opinion['neg'])
	{
		$bestMovie =updatePreference($index, 5);
		return $bestMovie;
	}
	else if ($opinion['pos'] < $opinion['neg'])
	{
		$bestMovie = updatePreference($index, -5);
		return $bestMovie;
	} 
	else if ($opinion['pos'] = $opinion['neg'])
	{
		return "more information needed";
	}
	//return $likedGenre;
} 



if(isset($_GET['text1']))
{
	if($_GET['text1'] == "recommend")
	{
		echo recommendMovie();
	}
	else 
	{	
		$dialog = $_GET['text1'];
		$index = $_GET['index'];
		echo operator($index, $dialog);
	}
}


?>