<?php

namespace Eds\Eds\JumisModels;

use Eds\Xml\Element;

class FinDocumentLine
{
	/**
	 * @var Element
	 */
	private $element;

	public function __construct()
	{
		$this->element = Element::factory('FinancialDocLine');
	}

	public function create(
		$lineSupplementaryNoticeID,
		$lineCurrency,
		$lineAmount,
		$lineDebetAccountCode,
		$lineCreditAccountCode,
		$lineVatRate

	): self {
		$this->element
			->addChild(Element::factory('LineSupplementaryNoticeID')->addContent($lineSupplementaryNoticeID))
			->addChild(Element::factory('LineCurrency')->addContent( $lineCurrency))
			->addChild(Element::factory('LineAmount')->addContent( $lineAmount))
			->addChild(Element::factory('LineDebetAccountCode')->addContent( $lineDebetAccountCode))
			->addChild(Element::factory('LineCreditAccountCode')->addContent( $lineCreditAccountCode))
			->addChild(Element::factory('LineVatRate')->addContent( $lineVatRate));

		return $this;
	}

	public static function factory(): self
	{
		return new self;
	}

	public function getElement(): Element
	{
		return $this->element ?? Element::factory();
	}
}