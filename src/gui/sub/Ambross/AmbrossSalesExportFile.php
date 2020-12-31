<?php


namespace Eds\Gui\Sub\Ambross;


use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\XLSX\Sheet;
use Eds\File;

class AmbrossSalesExportFile extends File
{

	/**
	 * @var array
	 */
	private $finnDocData = [];

	/**
	 * @var array
	 */
	private $findDocLineData = [];

	/**
	 * @return File
	 */
	public function readFile(): File
	{
		if (!$this->file) {
			return $this;
		}


		$reader = ReaderEntityFactory::createReaderFromFile($this->file);
		$reader->setShouldPreserveEmptyRows(true)
			->open($this->file);

		foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
			/**
			 * @var $sheet Sheet
			 */
			$this->sheets[] = $sheet->getName();
			if ($sheet->isActive()) {
				$this->activeSheet = $sheet->getName();
			}
		}

		// override active sheet
		if (($_POST['activeSheet'] ?? null) && in_array($_POST['activeSheet'], $this->sheets)) {
			$this->activeSheet = $_POST['activeSheet'];
		}

		$activeSheetRecord = null;

		foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
			if ($sheet->getName() == $this->activeSheet) {
				$activeSheetRecord = $sheet;
				break;
			}
		}

		$needCollectData = false;

		$data = [];

		foreach ($activeSheetRecord->getRowIterator() as $rowIndex => $row) {

			$cells = $row->getCells();

			if (!$needCollectData && (!isset($cells[2]) || !$cells[2]->getValue())) {
				continue;
			}

			if (!empty($cells[2]) && $cells[2]->getValue() == 'Numurs') {
				$needCollectData = true;
			}

			if ($needCollectData && (empty($cells[2]) || !$cells[2]->getValue())) {
				$needCollectData = false;
			}

			if ($needCollectData) {
				$data[] = $cells;
			}
		};

		$cleanedData = $this->getCleanData($data);

		$this->data[] = $cleanedData;

		return $this;
	}


	/**
	 * @return array
	 */
	public function getCollectedData(): array
	{
		return $this->data ?? [];
	}

}