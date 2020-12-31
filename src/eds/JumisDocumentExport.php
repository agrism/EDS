<?php


namespace Eds\Eds;


use Eds\Eds\JumisModels\FinDocument;
use Eds\Xml\Attribute;
use Eds\Xml\Element;

class JumisDocumentExport
{
	/**
	 * @var Element[]
	 */
	private $finDocuments = [];

	/**
	 * @var Element
	 */
	private $xml;

	/*** @return static */
	public static function factory(): self
	{
		return new self;
	}

	/**
	 * @return $this
	 */
	private function generateWholeDocument(): self
	{
		$this->xml = (new Element(''))
			->addChild(
				Element::factory('dataroot')->addChild(
					Element::factory('tjDocument')
						->addAttribute(Attribute::factory('Version', 'TJ5.5.101'))
				)->addChild(
					Element::factory('tjResponse')
						->addAttribute(Attribute::factory('Name', 'FinancialDoc'))
						->addAttribute(Attribute::factory('Operation', 'Read'))
						->addAttribute(Attribute::factory('Version', 'TJ7.0.112'))
						->addAttribute(Attribute::factory('Structure', 'Tree'))
						->addAttribute(Attribute::factory('RequestID', 'FinancialDoc_2'))
						->addChild((function (): Element {
							$root = Element::factory('');

							foreach ($this->finDocuments as $finDocElement) {
								$root->addChild($finDocElement->getElement());
							}

							return $root;
						})())
				)
			);

		return $this;
	}

	/**
	 * @param  false  $print
	 * @return string
	 */
	public function render($print = false): string
	{
		return $this
			->generateWholeDocument()
			->xml
			->render($print);
	}

	public function getFinDocuments(): array
	{
		return $this->finDocuments;
	}


	/**
	 * @param  FinDocument  $finDocument
	 * @return $this
	 */
	public function addFinancialDocument(FinDocument $finDocument): self
	{
		$this->finDocuments[] = $finDocument;
		return $this;
	}
}