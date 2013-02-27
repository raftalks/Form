<?php
namespace Html;

use Closure;


class HtmlHandler
{

	protected $root;

	protected $decorator;

	protected $defaultTag = 'div';

	protected $macros = array();


	protected static $instances = array();


	public function __construct(TagDecorator $decorator)
	{
		$this->decorator = $decorator;
	}


	public function make($tag, $callback = null, $ReturnRoot = false)
	{
		if($tag instanceOf Closure)
		{
			$this->root = new HtmlTag($this->defaultTag);
			$callback = $tag;
		}
		else
		{
			if($callback instanceOf Closure)
			{
				$this->root = new HtmlTag($tag);
			}
		}

		$HtmlMaker = $this->getInstanceHtmlMaker(); 

		$callback($HtmlMaker);

		if($ReturnRoot)
		{
			return $HtmlMaker->getRoot();
		}

		return $HtmlMaker->render();
	}

	
	protected function getInstanceHtmlMaker()
	{
		$global_vars = array();
		if(isset(static::$instances['HtmlMaker']))
		{
			$lastHtmlMaker = static::$instances['HtmlMaker'];
			$global_vars = $lastHtmlMaker->getGlobalVars();
		}

		$HtmlMaker =  new HtmlMaker($this->root, $this->decorator, $this->macros, $global_vars);
        static::$instances['HtmlMaker'] = $HtmlMaker;
        
        return $HtmlMaker;
	}


	public function decorate($name, Closure $callback)
	{
			$this->decorator->addTemplate($name, $callback);
	}


	public function macro($name, Closure $callback)
	{
		$this->macros[$name] = $callback;
	}

	public function getMacro($name)
	{
		if(!isset($this->macros[$name]))
		{
			return false;
		}
		
		return $this->macros[$name];
	}
	

	public function template($tag, $callback=null)
	{
		
		return $this->make($tag, $callback, true);
	}


	//include the template element in all the forms generated
	public function include_all(Closure $callback)
	{
		$this->macros['_include_in_all_'] = $callback;
	}

}

