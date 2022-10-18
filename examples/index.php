<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */
require_once "config.php";

use Empatisoft\YokAtlas;

$atlas = new YokAtlas();

$atlas->setProgram(105511051);
$atlas->setYear(2021);
$program = $atlas->getProgram();
json($program);

/*$atlas->setUniversity(1055);
$programs = $atlas->getPrograms();
json($programs);*/
