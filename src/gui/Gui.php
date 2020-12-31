<?php

namespace Eds\Gui;


use Eds\Gui\Sub\Ambross\AmbrossSalesExport;
use Eds\Gui\Sub\EgoMedia\EgoIinKopsavilkums;
use Eds\Gui\Sub\EgoMedia\EgoSoc;

class Gui
{
	private $activeReportName = '';

	private $menuReports = [
		'ego-iin-kopsavilkums' => EgoIinKopsavilkums::class,
		'ego-vsaoi' => EgoSoc::class,
		'ambross-export' => AmbrossSalesExport::class,
	];

	public function __construct()
	{
		$this->activeReportName = $_GET['report'] ?? null;
	}

	public static function factory(): self
	{
		return new self;
	}

	public function getActiveReportName(): string
	{
		return strval($this->activeReportName);
	}

	private function getPage()
	{
		if (!$instance = ($this->menuReports[$this->getActiveReportName()] ?? null)) {
			var_dump(__METHOD__);
			return;
		}

		return $instance::factory()->render();
	}

	public function menu()
	{
		$html = [];

		$html[] = '<ul>';

		foreach (array_keys($this->menuReports) as $reportName) {
			$html[] = '<li';
			if ($this->getActiveReportName() == $reportName) {
				$html[] = ' style="background-color:orange;" ';
			}
			$html[] = '>';
			$html[] = '<a ';
			$html[] = 'href="/?report='.$reportName.'">';
			$html[] = str_replace('-', ' ', ucfirst($reportName));
			$html[] = '</a>';
			$html[] = '</li>';
		}

		$html[] = '</ul>';

		return implode('', $html);
	}

	public function render()
	{
		$html = [];
		$html[] = '<div style="background-color: white;width: 100%;">';
		$html[] = '<div style="background-color: white;min-width: 250px; width: 30%;display: block;float: left;padding-right:10px">';
		$html[] = $this->menu();
		$html[] = '</div>';
		$html[] = '<div style="padding: 10px;">';
		$html[] = $this->getPage();
		$html[] = '</div>';
		$html[] = '</div>';

		echo implode('', $html);
	}
}