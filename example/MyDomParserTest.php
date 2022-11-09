<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Rocketsoba\DomParserWrapper\DomParserAdapter;

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
