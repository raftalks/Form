<?php namespace Raftalks\Formmaker\Html;

class HtmlTag extends HtmlTagComposite
{

	//Tag example <img />
	private $tagOpen = '<[@TAG] [@ATTR]';
	private $tagClose = '/>';

	//TagContainer #example <html></html>
	private $tagContOpen = '<[@TAG] [@ATTR]>';
	private $tagContClose = '</[@TAG]>';

	private $tagName = '';
	private $tagAttr = null;
	private $tagContainer = false; //boolean defines if tag is containing other tags

	protected $tagChildren = array();
	protected $tagText = null;

	private $originType = null;



	public function __construct($name, $originType=null , $container = false)
	{
		$this->setName($name);
		$this->originType = $originType;
		$this->setContainer($container);
	}

	public function testConnect()
	{
		die('Hello from HtmlTag');
	}

	public function setName($name){

		$this->tagName = $name;
	}

	public function setContainer($bool = true)
	{
		$this->tagContainer = $bool;
	}

	public function getTagName()
	{
		return $this->tagName;
	}

	public function getOriginType()
	{
		return $this->originType;
	}

	public function setAttribute($name, $value = null){

		if(is_null($this->tagAttr))
		{
			$this->tagAttr = array();
		}

		if(isset($this->tagAttr[$name]))
		{
			$attr = $this->tagAttr[$name] . ' ' . $value;
			$value = $this->removeDuplicateWords($attr);
		}

		$this->tagAttr[$name] = $value;
		
	}

	public function addTag(HtmlTagComposite $tag){

		$this->tagContainer = true;

		$this->tagChildren[] = $tag;

		return $this; //$tag;
	}


	public function addText($text)
	{
		$this->tagContainer = true;

		$this->tagText = $text;
	}

	public function render(){

		return $this->tagToString();
	}

	public function count(){

		$total = 1;
        foreach ($this->tagChildren as $child) {
           $total += $child->number();
        }
        return $total;

	}

	function __clone() {
        $tags = array();
        foreach ($this->tagChildren as $child) {
            $tags[] = clone($child);
        }
        $this->tagchildren = $tags;
    }


    protected function tagToString($level = 0)
    {
    	$html = $this->getTemplate();

    	$pad = $this->pad($level);

    	$html = $this->writeTagNameToTemplate($html);
    	$html = $this->writeAttrToTemplate($html);

    	$html = $this->writeBodyToTemplate($html);

    	$output = $pad . $html;

    	return $output;

    }



    private function getTemplate()
    {
    	if($this->tagContainer)
    	{
    		$template = $this->tagContOpen
    					.'[@BODY]'
    					.$this->tagContClose;

    	}
    	else
    	{
    		$template = $this->tagOpen .$this->tagClose;
    	}

    	return $template;
    }


    private function writeTagNameToTemplate($template)
    {

    	$name = $this->tagName;

    	$compose = array('[@TAG]'=> $name);

    	return $this->renderTemplate($template, $compose);
    }


    private function writeAttrToTemplate($template)
    {
    	$attr = $this->tagAttr;

    	$attrStr = $this->attributeToStringFormat($attr);

    	$compose = array('[@ATTR]'=> $attrStr);

    	return $this->renderTemplate($template, $compose);
    }


    private function writeBodyToTemplate($template)
    {
    	$tagText = $this->tagText;

		foreach($this->tagChildren as $child)
		{
			$tagText .= $child->tagToString() . PHP_EOL;
		}

    	$compose = array('[@BODY]'=>$tagText);

    	return $this->renderTemplate($template, $compose);

    }




    private function attributeToStringFormat($attr)
	{
		$output = '';
		if(is_array($attr))
		{
			foreach($attr as $key => $value)
			{
				$output .= "$key='$value' ";
			}
		}
		return $output;
	}

	

	private function renderTemplate($template, $compose)
	{
		$html = $template;

		foreach($compose as $search => $replace)
		{
			$html = str_replace($search, $replace, $html);
		}
				
		return $html;
	}

}