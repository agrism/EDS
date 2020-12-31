<?php

namespace Eds\Gui\Sub\EgoMedia;


use Eds\Eds\EdsVsaoi;
use Eds\Gui\Sub\Report;

class EgoSoc extends Report
{
	public function __construct()
	{
		$this->fileName = 'eds_VSAOI_'.date('Y-m-d').'.xml';
		$this->filePath = ROOT.'/out/'.$this->fileName;
	}

	public function render(): string
	{
		$file = new EgoMediaFile;

		$file->renderOpenFileAndMaybeStop($this->fileName, $this->filePath);

		$file->readForm()->readFile()->renderForm();

		$salaries = $file->getSalaries();
		$personalIncomeTax = $file->getPrivatePersonalIncome();

		$xmlContent = EdsVsaoi::factory()
			->setDefaultTabs()
			->setTab(1, (function () use ($salaries) {
				$tabRows = [];
				foreach ($salaries as $incomeItem) {
					$tabRows[] = EdsVsaoi::factory()->getRow(
						'12121212656',
						$incomeItem['Vārds, Uzvārds'] ?? 'n/a',
						'DN',
						strval($incomeItem['Bruto alga']),
						strval($incomeItem['SOC DN'] + $incomeItem['SOC DD']),
						$incomeItem['IIN'] ?? '0',
						'0.36',
						'155',
					);
				}
				return $tabRows;
			})())
			->setTab(15, (function () use ($personalIncomeTax) {
				$tabRows = [];
				foreach ($personalIncomeTax as $incomeItem) {
					$tabRows[] = EdsVsaoi::factory()->getRow(
						'11111112656',
						$incomeItem['Vārds, Uzvārds'] ?? 'n/a',
						'AS',
						$incomeItem['Bruto'] ?? '0',
						$incomeItem['VSAOI'] ?? '0',
						'0',
						'0',
						'0',
					);
				}
				return $tabRows;
			})())
			->render(false);

		file_put_contents($this->filePath, $xmlContent);
		return $file->getForm();
	}
}