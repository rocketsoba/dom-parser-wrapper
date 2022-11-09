# dom-parser-wrapper

This is a wrapper of 'PHP Simple HTML DOM Parser'.
If you try to use method chaining technique with original php-simple-html-dom-parser, PHP may return Notice message and NULL.
This library is for avoiding this issue by notifying encounter of empty elements by Exception.

## Installation

```
# This library is not available on Packagist, so you need to add repository manually.
composer config repositories.dom-parser-wrapper '{"type": "vcs", "url": "https://github.com/rocketsoba/dom-parser-wrapper", "no-api": true}'
composer require rocketsoba/dom-parser-wrapper
```

## Usage

```php
<?php

use Rocketsoba\DomParserWrapper\DomParserAdapter;
use Rocketsoba\DomParserWrapper\Exception\DomNotFoundException;

// $html is html string
$html = <<<EOF
<html>
  <body>
    <div>
      div
    </div>
    <div class="class1">
      div class1
    </div>
    <div class="class 2">
      div class 2
    </div>
    <div id="id1">
      div id1
    </div>
  </body>
</html>
EOF;

$dom = new DomParserAdapter($html);

echo (clone $dom)->findOne("div")->plaintext . PHP_EOL;
echo (clone $dom)->findOne("div.class1")->plaintext . PHP_EOL;
echo (clone $dom)->findOne('div[class="class 2"]')->plaintext . PHP_EOL;
echo (clone $dom)->findOne("div#id1")->plaintext . PHP_EOL;
// if you can not find element, library throws Rocketsoba\DomParserWrapper\Exception\DomNotFoundException
try {
    echo (clone $dom)->findOne("div#id2")->plaintext . PHP_EOL;
} catch (DomNotFoundException $exception) {
}

// emulate browser rendered string
echo (clone $dom)->findOne("div")->plaintext . PHP_EOL;
// whole element
echo (clone $dom)->findOne("div")->outertext . PHP_EOL;
// inner element
echo (clone $dom)->findOne("div")->innertext . PHP_EOL;
```
