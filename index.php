<?php

use Eds\Gui\Gui;

include './vendor/autoload.php';
include './src/functions.php';

define('ROOT', __DIR__);

Gui::factory()->render();


