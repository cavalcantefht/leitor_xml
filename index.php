<?php


date_default_timezone_set('America/Sao_Paulo');
// date_default_timezone_set('UTC');

require_once("lib/docs.class.php");

$docs = new Docs();

$docs->index();

echo "<center>";
$docs->render();
echo "</center>";
