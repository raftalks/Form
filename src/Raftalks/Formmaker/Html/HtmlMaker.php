<?php namespace Raftalks\Formmaker\Html;
use Closure;

class HtmlMaker
{
	protected $root;

	protected $fieldTypes = array(
				'text'		=> 'input',
				'textarea'	=> 'textarea',
				'number'	=> 'input',
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
				'hidden'	=> 'input',
		);


	protected $decorator = null;

	protected $fieldsHavingTypesAttr = array('input');
	protected $fieldsContainerTypes = array('form','fieldset','label','legend','option','button','textarea','select','a','ol','ul','li','i','p','h1','h2','h3','h4','h5','span','table','tr','th','td','thead','tbody');

	protected $macros = array();

	protected $global_vars = array();

	protected $macro_include_in_all = '_include_in_all_';


	//Instantiate the HtmlMaker setting up root element and decorator
	public function __construct($root, TagDecorator $decorator, $macros, $global_vars)
	{
		$this->root = $root;
		$this->decorator = $decorator;
		$this->macros = $macros;
		$this->global_vars = $global_vars;
	}

	public function getGlobalVars()
	{
		return $this->global_vars;
	}


	public function getRoot()
	{
		return $this->root;
	}

	//share data to root scope to enable access from nested closures
	public function share($name, $value)
	{
		$this->global_vars[$name] = $value;
	}

	public function share_errors($errors)
	{
		$this->global_vars['error_messages'] = $errors;
	}

	public function get_errors()
	{
		 return $this->get('error_messages');
	}

	//get data from roots scope from any where within a Form closure
	public function get($name)
	{
		if(isset($this->global_vars[$name]))
		{
			return $this->global_vars[$name];
		}

		return null;
	}

	//put text into container
	public function putText($text)
	{
		$this->root->addtext($text);
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
		return new Static($root , $this->decorator, $this->macros, $this->global_vars);
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
	public function setRootAttr($name, $value)
	{
		$this->root->setAttribute($name, $value);

	}


	public function render()
	{
		if($this->isMacro($this->macro_include_in_all))
		{
			$this->RunMacro($this->macro_include_in_all);
		}

		return $this->root->render();
	}


	protected function RunMacro($macro, $args=array())
	{
		
		$macroCallback = $this->macros[$macro];
		//$htmlMaker = '';
		$element = call_user_func_array($macroCallback, $args);
		
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
			
			if(isset($args[1]))
			{
				$name = $args[1];
			}
			
			return $this->setRootAttr($name, $args[0]);

		}
		else
		{

			$name = isset($args[0]) ? $args[0] : null;
			$label = isset($args[1]) ? $args[1] : null;

			if($name instanceOf Closure)
			{
				$callback = $name;
				return $this->container($method, $callback);
			}

			//check if method is calling a macro
			if($this->isMacro($method))
			{
				return $this->RunMacro($method, $args);
			}

			return $this->makeElement($method, $name, $label);

		}

		
	}
}



