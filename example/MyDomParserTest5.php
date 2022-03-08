<?php

date_default_timezone_set("Asia/Tokyo");
ini_set("arg_separator.output", "&");
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

require_once __DIR__ . "/../vendor/autoload.php";

use Rocketsoba\DomParserWrapper\DomParserAdapter;

try {
    $dom = new DomParserAdapter("
<html>
  <body>
    <div class=\"test\">
    </div>
  </body>
</html>");
    $dom2 = clone $dom;
    $dom->findOne("div2");
    var_dump($dom->outertext);
} catch (\Exception $exception) {
    echo $exception->getMessage();
}
try {
    $dom2->findOne("div")->findOne("tbody");
    var_dump($dom2->outertext);
} catch (\Exception $exception) {
    echo $exception->getMessage();
}
