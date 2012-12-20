<?php
namespace Form;
use Html\htmlTag;
use Closure;


class FormHandler
{

	protected $root;

	public function __construct()
	{
		$this->root = new HtmlTag('form');
	}


	public function make(Closure $callback)
	{
		$formMaker = new FormMaker(clone($this->root));

		$callback($formMaker);

		return $formMaker->render();
	}

}

