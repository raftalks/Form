<?php
namespace Form;
use Html\HtmlTag;
use Html\HtmlMaker;
use Html\HtmlHandler;
use Closure;


class FormHandler extends HtmlHandler
{

	protected $defaultTag = 'form';

	//protected $macros = array();

	public function make(Closure $callback)
	{
		
		$this->root = new HtmlTag($this->defaultTag);
		
		$HtmlMaker = new HtmlMaker(clone($this->root), $this->decorator, $this->macros);

		$callback($HtmlMaker);

		return $HtmlMaker->render();
	}
}

