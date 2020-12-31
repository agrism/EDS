<?php

namespace Eds\Gui\Sub\EgoMedia;

use Eds\Eds\EdsIinKopsavilkums;
use Eds\Gui\Sub\Report;

class EgoIinKopsavilkums extends Report
{
	public function __construct()
	{
		$this->fileName = 'eds_IIN_kopsavilkums_'.date('Y-m-d').'.xml';
		$this->filePath = ROOT.'/out/'.$this->fileName;
	}

	public function render(): string
	{
		$file = EgoMediaFile::factory();

		$file->renderOpenFileAndMaybeStop($this->fileName, $this->filePath);

		$file->readForm()->readFile()->renderForm();

		$personalIncomeTax = $file->getPrivatePersonalIncome();

		$xmlContent = EdsIinKopsavilkums::factory();

		foreach ($personalIncomeTax as $incomeItem) {
			$xmlContent->addLine(
				1,
				$incomeItem['Vārds, Uzvārds'],
				'1003',
				'2020-01-01',
				'2020-11-30',
				11,
				$incomeItem['Bruto'],
				0,
				0,
				0,
				0,
				0,
				0,
				0,
				$incomeItem['Neapliek, EUR'],
				$incomeItem['IIN']
			);
		}

		$xmlContent = $xmlContent->render(false);

		file_put_contents($this->filePath, $xmlContent);

		return $file->getForm();
	}
}