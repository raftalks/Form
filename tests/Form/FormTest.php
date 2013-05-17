<?php namespace Form;

class FormTest extends TestCase
{

	

	/**
	 * @expectedException	InvalidArgumentException
	 */
	public function testInvalidArgumentException()
	{
		$this->form->make();
	}



	public function testFormEmptyFormElement()
	{
		$form = $this->form->make(function($form){

		});

		$this->assertEquals('<form />', $form);
	}


	public function testFormAttributeMethodSetAsPost()
	{
		$form = $this->form->make(function($form)
		{
			$form->setMethod('POST');
		});

		$this->assertEquals("<form method='POST' />", $form);
	}

	public function testFormTagWithMultipleAttributes()
	{
		$form = $this->form->make(function($form)
		{
			$form->setRootAttr('class','formClass');
			$form->setClass('anotherClass');
			$form->setId('formId');
			$form->setCustom('isCustomAttribute');
		});

		$this->assertEquals("<form class='formClass anotherClass' id='formId' custom='isCustomAttribute' />", $form);
	}


	public function testFormWithInputTextElement()
	{
		$form = $this->form->make(function($form)
		{
			$form->text('fieldName');
		});

		$this->assertEquals("<form ><input name='fieldName' type='text' />\n</form>", $form);
	}



	public function testFormInputElementWithAutoLabel()
	{
		$form = $this->form->make(function($form)
		{
			$form->text('fieldName','Input Label');
		});

		$this->assertEquals("<form ><label for='fieldName' >Input Label</label>\n<input name='fieldName' type='text' />\n</form>", $form);
	}


	public function testFormInputElementWithLabelAndSetValue()
	{
		$form = $this->form->make(function($form)
		{
			$form->text('fieldName','Input Label')->value('Here is the content');
		});

		$this->assertEquals("<form ><label for='fieldName' >Input Label</label>\n<input name='fieldName' type='text' value='Here is the content' />\n</form>", $form);
	}



	public function testFormInputElementWithLabelAndSetValueDifferentWay()
	{
		$form = $this->form->make(function($form)
		{
			$form->label('Input Label')->for('fieldName');
			$form->text('fieldName')->value('Here is the content');
		});

		$this->assertEquals("<form ><label for='fieldName' >Input Label</label>\n<input name='fieldName' type='text' value='Here is the content' />\n</form>", $form);
	}



	public function testFormInputElementWithCustomAttributeNamePassedUsingSecondParameter()
	{
		$form = $this->form->make(function($form)
		{
			$form->label('Input Label')->for('fieldName');
			$form->text('fieldName')->value('Here is the content')->ng_model('modelName','ng-model');
		});

		$this->assertEquals("<form ><label for='fieldName' >Input Label</label>\n<input name='fieldName' type='text' value='Here is the content' ng-model='modelName' />\n</form>", $form);
	}



	public function testFormWithTextareaElement()
	{
		$form = $this->form->make(function($form)
		{
			$form->textarea('content here');
		});

		$this->assertEquals("<form ><textarea >content here</textarea>\n</form>", $form);
	}





}