<?php

require_once __DIR__ . "/../vendor/autoload.php";

use KubAT\PhpSimple\HtmlDomParser;

$dom = HtmlDomParser::str_get_html("
<html>
  <body>
    <div>
    </div>
  </body>
</html>");
$dom2 = HtmlDomParser::str_get_html("");
var_dump($dom->find("ul", 0));
