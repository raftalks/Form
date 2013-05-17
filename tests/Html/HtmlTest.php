<?php namespace Html;

class HtmlTest extends TestCase
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