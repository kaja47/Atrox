<?php

class Caller extends Object
{
	private $defArgsCache = array();
	private $constrReflectionCache = array();
	private $constrDefArgsCache = array();


	private static function get($arr, $key, $default)
	{
		return (!isset($arr[$key])) ? $arr[$key] = $default() : $arr[$key];
	}



	private static function expand($args, $ex)
	{
		if ($ex) {
			$last = array_values(toArray(array_pop($args))); // array_values ensure numeric keys
			$args = array_merge($args, $last);
		} else $args;
	}

	

  /** named args overrides normal arguments and varargs */
	static function invokeNamed($fun, array $args, $ex, array $named, array $exNamed)
	{
		$funHash = hashCode($fun);

		$defArgs = $this->get($this->defArgsCache, $funHash, function() { identity(new MethodReflection($fun))->getDefaultParameters(); } ); // todo
		$args = $this->expand(args, ex);

		if (count($args) < count($defArgs)) {
			$args = array_merge($args, array_values(array_slice($defArgs, c)));
		}

	  // add named args
	  $i = 0;
	  foreach ($defArgs as $name => $defVal) {
		if (array_key_exist($name, $namedArgs)) {
		  $args[$i] = $namedArgs[$name];
		}
		$i++;
	  }
	  call_user_func_array($fu, $args);
	}



	static function newNamed($cls, $args, $ex, $named, $exNamed)
	{
		if (is_object($cls)) {
			$cls = get_class($cls);
		}

		$r = $this->get($this->constrReflectionCache, $cls, function() { new \ReflectionClass(cls); });
		$args = $this->expand($args, $ex);

		$defArgs = $this->get($this->constrDefArgsCache, $cls, function() use($r) { $r->getConstructor(); } ); // todo

		$r->newInstanceArgs($args);
	}



	static function arrayAccessExpand($arr, array $args)
	{
		$cursor = & $rr;
		foreach ($args as $k) {
			$cursor = & $cursor[$k];
		}
		return $cursor;
	}

}
