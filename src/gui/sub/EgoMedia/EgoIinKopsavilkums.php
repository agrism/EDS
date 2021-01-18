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
				$incomeItem['Vārds, Uzvārds_2'],
				'1003',
				'2020-12-01',
				'2020-12-31',
				11,
				$incomeItem['Bruto_6'],
				0,
				0,
				0,
				0,
				0,
				0,
				0,
				$incomeItem['Neapliek, EUR_10'],
				$incomeItem['IIN_13']
			);
		}

		$xmlContent = $xmlContent->render(false);

		file_put_contents($this->filePath, $xmlContent);

		return $file->getForm();
	}
}