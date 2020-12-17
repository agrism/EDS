<?php


namespace Eds\Xml;


class Attribute
{
	public $key;
	public $value;

	public function __construct(string $key, string $value){
		$this->key = $key;
		$this->value = $value;
	}
}