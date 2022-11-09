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
