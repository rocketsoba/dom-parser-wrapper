<?php

require_once __DIR__ . "/../vendor/autoload.php";

use KubAT\PhpSimple\HtmlDomParser;

$dom = HtmlDomParser::str_get_html("
<html>
  <body>
    <div>
    </div>
    <ul>
    </ul>
  </body>
</html>");
$dom2 = $dom->find("div", 0);
var_dump($dom2->outertext);
var_dump($dom->outertext);
