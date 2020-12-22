<?php


namespace Eds;


use Eds\Xml\Attribute;
use Eds\Xml\Element;
use Ramsey\Uuid\Uuid;

class EdsIinKopsavilkums
{

	/**
	 * @var array
	 */
	private $lines = [];

	/**
	 * @var string
	 */
	private $xml;

	/*** @return static */
	public static function factory(): self
	{
		return new self;
	}

	public function addLine(
		$personasKods,
		$vardsUzvards,
		$ienakumaVeids,
		$ienakumaPeriodsNo,
		$ienakumaPeriodsLidz,
		$izmaksasMenesis,
		$ienemumi,
		$neapliekamieIenakumi,
		$neapliekamaisMiniums,
		$atvieglojumiParApgadajamiem,
		$atvieglojumaSumma,
		$iemaksasPPF,
		$apdrosinasanasSummaArUzkrasanu,
		$apdrosinasanasSummaBezUzkrasanas,
		$izdevumi,
		$nodoklis
	): self {
		$this->lines[] = (new Element('R'))
			->addChild((new Element('PersonasKods'))->addContent($personasKods))
			->addChild((new Element('VardsUzvards'))->addContent($vardsUzvards))
			->addChild((new Element('IenakumaVeids'))->addContent($ienakumaVeids))
			->addChild((new Element('IenakumuPeriodsNo'))->addContent($ienakumaPeriodsNo))
			->addChild((new Element('IenakumuPeriodsLidz'))->addContent($ienakumaPeriodsLidz))
			->addChild((new Element('IzmaksasDatums'))->addAttribute(new Attribute('xsi:nil', 'true')))
			->addChild((new Element('IzmaksasMenesis'))->addContent($izmaksasMenesis))
			->addChild((new Element('Ienemumi'))->addContent($ienemumi))
			->addChild((new Element('NeapliekamieIenakumi'))->addAttribute(new Attribute('xsi:nil', 'true')))
			->addChild((new Element('NeapliekamaisMinimums'))->addContent($neapliekamaisMiniums))
			->addChild((new Element('AtvieglojumiParApgadajamiem'))->addContent($atvieglojumiParApgadajamiem))
			->addChild((new Element('AtvieglojumaKods')))
			->addChild((new Element('AtvieglojumaSumma'))->addContent($atvieglojumaSumma))
			->addChild((new Element('VSAObligatasIemaksa'))->addAttribute(new Attribute('xsi:nil', 'true')))
			->addChild((new Element('IemaksasPPF'))->addContent($iemaksasPPF))
			->addChild((new Element('ApdrosinasanasSummaArUzkrasanu'))->addContent($apdrosinasanasSummaArUzkrasanu))
			->addChild((new Element('ApdrosinasanasSummaBezUzkrasanas'))->addContent($apdrosinasanasSummaBezUzkrasanas))
			->addChild((new Element('Izdevumi'))->addContent($izdevumi))
			->addChild((new Element('Nodoklis'))->addContent($nodoklis));

		return $this;
	}

	private function generateWholeDocument(): self
	{
		$this->xml = (new Element(''))
			->addChild(
				(new Element('DeclarationFile'))->addChild(
					(new Element('Declaration'))
						->addAttribute(new Attribute('Id', 'DEC'))
						->addChild((new Element('DokPFPISKv2'))
							->addAttribute(new Attribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema'))
							->addAttribute(new Attribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance'))
							->addChild($this->getHead())
							->addChild(
								(function () {
									$el = new Element('Tab');
									foreach ($this->lines as $line) {
										$el->addChild($line);
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
		return (new Element())->addChild((new Element('Precizejums'))->addContent('false'))
			->addChild((new Element('PrecizejamaisDokuments'))
				->addAttribute(new Attribute('xsi:nil', 'true')))
			->addChild((new Element('Id'))->addContent(RAND(10000000, 99999999)))
			->addChild((new Element('UID'))->addContent(Uuid::uuid4()))
			->addChild((new Element('NmrKods'))->addContent(env('NMK')))
			->addChild((new Element('ParskGads'))->addContent('2020'))
			->addChild((new Element('ParskMen'))->addContent('11'))
			->addChild((new Element('Epasts'))->addContent(env('EMAIL')))
			->addChild((new Element('Talrunis'))->addContent(env('PHONE')))
			->addChild((new Element('Sagatavotajs'))->addContent(env('REPORT_PREPARED_BY')))
			->addChild((new Element('IesniegsanasVeids'))->addContent('1'));
	}

}