<?php

namespace DomParserWrapper;

use \Sunra\PhpSimple\HtmlDomParser;
use \DomParserWrapper\Exception\DomNotFoundException;
use \DomParserWrapper\Exception\DomUnreadableException;
use \LogicException;

class DomParserAdapter implements \IteratorAggregate
{
    private $current_dom;
    private $all_dom;
    private $is_dom_many = false;
    private $allow_exception = true;

    public function __construct($raw_html)
    {
        $this->current_dom = HtmlDomParser::str_get_html($raw_html);
        if ($this->current_dom === false && $this->allow_exception) {
            throw new DomUnreadableException();
        }
        return $this->current_dom;
    }

    public function getIterator()
    {
        if (empty($this->all_dom)) {
            yield $this;
        } else {
            foreach ($this->all_dom as $idx1 => $val1) {
                $this->current_dom = $val1;
                yield $this;
            }
        }
    }

    public function findOne($search_str)
    {
        if ($this->validateSingleDom()) {
            /* 1to1 */
            $this->current_dom = $this->current_dom->find($search_str, 0);
        } else {
            /* 多to1 */
            foreach ($this->all_dom as $idx1 => $val1) {
                $current_dom = $val1->find($search_str, 0);

                if (!is_null($current_dom)) {
                    $this->current_dom = $current_dom;
                    $this->all_dom = null;
                    break;
                }
            }
        }

        if ((
                 !is_object($this->current_dom)
              || get_class($this->current_dom) !== "simplehtmldom_1_5\simple_html_dom_node"
            )
            && $this->allow_exception) {
            throw new DomNotFoundException();
        }

        return $this;
    }

    public function findMany($search_str)
    {
        if ($this->validateSingleDom()) {
            /* 1to多 */
            $this->all_dom = $this->current_dom->find($search_str);
            $this->current_dom = null;
        } else {
            /* 多to多 */
            $all_dom = [];

            foreach ($this->all_dom as $idx1 => $val1) {
                $current_dom = $val1->find($search_str);
                $all_dom = array_merge($all_dom, $current_dom);
            }
            $this->all_dom = $all_dom;
        }

        if (empty($this->all_dom) && $this->allow_exception) {
            throw new DomNotFoundException();
        }

        return $this;
    }

    private function validateSingleDom()
    {
        if (empty($this->current_dom) && empty($this->all_dom)) {
            /* 多も1も存在しないとき */
            throw new LogicException("Somehow dom is invalid");
        } elseif (!empty($this->current_dom) && !empty($this->all_dom)) {
            /* 多も1も存在するとき(Iteratorしていて子オブジェクトのメソッドが呼ばれたとき) */
            $this->all_dom = null;
            return true;
        } elseif (!empty($this->all_dom)) {
            /* 多だけが存在するとき */
            if (count($this->all_dom) == 1) {
                /* 配列の長さが1の時、配列を破棄してHtmlDomParserオブジェクトに変える */
                $this->current_dom = $this->all_dom[0];
                $this->all_dom = null;
                return true;
            } else {
                return false;
            }
        } else {
            /* 1だけが存在するとき */
            return true;
        }
    }

    private function plaintext()
    {
        if ($this->validateSingleDom()) {
            return $this->current_dom->plaintext;
        } else {
            $all_text = [];
            foreach ($this->all_dom as $idx1 => $val1) {
                $all_text[] = $val1->plaintext;
            }

            return $all_text;
        }
    }

    private function innertext()
    {
        if ($this->validateSingleDom()) {
            return $this->current_dom->innertext;
        } else {
            $all_text = [];
            foreach ($this->all_dom as $idx1 => $val1) {
                $all_text[] = $val1->innertext;
            }

            return $all_text;
        }
    }

    private function outertext()
    {
        if ($this->validateSingleDom()) {
            return $this->current_dom->outertext;
        } else {
            $all_text = [];
            foreach ($this->all_dom as $idx1 => $val1) {
                $all_text[] = $val1->outertext;
            }

            return $all_text;
        }
    }

    public function getAllAttributes()
    {
        if (!is_null($this->current_dom) && !is_null($this->current_dom->attr)) {
            return $this->current_dom->attr;
        } else {
            return [];
        }
    }

    /* 未実装 */
    public function denyException()
    {
        $this->allow_exception = false;

        return $this;
    }

    public function __get($name)
    {
        $all_attributes = $this->getAllAttributes();
        if (isset($all_attributes[$name])) {
            return $all_attributes[$name];
        }
        switch ($name) {
            case 'outertext':
                return $this->outertext();
            case 'innertext':
                return $this->innertext();
            case 'plaintext':
                return $this->plaintext();
        }
    }
}
