<?php

date_default_timezone_set("Asia/Tokyo");
ini_set("arg_separator.output", "&");
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

require_once __DIR__ . "/../vendor/autoload.php";

use \Sunra\PhpSimple\HtmlDomParser;

$dom = HtmlDomParser::str_get_html("
<html>
  <body>
    <div>
    </div>
  </body>
</html>");
$dom2 = HtmlDomParser::str_get_html("");
var_dump($dom->find("ul", 0));
