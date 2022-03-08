<?php

namespace Rocketsoba\DomParserWrapper;

use KubAT\PhpSimple\HtmlDomParser;
use Rocketsoba\DomParserWrapper\Exception\DomNotFoundException;
use Rocketsoba\DomParserWrapper\Exception\DomUnreadableException;
use LogicException;
use IteratorAggregate;

/**
 * PHP Simple HTML DOM Parserのラッパークラス
 *
 * 移譲のAdapterパターン、IteratorAggregateを使用
 */
class DomParserAdapter implements IteratorAggregate
{
    /**
     * 現在のDOM(HtmlDomParser)オブジェクト
     * @var \KubAT\PhpSimple\HtmlDomParser $current_dom
     */
    private $current_dom;
    /**
     * 要素が複数見つかり、DOM(HtmlDomParser)オブジェクトが複数あるときの配列
     * @var \KubAT\PhpSimple\HtmlDomParser[] $all_dom
     */
    private $all_dom;
    /**
     * 例外を許す
     * @todo 未実装
     * @var bool $allow_exception
     */
    private $allow_exception = true;
    /** @var bool $is_dom_many */
    private $is_dom_many = false;

    /**
     * DomParserWrapperのコンストラクタ
     *
     * @todo returnいらない
     * @throw DomUnreadableException HtmlDomParserでDOMを読み込めなかった場合(空文字列など)
     * @param string $raw_html スクレイピングしたいHTML
     */
    public function __construct($raw_html)
    {
        $this->current_dom = HtmlDomParser::str_get_html($raw_html);
        if ($this->current_dom === false && $this->allow_exception) {
            throw new DomUnreadableException();
        }
        return $this->current_dom;
    }

    /**
     * IteratorAggreagateのオーバーライド
     *
     * $all_domが存在していてこのクラスのオブジェクトに対してforeachが呼ばれた場合にyieldでイテレートする
     */
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

    /**
     * 一番最初に見つかる要素を走査し状態を保持
     *
     * メソッドチェーン可、$allow_exception == trueのとき例外の可能性
     *
     * @param  string $search_str
     * @return $this
     */
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
                 || strpos(get_class($this->current_dom), "simple_html_dom_node") === false
            )
            && $this->allow_exception) {
            throw new DomNotFoundException();
        }

        return $this;
    }

    /**
     * DOM内にある全ての要素を走査し状態を保持
     *
     * メソッドチェーン可、$allow_exception == trueのとき例外の可能性
     *
     * @param  string $search_str
     * @return $this
     */
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

    /**
     * 要素が一つだけであるか確認する
     *
     * 一つの時trueを返す
     *
     * @todo public化
     * @throws LogicException
     * @return bool
     */
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

    /**
     * HTMLエンティティをできるだけ取り除いた要素の文字列を返す
     *
     * 元クラスのメソッドに即しているためCamelCase、命名などは変更しない
     *
     * @todo tirm()するべき？
     * @return string|string[]
     */
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

    /**
     * 要素自体のHTMLタグを含まない要素全体の文字列を返す
     *
     * 元クラスのメソッドに即しているためCamelCase、命名などは変更しない
     *
     * @todo tirm()するべき？
     * @return string|string[]
     */
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

    /**
     * 要素自体のHTMLタグを含む要素全体の文字列を返す
     *
     * 元クラスのメソッドに即しているためCamelCase、命名などは変更しない
     *
     * @todo tirm()するべき？
     * @return string|string[]
     */
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

    /**
     * 要素のすべての属性を返す
     *
     * 元クラスのメソッドに即しているためCamelCase、命名などは変更しない
     *
     * @return array
     */
    public function getAllAttributes()
    {
        if (!is_null($this->current_dom) && !is_null($this->current_dom->attr)) {
            return $this->current_dom->attr;
        } else {
            return [];
        }
    }

    /**
     * 例外を許す
     *
     * @todo 未実装
     */
    public function denyException()
    {
        $this->allow_exception = false;

        return $this;
    }

    /**
     * PHPのマジックメソッド__get()
     *
     * 要素、属性のパブリックプロパティ化
     *
     * @param string $name 取得する要素、属性名
     * @return string|string[]|array
     */
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
