<?php
namespace Form;

use Html\HtmlTag;
use Closure;

abstract class FormComposer
{

	protected $root;

	

	public function __construct($tag='form')
	{
		$this->root = new HtmlTag($tag);
	}


	

	private function newInstance($tag)
	{
		$FormComposer =  new Static($tag);
		return $FormComposer;
	}

	private function newHtmlTagInstance($tag=null)
	{
		return new HtmlTag($tag);
	}

	public static function __callStatic($method, $arguments)
	{

		if(!method_exists(__CLASS__, $method))
		{
			switch($method)
			{
				case 'make':
					return call_user_func_array(static::maker(), $arguments);
				break;
			}
		}
	}
}