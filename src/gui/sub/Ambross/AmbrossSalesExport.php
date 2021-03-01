<?php

namespace Eds\Gui\Sub\Ambross;

use Eds\Eds\JumisDocumentExport;
use Eds\Eds\JumisModels\FinDocument;
use Eds\Eds\JumisModels\FinDocumentLine;
use Eds\Gui\Sub\Report;

class AmbrossSalesExport extends Report
{
	public function __construct()
	{
		$this->fileName = 'ambross_dales_export_'.date('Y-m-d').'.xml';
		$this->filePath = ROOT.'/out/'.$this->fileName;
	}


	public function render(): string
	{
		$file = AmbrossSalesExportFile::factory();

		$file->renderOpenFileAndMaybeStop($this->fileName, $this->filePath);

		$file->readForm()->readFile()->renderForm();

		$xmlContent = JumisDocumentExport::factory();

		foreach ($file->getCollectedData() as $index => $documents) {

			foreach ($documents as $document) {

				$financialDocument = FinDocument::factory();
				$financialDocument->create(
					$this->getDocumentNumberFromString($document['Numurs_2']),
					$this->getDocumentSerialFromString($document['Numurs_2']),
					$document['Datums_1'],
					'D',
					'EUR',
					$document['Kopā ar PVN_8'],
					'',
					'',
					$this->getDocumentNameFromString($document['Pircējs_3']),
					substr($document['Kods_4'], 0, 2),
					'',
					$document['Kods_4'],
					'0',
					'',
					''
				);


				// todo check problems
				$withOutVAT = floatval($document['Kopā bez PVN_5']) * 100 + floatval($document['Izm.1_6']) * 100;
				$vat = floatval($document['PVN_7']) * 100;
				$withVat = floatval($document['Kopā ar PVN_8']) * 100;
				$isCash = boolval($document['Kopā ar PVN_10'] ?? false);
				$vatRate = intval(round($vat / $withOutVAT * 100, 0));
				$sumDifference = $withOutVAT + $vat - $withVat;

				if (!in_array($vatRate, [0, 21])) {

					if ($withOutVAT < 100 && in_array($vatRate, [19, 20, 22, 23])) {
						$vatRate = 21;
					} else {
						$this->dump(
							'VAT rate incorrect',
							$withOutVAT,
							$vat,
							$withVat,
							$sumDifference,
							$vatRate,
							$this->getDocumentNumberFromString($document['Numurs_2']),
						);
					}
				}


				$sumDifference = intval($withOutVAT + $vat - $withVat);

				$needSecondCheck = false;

				if ($sumDifference != 0) {

					$needSecondCheck = true;

					if ($sumDifference > 2 || $sumDifference < -2) {
						$this->dump(
							'Sum difference too high',
							$withOutVAT,
							$vat,
							$withVat,
							$sumDifference,
							$vatRate,
							$this->getDocumentNumberFromString($document['Numurs_2']),
						);
						die;
					}

					$withOutVAT = $withOutVAT - $sumDifference;
				}

				if ($needSecondCheck) {
					if (intval($withOutVAT + $vat) === intval($withVat)) {
						$this->dump(
							'Sum difference too high - FIXED',
							$withOutVAT,
							$vat,
							$withVat,
							$sumDifference,
							$vatRate,
							$this->getDocumentNumberFromString($document['Numurs_2']),
						);
					} else {
						$this->dump(
							'Sum difference too high - fix failed',
							$withOutVAT,
							$vat,
							$withVat,
							$sumDifference,
							$vatRate,
							$this->getDocumentNumberFromString($document['Numurs_2']),
						);
						die;
					}
				}


				$financialDocument->addFinDocLine(
					FinDocumentLine::factory()->create(
						'1',
						'EUR',
						$withOutVAT / 100,
						$isCash ? '261001' : '2310',
						$vatRate == 21 ? '6110' : '6111',
						$vatRate
					)
				);

				$financialDocument->addFinDocLine(
					FinDocumentLine::factory()->create(
						'1',
						'EUR',
						$vat / 100,
						$isCash ? '261001' : '2310',
						'5721',
						$vatRate
					)
				);

				$xmlContent->addFinancialDocument($financialDocument);
			}
		}

		/**
		 * $xmlContent JumisDocumentExport
		 */
		$xmlContent = $xmlContent->render(false);

		file_put_contents($this->filePath, $xmlContent);

		return $file->getForm();
	}

	private function getDocumentSerialFromString(string $data)
	{
		$exploded = explode(' ', $data);
		if (count($exploded) < 1) {
			return '';
		}

		return $exploded[0];
	}

	private function getDocumentNumberFromString(string $data)
	{
		$exploded = explode(' ', $data);
		if (count($exploded) < 1) {
			return $exploded[0] ?? '';
		}

		return $exploded[1] ?? '';
	}

	private function getDocumentNameFromString(string $data)
	{
		return str_replace('&', '_', $data);
	}

	private function dump($message, $withOutVAT, $vat, $withVat, $sumDifference, $vatRate, $docNo)
	{
		dump([
			'MESSAGE' => $message,
			'$withOutVAT' => $withOutVAT,
			'$vat' => $vat,
			'$withVat' => $withVat,
			'$sumDifference' => $sumDifference,
			'vatRate' => $vatRate,
			'docNo' => $docNo,
		]);
	}

}