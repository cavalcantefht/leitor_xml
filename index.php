<?php


date_default_timezone_set('America/Sao_Paulo');
// date_default_timezone_set('UTC');

require_once("lib/docs.class.php");
require_once('lib/FileCsv.php');

$docs = new Docs();

$array_content = $docs->index();

$generate = new FileCSV();

$array_header = [
    "estado",
    "ano",
    "mes",
    "cnpj",
    "modelo",
    "serie",
    "numero",
    "forma_emi",
    "cod_num",
    "dv",
    "chave",
    "doc_xml",
    "mod_xml",
    "dhEmi",
    "cStat"
];

$generate->setHeader($array_header);
$generate->setContent($array_content);
$generate->generateAndDownloadFileCSV();
