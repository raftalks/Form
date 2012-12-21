<?php
namespace Html;

abstract class HtmlTagComposite
{

	abstract public function setName($name);

	abstract public function getTagName();

	abstract public function setAttribute($name, $value);

	abstract public function addTag(HtmlTagComposite $tag);

	abstract public function addText($text);

	abstract public function render();

	abstract public function count();

	public function pad($level) {
       
        $ret = "";
        
        for ($x = 0; $x < $level; $x++) {
            $ret .= "    ";
        }
        
        return $ret;
    }

    protected function removeDuplicateWords($attr)
	{
		$str = str_replace(' ', ',', $attr);
		$str = implode(' ',array_unique(explode(',', $str)));
		return $str;
	}

}