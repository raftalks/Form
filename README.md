# Form Maker

Form Maker can help buildind forms in PHP. Specially developed package for Laravel 4.

#Updated to version 1.2.3
###Change Log
- Added support to create table, tr, th, td, thead, tbody, etc to generate html Table
- Added additional support for html container type tags
- Added secondary optional parameter to set method for container attributes, example $form->setNgBind($value, 'ng-bind'); which will force the attribute to be set as given in second parameter.


##Updated to version 1.2.2
###Change Log
- Added Support to Share Data across nested Closures
- Added method to share form errors 
- Added Support to putText inside a container
- Added support for tag element to fill an array of attributes using method setAttributes()
- Added method include_all(Closure $callback) to include template in all forms generated
- Fixed bug with Select input type

##Updated to version 1.2.0
###Change Log
- Added Support to create Macros
- Added Support to create global Html Tag Decorator
- Added Support to include Template structure for advanced UI interface

###Upgrade from version 1.0.0
- Download the updates using Composer update command 
- In Laravel 4, add the additional class Html class alias

## To Use with Laravel 4
Install the package via composer.
Now we need to put the class Alias inside L4 app/config/app.php file.
Find the aliases key which should be below the providers key and put the following inside its array.
```php
	'Form'	 => 'Form\Form',
	'Html'	 => 'Html\Html', //required to be added for version 1.2.0
```
Now you can try using the Form::make(function($form){ ...here you can put the form fields ...});


#Features
Following shows you how this package library is used to make forms.

### New Features added to version 1.2.3

```php

//making table based forms

$form->table(function($table)
{
	$table->thead(function($table)
	{
		$table->tr(function($tr)
		{
			$tr->th('item');
			$tr->th('category');
		});
	});


	$table->tr(function($tr)
	{	
		$tr->td('')->ng_bind('item.name','ng-bind');
		$tr->td('')->ng_bind('item.category','ng-bind');

		$tr->setNgRepeat('item in list','ng-repeat'); //using second parameter to force the attribute name.
	});

	$table->setClass('table');
});

```


#### New Features added to version 1.2.2

####Form elements common to all forms generated
When using with a framework like Laravel, you may want to include some element like hidden csrf token in all the forms. We can do this by making a template of csrf and putting it in the Form::include_all() method.

```php

Form::include_all(function()
{
	return Form::template('div',function($form)
	{
		$form->hidden('csrf_token')->value(Session::getToken());
		$form->setClass('token');
	});
});

```

Now we can share variables across nested closure methods. For example we want to pass the POST data or Error messages, which is used to set the value of the field.

```php
	
Form::make(function($form) use($usergroups, $validation_errors)
{
	
	$form->share('usergroups',$usrgroups);
	$form->share_errors($validation_errors);

	$form->div(function($form)
	{

		$usergroups = $form->get('usergroups');	
		$form->select('usergroup_id',trans('user.usergroup'))->options($usergroups);

	});

});

```

#### other new features added includes
```php

//sample function to filter errors
function_to_filter_error_by_field($fieldname, $errors)
{
	//filter errors and return matched error
}

//Macro created to show error message for fields
Form::macro('show_error',function($fieldName, $message=null)
{
	return Form::template('span',function($form) use($fieldName, $message)
	{
		$error_messages = $form->get_errors(); 
		$error_message = function_to_filter_error_by_field($fieldName, $error_messages);			


		// the contaner is <span></span> and we are
		// adding the error message as text
		$form->putText($error_message);
		
		// set the container class
		$form->setClass('help-block text-error');
		
	});
});


// Now we are creating a nother styled input text field which uses
// the above show error macro 

Form::macro('input_text', function($name, $label, $value=null, $attr = array())
{
	return Form::template('div',function($form) use ($name, $label, $attr, $value)
	{
			//notice the setAttribute method used here can fill the element with an array
			//of attributes

		$form->text($name)->placeholder($label)->value($value)->setAttributes($attr);
		
		//we are calling the macro to run the template and show the error message for this input template
		$form->show_error($name);

		$form->setClass('input');
	});
});


// Now we use everything above like this to make a real form.

Form::make(function($form) use($validation_errors)
{
	$form->share_errors($validation_errors);

	//This will run the above macro and will show error messages if any exists
	$form->input_text('username','User Name');

});

```


####Additional Features added to version 1.2.0

```php
//globaly apply attributes to tag elements
	
	//apply attribute to all text input fields
	Form::decorate('text',function($tag)
	{		
		$tag->class('class decorated');
	});

	//Use Form::decorate to apply attribute to all text input fields in templates
	Form::decorate('text',function($tag)
	{		
		$tag->class('class decorated');
	});


//Create Form Macros with template

	//bootstrap controlgroup textfield
	Form::macro('group_text',function($name, $label=null)
	{
		return Form::template(function($form) use($name, $label)
		{
			$form->label($label)->class('control-label');

			$form->div(function($form) use($name)
			{
				$form->text($name);
				$form->setClass('controls');
			});

			$form->setClass('group-controls');
		});

	});

	//the above Macro is now available as a Form field type and can be called within a Form 
	
	Form::make(function($form))
	{
		$form->group_text('telephone','Telephone Number');
	}

// will include more use cases later

```

###version 1.0.0 features
```php
echo Form::make(function($form)
{
		$form->div(function($form){ //makes a div container for the enclosed fields

			//creates a text input with label
			$form->text('username','User Name')->class('myname')->value('some name');  

			//creates a password input with label
			$form->password('password','Enter Password');

			$form->select('usergroup','User Group')->options(array('admin'=>'admin','manager'=>'manager','user'=>'user'),
									 array('user','admin'))->multiple('multiple');

			$form->setClass('input'); //sets container class
			$form->setId('UserAccount'); //sets container id
		});

		// creates an custom tag element like <group>dome</group> 
		$form->group('dome'); 

		//creates a fieldset container <fieldset></fieldset> and enclose the fields in it
		$form->fieldset(function($form) 
		{
			$form->legend('HelloWOrld');

			$form->label('Your Address')->for('address'); //create label field separately
			$form->text('address');
		});
		
		//create Angularjs type input
		$form->text('timer','Time')->ngmodel('time','ng-model');
		$form->select('countries','select country')->ngrepeat('country.name in countries','ng-repeat');

		$form->submit('Save');
		
		//sets container attributes, therefore, as this is form container, this sets the form attributes
		$form->setId('formIDhere');
		$form->setAction(URL::to('test'));
		$form->setMethod('POST');
		$form->setClass('fill-up');

});

```


## Documentation

will be updated soon.


## Copyright and License
FormMaker was written by Raftalks for the Laravel framework.
FormMaker is released under the MIT License. See the LICENSE file for details.

Copyright 2011-2012 Raftalks