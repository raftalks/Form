<?php
namespace Form;
use Html\TagDecorator;
use InvalidArgumentException;

class Form
{

	public static $handler;

	public static $decorator;
	
	
	public static function resolveFacadeInstance()
	{
		if (is_object(static::$handler)) return static::$handler;

		$decorator = static::getDecorator();

		return static::$handler = new FormHandler($decorator);
	}

	public static function getDecorator()
	{
		if (is_object(static::$decorator)) return static::$decorator;

		return static::$decorator = new TagDecorator();
	}



	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param  string  $method
	 * @param  array   $args
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		return static::runCallback($method, $args);
	}


	public function __call($method, $args)
	{
		return static::runCallback($method, $args);
	}


	protected static function runCallback($method, $args)
	{
		$instance = static::resolveFacadeInstance();

		if(empty($args))
		{
			throw new InvalidArgumentException("Please provide an argument to this method");
			
		}

		switch (count($args))
		{
			case 0:
				return $instance->$method();

			case 1:
				return $instance->$method($args[0]);

			case 2:
				return $instance->$method($args[0], $args[1]);

			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);

			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}


	
}