<?php
namespace Html;
use Closure;

class HtmlMaker
{
	protected $root;

	protected $fieldTypes = array(
				'text'		=> 'input',
				'textarea'	=> 'textarea',
				'select'	=> 'select',
				'date'		=> 'input',
				'time'		=> 'input',
				'range'		=> 'input',
				'label'		=> 'label',
				'submit'	=> 'input',
				'button'	=> 'button',
				'reset'		=> 'input',
				'checkbox'	=> 'input',
				'radio'		=> 'input',
				'password'	=> 'input',
				'legend'	=> 'legend',

		);


	protected $decorator = null;

	protected $fieldsHavingTypesAttr = array('input');
	protected $fieldsContainerTypes = array('label','legend','option','button','textarea','select');

	protected $macros = array();


	//Instantiate the HtmlMaker setting up root element and decorator
	public function __construct($root, TagDecorator $decorator, $macros)
	{
		$this->root = $root;
		$this->decorator = $decorator;
		$this->macros = $macros;
	}


	public function getRoot()
	{
		return $this->root;
	}


	public function container($tag, $callback=null)
	{
		if($tag instanceOf Closure)
		{
			$callback = $tag;
			$tag = 'div';
		}

		if($callback instanceOf Closure)
		{
			$originType = $tag;
			$group_root = $this->newElementInstance($tag, $originType);
			$HtmlMaker = $this->newInstanceOfHtmlMaker($group_root);
			$callback($HtmlMaker);
			$this->addToRoot($HtmlMaker->getRoot());
		}

		if(is_null($callback))
		{
			$this->makeElement('group', $tag,null,true);
		}
	}

	protected function newInstanceOfHtmlMaker($root)
	{
		return new Static($root , $this->decorator, $this->macros);
	}

	protected function newElementInstance($tag, $originType = null, $container =false)
	{
		return new HtmlTag($tag, $originType, $container);
	}


	protected function findTagName($type)
	{
		if($this->checkInputType($type))
		{
			$tagName = $this->fieldTypes[$type];
		}
		else
		{
			$tagName = $type;
		}

		return $tagName;
	}

	protected function makeElement($type, $name = null, $label = null, $container = false)
	{

		$tagName = $this->findTagName($type);

		if(in_array($tagName, $this->fieldsContainerTypes)){  $container = true; }

		$tag = $this->newElementInstance($tagName, $type, $container);

		if($container !== true)
		{
			if(!is_null($name)) $tag->setAttribute('name', $name);
			
		}
		else
		{
			if(!is_null($name))
			{
				if($tagName == 'select')
					{
						$tag->setAttribute('name', $name);
					}
				else
					{
						$tag->addText($name);
					}
			}
		}

		//check if field type attribute can be set to the input
		$inputType = strtolower($tagName);
		if(in_array($inputType, $this->fieldsHavingTypesAttr))
		{
			$tag->setAttribute('type',$type);
		}

		

		if(!is_null($label))
		{
			$labelTag = $this->newElementInstance('label','label');
			$labelTag->setAttribute('for',$name);
			$labelTag->addText($label);

			$this->addToRoot($labelTag);
		}

		$this->addToRoot($tag);

		return new ElementHandler($tag);

	}


	protected function addToRoot(HtmlTag $tag)
	{
		
		$this->decorator->decorate($tag);

		$this->root->addTag($tag);
	}


	//sets root element attributes
	protected function setRootAttr($name, $value)
	{
		$this->root->setAttribute($name, $value);

	}


	public function render()
	{
		return $this->root->render();
	}


	protected function RunMacro($macro, $name, $label)
	{
		$macroCallback = $this->macros[$macro];
		//$htmlMaker = '';
		$element = $macroCallback($name, $label);

		$this->addToRoot($element);
		
	}


	protected function checkInputType($type)
	{
		return isset($this->fieldTypes[$type]);
	}

	//checks if the tag is calling for a macro
	protected function isMacro($name)
	{
		return isset($this->macros[$name]);
	}
	

	public function __call($method, $args)
	{
		//check if method starts with 'set' to add container attribute
		if(strpos($method, 'set') === 0)
		{
			$name = str_replace('set', '', $method);

			$name = strtolower($name);
			
			return $this->setRootAttr($name, $args[0]);

		}
		else
		{

			$name = $args[0];
			$label = isset($args[1]) ? $args[1] : null;

			if($name instanceOf Closure)
			{
				$callback = $name;
				return $this->container($method, $callback);
			}

			//check if method is calling a macro
			if($this->isMacro($method))
			{
				return $this->RunMacro($method, $name, $label);
			}

			return $this->makeElement($method, $name, $label);

		}

		
	}
}



