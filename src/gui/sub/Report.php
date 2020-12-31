<?php

namespace Eds\Gui\Sub;

abstract class Report
{
	protected $fileName;
	protected $filePath;

	public static function factory()
	{
		return new static();
	}

	public abstract function render(): string;
}