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
    $dom = $dom->findOne("div");
    var_dump($dom->class);
} catch (Exception $exception) {
    echo $exception->getMessage();
}
