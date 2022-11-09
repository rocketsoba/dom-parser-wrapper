<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Rocketsoba\DomParserWrapper\DomParserAdapter;

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
