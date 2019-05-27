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
    $dom = $dom->findMany("div");
    foreach ($dom as $idx1 => $val1) {
        try {
            $div_elem = clone $val1;
            var_dump($div_elem->findOne("div")->outertext);
        } catch (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    }
} catch (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
}
