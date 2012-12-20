<?php
namespace Form;
use Html\HtmlTag;
use Closure;

class FormMaker
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

	protected $fieldsHavingTypesAttr = array('input');
	protected $fieldsContainerTypes = array('label','legend','option','button','textarea');

	//Instantiate the FormMaker setting up root element
	public function __construct($root)
	{
		$this->root = $root;
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
			$group_root = $this->newElementInstance($tag);
			$formMaker = new FormMaker($group_root);
			$callback($formMaker);

			$this->root->addTag($formMaker->getRoot());
		}

		if(is_null($callback))
		{
			$this->makeElement('group', $tag,null,true);
		}
	}


	protected function newElementInstance($tag)
	{
		return new HtmlTag($tag);
	}


	protected function makeElement($type, $name = null, $label = null, $container = false)
	{
		if($this->checkInputType($type))
		{
			$tagName = $this->fieldTypes[$type];
		}
		else
		{
			$tagName = $type;
		}

		$tag = $this->newElementInstance($tagName);

		if(in_array($tagName, $this->fieldsContainerTypes))
		{
			
			$container = true;
		}

		if($container==false)
		{
			if(!is_null($name))
			{
				$tag->setAttribute('name', $name);
			}
		}
		else
		{
			if(!is_null($name))
			{
				$tag->addText($name);
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
			$labelTag = $this->newElementInstance('label');
			$labelTag->setAttribute('for',$name);
			$labelTag->addText($label);

			$this->root->addTag($labelTag);
		}

		$this->root->addTag($tag);

		return new ElementHandler($tag);

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


	protected function checkInputType($type)
	{
		return isset($this->fieldTypes[$type]);
	}
	

	public function __call($method, $args)
	{
		//check if method starts with 'set' to add Form attribute
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

			return $this->makeElement($method, $name, $label);

			//since there is no requirement to acccess the root directly this is disabled
			//return call_user_func_array(array($this->root, $method), $args);
		}

		
	}
}



class ElementHandler
{
	protected $element;
	protected $type;

	public function __construct(HtmlTag $element)
	{
		$this->element = $element;
		$this->type = $element->getTagName();
	}


	public function setAttribute($name, $value, $extra = null)
	{
		if($name !== 'options')
		{
			$this->element->setAttribute($name, $value);
		}
		else
		{
			$this->addOptionsToSelect($name, $value, $extra);
		}

		return $this;
	}


	public function addChildren(HtmlTag $child)
	{
		$this->element->addTag($child);
		return $this;
	}


	public function addOptionsToSelect($name, $value, $selected)
	{
		$options = $value;
		$tags = array();

		if(!is_array($selected))
		{
			$selected = array($selected);
		}

		if(is_array($options))
		{
			foreach($options as $val => $label)
			{
				$tag = $this->newElementInstance('option');
				
				if(in_array($val, $selected))
				{
					$tag->setAttribute('selected','selected');
				}

				$tag->addText($label);
				$this->addChildren($tag);

			}
		}
		

	}


	protected function newElementInstance($tag)
	{
		return new HtmlTag($tag);
	}


	public function __call($method, $args)
	{

		$value = $args[0];
		$name = $method;
		$extra = null;
		if(isset($args[1]))
		{
			if($method !== 'options')
			{
				$name = $args[1];
			}else
			{
				$extra = $args[1];
			}
		}
		return $this->setAttribute($name, $value, $extra);
	}

}