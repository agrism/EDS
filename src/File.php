<?php

namespace Eds;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\XLSX\Sheet;

class File
{
	private $file;
	private $inputName = 'file__';
	private $sheets = [];
	private $activeSheet = '';

	private $salaries = [];
	private $privateCorporateIncome = [];
	private $privatePersonalIncome = [];

	public function renderForm(): self
	{

//		dump($_POST[$this->inputName] ?? 2);

		$form = [];
		$form[] = '<form method="post">';
		$form[] = $this->getSheetOption();


		$fileData = !empty($_POST[$this->inputName]) ? $_POST[$this->inputName] : null;
		$form[] = '<input type="text" name=fileData value="'.($fileData ?? ($_POST['fileData'] ?? '')).'">';
		$form[] = '<input type="file" name="'.$this->inputName.'">';
		$form[] = '<input type="submit" name="submit">';

		$form[] = '</form>';

		$form[] = '<a href="/?tab=tab" target="_blank" >Get File</a>';

		echo implode('', $form);

		return $this;
	}

	public function getSheetOption(): string
	{
		$render = [];
		$render[] = '<select name="activeSheet">';
		foreach ($this->sheets as $sheet) {
			$render[] = "<option value=$sheet";
			$render[] = $sheet === $this->activeSheet ? ' selected ' : '';
			$render[] = '>';
			$render[] = $sheet;
			$render[] = '</option>';
		}
		$render[] = '</select>';

		return implode('', $render);
	}

	public function readForm(): self
	{
		$f = !empty($_POST[$this->inputName]) ? $_POST[$this->inputName] : null;

		$this->file = $f ?? ($_POST['fileData'] ?? null);
		$this->activeSheet = $_POST['activeSheet'] ?? null;

		return $this;
	}

	public function readFile(): self
	{

		if ($this->file) {
			$reader = ReaderEntityFactory::createReaderFromFile($this->file);
			$reader->open($this->file);


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

//			dump('active sheet record: '.$activeSheetRecord->getName());

			$needCollectData = false;
			$needCollectData2 = false;

			$data = [];
			$data2 = [];

			foreach ($activeSheetRecord->getRowIterator() as $rowIndex => $row) {

				// -0

				$cells = $row->getCells();

				if (!$cells[2]->getValue()) {
					continue;
				}


				if (str_starts_with($cells[2]->getValue(), 'ALGAS ')) {
					$needCollectData = true;
				}

				if (str_starts_with($cells[2]->getValue(), 'AUTORATLĪDZĪBAS, Īre, Noma ')) {
					$needCollectData = false;
				}

				if ($needCollectData) {
					$data[] = $cells;
				}


				/////-2

				if (str_starts_with($cells[2]->getValue(), 'AUTORATLĪDZĪBAS, Īre, Noma ')) {
					$needCollectData2 = true;
				}

				if (str_starts_with($cells[2]->getValue(), 'Saņēmējs')) {
					$needCollectData2 = false;
				}

				if ($needCollectData2) {
					$data2[] = $cells;
				}


			}

			$salaries = $this->getCleanData($data);
			$private = $this->getCleanData($data2);


			$privatePersonalIncome = [];
			$privateCorporateIncome = [];

			foreach ($private as $privateItem){
				if( $this->filterPrivatPersonalIncome($privateItem) ){
					$privatePersonalIncome[] = $privateItem;
				} else {
					$privateCorporateIncome[] = $privateItem;
				}
			}

			$this->salaries = $salaries;
			$this->privatePersonalIncome = $privatePersonalIncome;
			$this->privateCorporateIncome =$privateCorporateIncome;

		}
		return $this;
	}

	/**
	 * @param  array  $rows
	 */
	private function getCleanData($rows = [])
	{
		$salaries = [];
		$indexes = [];

		foreach ($rows as $index => $cells) {
			if (!$index) {
				continue;
			}
			if ($index === 1) {
				foreach ($cells as $cell) {
					$indexes[] = $cell->getValue();
				}
				continue;
			}

			$temp = [];

			foreach ($cells as $cellIndex => $cell) {
				if (empty($indexes[$cellIndex])) {
					continue;
				}

				$temp[$indexes[$cellIndex]] = $cell->getValue();
			}
			$salaries[] = $temp;
		}

		return $salaries;
	}

	private function filterPrivatPersonalIncome($private = [])
	{
		$isPrivate = false;
		foreach ($private as $key => $value) {
			if ($key == 'UIN') {

				if (intval($value) === 0) {
					$isPrivate = true;
				}

				break;
			}
		}

		return $isPrivate;
	}

	/**
	 * @return array
	 */
	public function getSalaries(): array
	{
		return $this->salaries;
	}

	/**
	 * @return array
	 */
	public function getPrivateCorporateIncome(): array
	{
		return $this->privateCorporateIncome;
	}

	/**
	 * @return array
	 */
	public function getPrivatePersonalIncome(): array
	{
		return $this->privatePersonalIncome;
	}


}