<?php

date_default_timezone_set("Asia/Tokyo");
ini_set("arg_separator.output", "&");
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

require_once __DIR__ . "/../vendor/autoload.php";

use \DomParserWrapper\DomParserAdapter;
use \Curl\MyCurlBuilder;
use \Curl\MyCurl;

/* $curl1 = (new MyCurlBuilder("https://www.nicovideo.jp/"))->build();
 * $result_html = $curl1->getResult(); */
try {
    $dom = new DomParserAdapter("
<html>
  <body>
    <div>
    </div>
  </body>
</html>");
    $div_elem = $dom->findOne("div")->plaintext;
    var_dump($div_elem);
    $dom2 = new DomParserAdapter("");
} catch (Exception $exception) {
    echo $exception->getMessage();
}
