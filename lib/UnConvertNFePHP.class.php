<?php

/**
 * Este arquivo e parte do projeto NFePHP - Nota Fiscal eletronica em PHP.
 *
 * Este programa e um software livre: voce pode redistribuir e/ou modifica-lo
 * sob os termos da Licenca Publica Geral GNU como e publicada pela Fundacao
 * para o Software Livre, na versao 3 da licenca, ou qualquer versao posterior.
 * e/ou
 * sob os termos da Licenca Publica Geral Menor GNU (LGPL) como e publicada pela
 * Fundacao para o Software Livre, na versao 3 da licenca, ou qualquer versao posterior.
 *
 * Este programa e distribuído na esperanca que sera util, mas SEM NENHUMA
 * GARANTIA; nem mesmo a garantia explícita definida por qualquer VALOR COMERCIAL
 * ou de ADEQUACAO PARA UM PROPOSITO EM PARTICULAR,
 * veja a Licenca Publica Geral GNU para mais detalhes.
 *
 * Voce deve ter recebido uma copia da Licenca Publica GNU e da
 * Licenca Publica Geral Menor GNU (LGPL) junto com este programa.
 * Caso contrario consulte
 * <http://www.fsfla.org/svnwiki/trad/GPLv3>
 * ou
 * <http://www.fsfla.org/svnwiki/trad/LGPLv3>.
 * 
 * Esta classe atende aos criterios estabelecidos no
 * Manual de Importacao/Exportacao TXT Notas Fiscais eletronicas versao 3.10
 *
 * @package     NFePHP
 * @name        UnConvertNFePHP
 * @version     1.0.3
 * @license     http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
 * @license     http://www.gnu.org/licenses/lgpl.html GNU/LGPL v.3
 * @copyright   2009-2011 &copy; NFePHP
 * @link        http://www.nfephp.org/
 * @author      Roberto L. Machado <linux.rlm at gmail dot com>
 * @author      Daniel Batista Lemes <dlemes at gmail dot com>
 *
 *
 *        CONTRIBUIDORES (em ordem alfabetica):
 *              Alberto  Leal <ees.beto at gmail dot com>
 *              Andre Noel <andrenoel at ubuntu dot com>
 *              Clauber Santos <cload_info at yahoo dot com dot br>
 *              Crercio <crercio at terra dot com dot br>
 *              Diogo Mosela <diego dot caicai at gmail dot com>
 *              Eduardo Gusmao <eduardo dot intrasis at gmail dot com>
 *              Elton Nagai <eltaum at gmail dot com>
 *              Fabio Ananias Silva <binhoouropreto at gmail dot com>
 *              Giovani Paseto <giovaniw2 at gmail dot com>
 *              Giuliano Nascimento <giusoft at hotmail dot com>
 *              Helder Ferreira <helder.mauricicio at gmail dot com>
 *              Joao Eduardo Silva Correa <jscorrea2 at gmail dot com>
 *              Leandro C. Lopez <leandro.castoldi at gmail dot com>
 *              Leandro G. Santana <leandrosantana1 at gmail dot com>
 *              Marcos Diez <marcos at unitron dot com dot br>
 *              Renato Ricci <renatoricci at singlesoftware dot com dot br>
 *              Roberto Spadim <rspadim at gmail dot com>
 *              Rodrigo Rysdyk <rodrigo_rysdyk at hotmail dot com>
 *
 */

class UnConvertNFePHP
{

        /**
         * errMsg
         * Mensagens de erro do API
         * @var string
         */
        public $errMsg = '';

        /**
         * errStatus
         * Status de erro
         * @var boolean
         */
        public $errStatus = false;

        /**
         * vlayout
         * versao do layout do xml
         * @var string
         */
        public $vlayout = '';

        /**
         * nfexml2txt
         * Metodo de conversao das NFe de xml para txt, conforme
         * especificacoes do Manual de Importacao/Exportacao TXT
         * Notas Fiscais eletronicas Versao 2.0.0
         * Referente ao modelo de NFe contido na versao 4.01
         * do manual de integracao da NFe
         *
         * @name nfexml2txt
         * @param mixed string ou array $arq Paths dos arquivos xmls
         * @return mixed boolean ou string
         */

        public function nfexml2txt($arq)
        {
                //verificar se a string passada como parametro e string ou array
                if (is_array($arq)) {
                        $matriz = $arq;
                } else {
                        $matriz[] = $arq;
                }
                //para cada nf passada na matriz

                $contNotas = 0;
                $txt = '';
                foreach ($matriz as $file) {
                        //carregar o conteudo do arquivo xml em uma string
                        if (is_file($file)) {
                                $xml = file_get_contents($file);
                        } else {
                                $xml = $file;
                        }

                        //instanciar o ojeto DOM
                        $dom = new DOMDocument('1.0', 'utf-8');
                        //carregar o xml no objeto DOM
                        $xml = preg_replace('/&(?!#?[a-z0-9]+;)/', 'e', $xml);
                        $xml = str_replace("\n", "", $xml);
                        $xml = str_replace("|", "*", $xml);
                        if (!$dom->loadXML($xml)) {
                                $this->errMsg = 'O arquivo indicado como NFe nao e um XML!';
                                $this->errStatus = true;
                                return false;
                        }
                        //e um xml => verificar se e uma NFe
                        $infNFe = $dom->getElementsByTagName("infNFe")->item(0);


                        if (!isset($infNFe)) {
                                $this->errMsg = 'O arquivo indicado como NFe nao e uma NFe!';
                                $this->errStatus = true;
                                return false;
                        }


                        // e uma NFe => transformar em txt
                        $contNotas++;
                        //tansforma no xml => txt
                        $txt = $this->cxtt($dom);
                } //fim foreach
                return $txt;
        } //fim nfexml2txt

        /**
         *cxtt
         * 
         * @param type $dom 
         */
        private function cxtt($dom)
        {

                $txt = [];

                $ide = $dom->getElementsByTagName("ide")->item(0);
                $mod = $ide->getElementsByTagName("mod")->item(0);
                $doc = $ide->getElementsByTagName("nNF")->item(0);
                $dhEmi = $ide->getElementsByTagName("dhEmi")->item(0);

                $protNFe  = $dom->getElementsByTagName("protNFe")->item(0);
                $cStat = null;
                if (isset($protNFe))
                        $cStat = $protNFe->getElementsByTagName("cStat")->item(0);



                $txt[] = [
                        "doc" => isset($doc->nodeValue) ? $doc->nodeValue : null,
                        "mod" => isset($mod->nodeValue) ? $mod->nodeValue : null,
                        "dhEmi" => isset($dhEmi->nodeValue) ? $dhEmi->nodeValue : null,
                        "cStat" => isset($protNFe) && isset($cStat->nodeValue) ? $cStat->nodeValue : '000',
                ];

                return $txt;
        }
}
//fim da classe
