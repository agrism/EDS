<?php


namespace Eds\Xml;


class Attribute
{
	public $key;
	public $value;

	public function __construct(string $key, string $value)
	{
		$this->key = $key;
		$this->value = $value;
	}

	public static function factory(string $key, string $value): self
	{
		return new self($key, $value);
	}
}