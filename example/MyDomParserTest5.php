<?php

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
