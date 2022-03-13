<?php

require_once("./lib/UnConvertNFePHP.class.php");

class Docs extends UnConvertNFePHP
{

    public $dir;

    public $UnConvertNFe;

    public $dados = [];

    public function __construct($dir = null)
    {

        if ($dir == null)
            $this->dir = './tmp/';
        else
            $this->dir = $dir;
    }
    public function index()
    {

        try {
            $diretorio = dir($this->dir);

            $docs = [];

            while ($arq = $diretorio->read()) {
                // $extension = explode('.', $arq);
                $extension = pathinfo($arq, PATHINFO_EXTENSION);
                if ($extension == 'xml') {
                    $xml = $this->nfexml2txt($this->dir . $arq);
                    $docs[] = [
                        "estado" => substr($arq, 0, 2),
                        "ano" => substr($arq, 2, 2),
                        "mes" => substr($arq, 4, 2),
                        "cnpj" => substr($arq, 6, 14),
                        "modelo" => substr($arq, 20, 2),
                        "serie" => substr($arq, 22, 3),
                        "numero" => substr($arq, 25, 9),
                        "forma_emi" => substr($arq, 34, 1),
                        "cod_num" => substr($arq, 35, 8),
                        "dv" => substr($arq, 43, 1),
                        "chave" => $arq,
                        "doc_xml" => $xml[0]['doc'],
                        "mod_xml" => $xml[0]['mod'],
                        "dhEmi" => $xml[0]['dhEmi'],
                        "cStat" => $xml[0]['cStat'],
                    ];
                }
            }


            $this->dados = $docs;

            return $docs;
        } catch (Exception $e) {
            print_r($e);
            print("ERRO AO LER DIRETORIO");
            die();
        }
    }

    public function render()
    {

        $header = "";

        $table = "<table border='1'>";
        $header .= "<tr>";
        $header .= "<td>Estado</td>";
        $header .= "<td>Mes</td>";
        $header .= "<td>Ano</td>";
        $header .= "<td>CNPJ</td>";
        $header .= "<td>Modelo</td>";
        $header .= "<td>Modelo XML</td>";
        $header .= "<td>Serie</td>";
        $header .= "<td>Numero</td>";
        $header .= "<td>Numero XML</td>";
        $header .= "<td>Cod. Num</td>";
        $header .= "<td>cStat</td>";
        $header .= "<td>DH EMIS</td>";
        $header .= "</tr>";

        $table .= $header;



        if (count($this->dados) > 0) {
            foreach ($this->dados as $dados) {
                $table .= "<tr>";
                $table .= "<td>" . $dados['estado']  . "</td>";
                $table .= "<td>" . $dados['mes']  . "</td>";
                $table .= "<td>" . $dados['ano']  . "</td>";
                $table .= "<td>" . $dados['cnpj']  . "</td>";
                $table .= "<td>" . $dados['modelo']  . "</td>";
                $table .= "<td>" . $dados['mod_xml']  . "</td>";
                $table .= "<td>" . $dados['serie']  . "</td>";
                $table .= "<td>" . $dados['numero']  . "</td>";
                $table .= "<td>" . $dados['doc_xml']  . "</td>";
                $table .= "<td>" . $dados['cod_num']  . "</td>";
                $table .= "<td>" . $dados['cStat']  . "</td>";
                if (!empty($dados['dhEmi'])) {
                    $table .= "<td>" . date('d/m/Y H:i:s', strtotime($dados['dhEmi']))  . "</td>";
                } else {
                    $table .= "<td> - </td>";
                }
                $table .= "</tr>";
            }
        } else {
        }


        $table .= "</table>";

        echo $table;
        return;
    }
}
