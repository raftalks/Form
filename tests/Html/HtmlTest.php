<?php 
use Raftalks\Html\Html;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->html = new Html;

	}


	/**
	 * @expectedException	InvalidArgumentException
	 */
	public function testInvalidArgumentException()
	{
		$this->html->make();
	}


	public function testFormEmptyFormElement()
	{
		$html = $this->html->make(function($html){

		});

		$this->assertEquals('<div />', $html);
	}
}