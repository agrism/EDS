<?php

use Eds\EdsIinKopsavilkums;
use Eds\EdsVSOAI;

include './vendor/autoload.php';
include './src/functions.php';

$filePath = __DIR__.'/out/eds_IIN_kopsavilkums_'.date('Y-m-d').'.xml';
//$filePath = __DIR__.'/out/eds_VSAOI_'.date('Y-m-d').'.xml';

if (($_GET['tab'] ?? null) == 'tab') {
	header('Content-type: text/xml');
	header('Content-Disposition: attachment; filename="listingstatus.xml"');
	header('Pragma: public');
	header('Cache-control: private');
	header('Expires: -1');
	echo file_get_contents($filePath);
	exit;
}

$file = new \Eds\File();
$file->readForm()->readFile()->renderForm();

$salaries = $file->getSalaries();
$personalIncomeTax = $file->getPrivatePersonalIncome();
/*

$xmlContent = EdsVSOAI::factory()
	->setDefaultTabs()
	->setTab(1, (function () use ($salaries) {
		$tabRows = [];
		foreach ($salaries as $incomeItem) {
			$tabRows[] = EdsVSOAI::factory()->getRow(
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
			$tabRows[] = EdsVSOAI::factory()->getRow(
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

$file = __DIR__.'/out/eds_VSAOI_'.date('Y-m-d').'.xml';
file_put_contents($filePath, $xmlContent);
*/

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

file_put_contents($filePath, $xmlContent);



