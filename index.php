<?php

require_once 'library/Requests.php';

Requests::register_autoloader();

$urls = '';

$regulars = array(
	"itunes.apple.com" => '/<h1 itemprop="name">(.*?)<\/h1>/',
	"play.google.com" => '/<div class="document-title" itemprop="name"> <div>(.*?)<\/div> <\/div>/',
	"www.windowsphone.com" => '/<h1 itemprop="name">(.*)<\/h1>/'
);

function parseName($str) 
{
	$delimiters = array(" -", ":", " for ");

	foreach ($delimiters as $delimiter) {
		$str = ($strpos=mb_strpos($str,$delimiter))!==false?mb_substr($str,0,$strpos,'utf8'):$str;
	}
	return $str;
} 

if(!empty($_POST)) {
	$urls = array_filter(preg_split('/\n|\r\n?/', $_POST['urls']));

	$results = array();
	foreach ($urls as $url) {
		$val = "";
		$parsedUrl = parse_url($url);
		if (!empty($parsedUrl["host"]) && $regulars[$parsedUrl["host"]]) {
			$r = Requests::get($url);
			$regular = $regulars[$parsedUrl["host"]];
			preg_match_all ($regular, $r->body, $matches);
			$val = parseName($matches[1][0]);
			if ($val) $results[$val][] = $url;
		}
	}

	foreach ($results as $product => $urls) {
		echo $product.":<br/>";
		foreach ($urls as $url) {
			echo $url."<br/>";
		}
		echo "<br/>";
	}
}

?>

<form method="POST" action="test.php">
	<textarea name="urls" style="width: 400px; height: 400px;"></textarea>
	<button type="submit">Отправить</button>
</form>