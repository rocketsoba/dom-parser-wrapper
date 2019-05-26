<?php

namespace DomParserWrapper;

use \Sunra\PhpSimple\HtmlDomParser;
use \DomParserWrapper\Exception\DomNotFoundException;
use \DomParserWrapper\Exception\DomUnreadableException;
use \LogicException;

class DomParserAdapter
{
    private $dom;
    private $is_dom_many = false;
    private $allow_exception = true;

    public function __construct($raw_html)
    {
        $this->dom = HtmlDomParser::str_get_html($raw_html);
        if ($this->dom === false && $this->allow_exception) {
            throw new DomUnreadableException();
        }
        return $this->dom;
    }

    public function findOne($search_str)
    {
        if ($this->validateSingleDom()) {
            $this->dom = $this->dom->find($search_str, 0);
        } else {
            foreach ($this->dom as $idx1 => $val1) {
                $current_dom = $val1->find($search_str, 0);

                if (!is_null($current_dom)) {
                    $this->dom = $current_dom;
                }
            }
        }

        if (is_null($this->dom) && $this->allow_exception) {
            throw new DomNotFoundException();
        }

        return $this;
    }

    public function findMany($search_str)
    {
        if ($this->validateSingleDom()) {
            $this->dom = $this->dom->find($search_str);
        } else {
            $all_dom = [];

            foreach ($this->dom as $idx1 => $val1) {
                $current_dom = $val1->find($search_str);
                $all_dom = array_merge($all_dom, $current_dom);
            }
        }

        if (empty($this->dom) && $this->allow_exception) {
            throw new DomNotFoundException();
        }

        return $this;
    }

    private function validateSingleDom()
    {
        if (empty($this->dom)) {
            throw LogicException("Somehow dom is invalid");
        }

        if (is_array($this->dom)) {
            if (count($this->dom) == 0) {
                throw LogicException("Somehow dom is invalid");
            } elseif (count($this->dom) == 1) {
                $this->dom = $this->dom[0];
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    private function plaintext()
    {
        if ($this->validateSingleDom()) {
            return $this->dom->plaintext;
        } else {
            $all_text = [];
            foreach ($this->dom as $idx1 => $val1) {
                $all_text[] = $val1->plaintext;
            }

            return $all_text;
        }
    }

    private function innertext()
    {
        if ($this->validateSingleDom()) {
            return $this->dom->innertext;
        } else {
            $all_text = [];
            foreach ($this->dom as $idx1 => $val1) {
                $all_text[] = $val1->innertext;
            }

            return $all_text;
        }
    }

    private function outertext()
    {
        if ($this->validateSingleDom()) {
            return $this->dom->outertext;
        } else {
            $all_text = [];
            foreach ($this->dom as $idx1 => $val1) {
                $all_text[] = $val1->outertext;
            }

            return $all_text;
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
