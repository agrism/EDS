<?php


namespace Eds;


use Eds\Xml\Attribute;
use Eds\Xml\Element;
use Ramsey\Uuid\Uuid;

class EdsVSOAI
{

	/**
	 * @var Element[]
	 */
	private $tabs = [];

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
				(new Element('DeclarationFile'))->addChild(
					(new Element('Declaration'))
						->addAttribute(new Attribute('Id', 'DEC'))
						->addChild((new Element('DokDDZv3'))
							->addAttribute(new Attribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema'))
							->addAttribute(new Attribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance'))
							->addChild($this->getHead())
							->addChild(
								(function (): Element {
									$el = (new Element());

									foreach ($this->getTabs() as $tab) {
										$el->addChild($tab);
									}

									return $el;
								})()
							)
						)
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

	public function getHead(): Element
	{
		return (new Element())->addChild((new Element('Precizejums'))->addContent('true'))
			->addChild((new Element('PrecizejamaisDokuments'))
				->addAttribute(new Attribute('xsi:nil', 'true')))
			->addChild((new Element('Id'))->addContent(RAND(10000000,99999999)))
			->addChild((new Element('UID'))->addContent(Uuid::uuid4()))
			->addChild((new Element('NmrKods'))->addContent(env('NMK')))
			->addChild((new Element('ParskGads'))->addContent('2020'))
			->addChild((new Element('ParskMen'))->addContent('09'))
			->addChild((new Element('Epasts'))->addContent(env('EMAIL')))
			->addChild((new Element('Talrunis'))->addContent(env('PHONE')))
			->addChild((new Element('Sagatavotajs'))->addContent(env('REPORT_PREPARED_BY')))
			->addChild((new Element('IzmaksasDatums'))->addContent('15'));
	}

	/**
	 * @param  int  $tabNumber
	 * @param  array  $tabRows
	 * @return $this
	 */
	public function setTab($tabNumber = 1, array $tabRows): self
	{
		$el = (new Element('Tab'.$tabNumber));

		foreach ($tabRows as $tabRow) {

			$el->addChild($tabRow);
		}

		$this->tabs[$tabNumber] = $el;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function setDefaultTabs(): self
	{
		foreach (range(1, 15) as $item) {
			if (!empty($this->tabs[$item])) {
				continue;
			}
			$this->tabs[$item] = new Element('Tab'.$item);
		}

		return $this;
	}

	public function getTabs(): array
	{
		return $this->tabs;
	}

	/**
	 * @param $persKods
	 * @param $nameSurname
	 * @param  string  $status
	 * @param  int  $ienakumi
	 * @param  int  $iemaksas
	 * @param  int  $ieturetaisNodoklis
	 * @param  float  $riskaNodeva
	 * @param  int  $stundas
	 * @return Element
	 */
	public function getRow(
		$persKods,
		$nameSurname,
		$status = 'DN',
		$ienakumi = 0,
		$iemaksas = 0,
		$ieturetaisNodoklis = 0,
		$riskaNodeva = 0.36,
		$stundas = 150
	): Element {
		return (new Element('R'))
			->addChild((new Element('PersonasKods'))->addContent($persKods))
			->addChild((new Element('VardsUzvards'))->addContent($nameSurname))
			->addChild((new Element('Statuss'))->addContent($status))
			->addChild((new Element('Ienakumi'))->addContent($ienakumi))
			->addChild((new Element('Iemaksas'))->addContent($iemaksas))
			->addChild((new Element('PrecizetieIenakumi'))->addAttribute(new Attribute('xsi:nil', 'true')))
			->addChild((new Element('PrecizetasIemaksas'))->addContent('0.00'))
			->addChild((new Element('IeturetaisNodoklis'))->addContent($ieturetaisNodoklis))
			->addChild((new Element('DarbaVeids'))->addContent('1'))
			->addChild((new Element('RiskaNodevasPazime'))->addContent($riskaNodeva ? 'true' : 'false'))
			->addChild((new Element('RiskaNodeva'))->addContent(strval($riskaNodeva)))
			->addChild((new Element('IemaksuDatums'))->addAttribute(new Attribute('xsi:nil', 'true')))
			->addChild((new Element('Stundas'))->addContent(strval($stundas)))
			->addChild((new Element('PensijuIemaksas'))->addAttribute(new Attribute('xsi:nil', 'true')))
			->addChild((new Element('SolidaritatesNodoklis'))->addAttribute(new Attribute('xsi:nil', 'true')))
			->addChild((new Element('PrecizetasPensijuIemaksas'))->addAttribute(new Attribute('xsi:nil', 'true')))
			->addChild((new Element('PrecizetaisSolidaritatesNodoklis'))->addAttribute(new Attribute('xsi:nil',
				'true')));
	}

}