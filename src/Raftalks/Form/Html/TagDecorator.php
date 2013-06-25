<?php namespace Raftalks\Form\Html;

use Closure;


class TagDecorator
{

	protected $templates = array();

	

	public function decorate(HtmlTag $tag)
	{
		$tagName 		= $tag->getTagName();
		$originType 	= $tag->getOriginType();

		$callback = $this->getTemplate($originType);
		
		if(!is_null($callback))
		{
			$elem = new ElementHandler($tag);
			$callback($elem);
		}
	}


	public function addTemplate($name, Closure $callback)
	{
		$this->templates[$name] = $callback;
	}


	public function getTemplate($name)
	{
		if(isset($this->templates[$name]))
		{
			return $this->templates[$name];
		}
		return null;
	}
}