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
    <div class=\"test\">
      <div>
      </div>
    </div>
    <div class=\"test2\">
    </div>
  </body>
</html>");
    $dom2 = $dom->findOne("div.test");
    var_dump($dom2->outertext);
    var_dump($dom->outertext);
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}
