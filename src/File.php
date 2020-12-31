<?php

namespace Eds;

use Eds\Gui\Gui;

abstract class File
{
	protected $file;
	protected $inputName = 'file__';
	protected $sheets = [];
	protected $activeSheet = '';

	/**
	 * @var string
	 */
	private $form = '';

	public static function factory(){
		return new static;
	}

	public function renderForm(): self
	{

		$form = [];
		$form[] = '<form method="post">';
		$form[] = $this->getSheetOption();


		$fileData = !empty($_POST[$this->inputName]) ? $_POST[$this->inputName] : null;
		$form[] = '<input type="text" name=fileData value="'.($fileData ?? ($_POST['fileData'] ?? '')).'">';
		$form[] = '<input type="file" name="'.$this->inputName.'">';
		$form[] = '<input type="submit" name="submit">';

		$form[] = '</form>';

		$form[] = '<a href="/?report='.Gui::factory()->getActiveReportName().'&tab=tab" target="_blank" >Get File</a>';

		$this->form = implode('', $form);

		return $this;
	}

	/**
	 * @param  string  $fileName
	 * @param  string  $filePath
	 */
	public function renderOpenFileAndMaybeStop(string $fileName, string $filePath){
		if (($_GET['tab'] ?? null) == 'tab') {
			header('Content-type: text/xml');
			header('Content-Disposition: attachment; filename="'.$fileName.'"');
			header('Pragma: public');
			header('Cache-control: private');
			header('Expires: -1');
			echo file_get_contents($filePath);
			exit;
		}
	}

	public function printForm(): self
	{
		echo $this->form;
		return $this;
	}


	public function getForm(): string
	{
		return strval($this->form);
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

	/**
	 * @return $this
	 */
	public function readForm(): self
	{
		$f = !empty($_POST[$this->inputName]) ? $_POST[$this->inputName] : null;

		$this->file = $f ?? ($_POST['fileData'] ?? null);
		$this->activeSheet = $_POST['activeSheet'] ?? null;

		return $this;
	}

	public abstract function readFile(): self;

	protected function getCleanData($rows = [], $titleRowIndex = 0)
	{
		$salaries = [];
		$indexes = [];

		foreach ($rows as $index => $cells) {
			if ($index < $titleRowIndex) {
				continue;
			}
			if ($index === $titleRowIndex) {
				foreach ($cells as $cellIndex => $cell) {
					$indexes[] = $cell->getValue().'_'.$cellIndex;
				}
				continue;
			}

			$temp = [];

			foreach ($cells as $cellIndex => $cell) {
				if (empty($indexes[$cellIndex])) {
					continue;
				}

				$cellValue = $cell->getValue();

				if($cellValue instanceof \DateTime){
					$cellValue = $cellValue->format('Y-m-d');
				}

				$temp[$indexes[$cellIndex]] = $cellValue;
			}
			$salaries[] = $temp;
		}

		return $salaries;
	}

}