<?php
namespace Html;

use Closure;


class HtmlHandler
{

	protected $root;

	protected $decorator;

	protected $defaultTag = 'div';

	protected $macros = array();


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

		$HtmlMaker = new HtmlMaker(clone($this->root), $this->decorator, $this->macros);

		$callback($HtmlMaker);

		if($ReturnRoot)
		{
			return $HtmlMaker->getRoot();
		}

		return $HtmlMaker->render();
	}


	public function decorate($name, Closure $callback)
	{
			$this->decorator->addTemplate($name, $callback);
	}


	public function macro($name, Closure $callback)
	{
		$this->macros[$name] = $callback;
	}

	public function template($tag, $callback=null)
	{
		return $this->make($tag, $callback, true);
	}

}

