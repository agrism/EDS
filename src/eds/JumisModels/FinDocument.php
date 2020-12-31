<?php

namespace Eds\Eds\JumisModels;

use Eds\Xml\Element;

class FinDocument
{
	/**
	 * @var Element
	 */
	private $finDoc;

	/**
	 * @var Element[]
	 */
	private $finDocLines = [];

	public function __construct()
	{
		$this->finDoc = Element::factory('FinancialDoc');
	}

	public static function factory(): self
	{
		return new self;
	}

	public function create(
		$docNo,
		$docNoSerial,
		$docDate,
		$docGroupAbbreviation = 'D',
		$docCurrency,
		$docAmount,
		$docCompanyVatNoCountryCode,
		$docCompanyVatNo,
		$docPartnerName,
		$docPartnerVatNoCountryCode,
		$docPartnerRegistrationNo,
		$docPartnerVatNo,
		$docDisbursementNoticeID = '0',
		$docDisbursementTerm = '',
		$docComments = ''
	): self {
		$this->finDoc = Element::factory('FinancialDoc')
			->addChild(Element::factory('DocNo')->addContent($docNo))
			->addChild(Element::factory('DocNoSerial')->addContent($docNoSerial))
			->addChild(Element::factory('DocDate')->addContent($docDate))
			->addChild(Element::factory('DocGroupAbbreviation')->addContent($docGroupAbbreviation))
			->addChild(Element::factory('DocCurrency')->addContent($docCurrency))
			->addChild(Element::factory('DocAmount')->addContent($docAmount))
			->addChild(Element::factory('DocCompanyVatNoCountryCode')->addContent($docCompanyVatNoCountryCode))
			->addChild(Element::factory('DocCompanyVatNo')->addContent($docCompanyVatNo))
			->addChild(Element::factory('DocPartnerName')->addContent($docPartnerName))
			->addChild(Element::factory('DocPartnerVatNoCountryCode')->addContent($docPartnerVatNoCountryCode))
			->addChild(Element::factory('DocPartnerRegistrationNo')->addContent($docPartnerRegistrationNo))
			->addChild(Element::factory('DocPartnerVatNo')->addContent($docPartnerVatNo))
			->addChild(Element::factory('DocDisbursementNoticeID')->addContent($docDisbursementNoticeID))
			->addChild(Element::factory('DocDisbursementTerm')->addContent($docDisbursementTerm))
			->addChild(Element::factory('DocComments')->addContent($docComments));

		return $this;
	}

	public function addFinDocLine(FinDocumentLine $finDocLine): self
	{
		$this->finDocLines[] = $finDocLine;

		return $this;
	}

	public function getElement(): Element
	{
		foreach ($this->finDocLines as $line) {
			/**
			 * @var $line FinDocumentLine
			 */
			$this->finDoc->addChild($line->getElement());
		}

		return $this->finDoc;
	}
}