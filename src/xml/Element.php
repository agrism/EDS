<?php


namespace Eds\Xml;


class Element
{
	/*** @var string */
	public $name;

	/*** @var Element[] */
	public $children = [];

	/*** @var Attribute[] */
	public $attributes = [];

	/*** @var string[] */
	public $content = [];

	/** @var array */
	public $rendered = [];

	public function __construct(string $name = null)
	{
		$this->name = $name;
	}

	public static function factory(string $name = null): self
	{
		return new self($name);
	}


	/**
	 * @param  Attribute  $attribute
	 * @return Element
	 */
	public function addAttribute(Attribute $attribute): self
	{
		$this->attributes[] = $attribute;

		return $this;
	}

	/**
	 * @return Attribute[]
	 */
	public function getAttributes(): array
	{
		return $this->attributes;
	}

	/**
	 * @param  Element  $element
	 * @return $this
	 */
	public function addChild(Element $element): self
	{
		$this->children[] = $element;

		return $this;
	}

	/**
	 * @param  string  $content
	 * @return $this
	 */
	public function addContent(string $content): self
	{
		$this->content[] = $content;

		return $this;
	}

	public function render($print = false): string
	{
		$this->rendered[] = '<?xml version="1.0" encoding="utf-8"?>';
		foreach ($this->children as $child) {
			$this->renderSelf($child);
		}

		$return = implode('', $this->rendered);

		if ($print) {
			echo $return;
		}

		return $return;
	}

	private function renderSelf(Element $element)
	{
		if ($element->name) {
			$this->rendered[] = '<'.$element->name;
			foreach ($element->getAttributes() as $attr) {
				$this->rendered[] = ' ';
				$this->rendered[] = $attr->key;
				$this->rendered[] = '=';
				$this->rendered[] = '"'.$attr->value.'"';
			}

			$this->rendered[] = '>';

			foreach ($element->content as $content) {
				$this->rendered[] = $content;
			}
		}

		foreach ($element->children as $child) {
			$this->renderSelf($child);
		}

		if ($element->name) {
			$this->rendered[] = '</'.$element->name.'>';
		}
	}

}