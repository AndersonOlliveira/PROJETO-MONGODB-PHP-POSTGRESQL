<?php

// use BuscaValorLotePorConsulta;
ini_set('memory_limit', '1256M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class CapturaAplication
{

    protected $conection;
    protected $proZoomy;



    public function __construct()
    {
        require_once 'ConexaoBd.php';
        $this->conection = new conexaoBd();
        require_once __DIR__ . '/../../../class/Core/V2.php';
        require_once __DIR__ . '/../../../proZoomy/BDproZoomy.php';
        $this->proZoomy = new BDproZoomy();
    }


    public function get_aplication($contrato)
    {
        $conexaoBd = $this->conection->getInstance();
        $results_pages  = [];

        $sql = "";
        $sql = "SELECT srvdsc, srvlnk, srvobs, srvid, upper(srvtst), 
			   upper(srvtst), upper(srvprm), upper(srvman), 
			   upper(srvprmman), srvapp
			   FROM ctrsrv, srv WHERE (ctrsrvsrv = srvid)
			    AND upper(srvapp) = 'S' AND 
			    (ctrsrvctr= ?) 
			    AND ((upper(srvweb)='S'
			    or upper(srvweb)='Y'))
			    AND (trim(upper(srvlnk))
			    like '%.CHP')
                --AND trim(upper(srvlnk)) NOT IN ('/MNTRGR/MNTRGR.CHP','/RNGLST/RNGLST.CHP')
			   AND ((upper(ctrsrvatv)='S' or upper(ctrsrvatv)='Y')) ORDER BY srvdsc;";

        $dadosTransacao = [$contrato];
        $results = $conexaoBd->BD_COM->prepare($sql);
        $results->execute($dadosTransacao);
        // return $results->fetchAll(PDO::FETCH_ASSOC);

        if ($results->rowCount() == 0) {

            return false;
        }

        #Segundo SQL aqui se necessário
        $sql_two = "SELECT rdecnsdsc, rdecnsid, rdecnsweb, rdecnsprocura FROM 
        rdecns, rdelja, rdeljacns WHERE 
        (rdeljactr = ?) and (rdeljaid = rdeljacnsrdelja) and (rdecnsid = rdeljacnsrdecns)
        and (rdecnsweb = 'S') AND rdecnsprocura = 'S' ORDER BY rdecnsdsc;";

        $resultstwo = $conexaoBd->BD_COM->prepare($sql_two);
        // $resultstwo->execute();
        $resultstwo->execute($dadosTransacao);

        while ($rowsCns = $resultstwo->fetchAll(PDO::FETCH_ASSOC)) {

            if (!empty($rowsCns)) {
                $results_pages[] = [
                    'srvdsc' => 'proCura',
                    'srvlnk' => '/procura/proCura.php',
                    'srvlnk_' => '/procura/proCura.php',
                    'srvobs' => 'procura',
                    'srvlnk_logos' => '/procura/proCura.php',
                    'proCura' => $rowsCns,
                    'infoProCura' => 'proCura',
                    'to_score' => false,
                    'color' => 'gren'
                ];
            }
       
        }

        #ACESSAR PAGINA DE PROZOOM
        // $result = $this->proZoomy->buscar_consultas($contrato);
        $prozommy = $this->proZoomy->buscar_mapas($contrato);

        if (!empty($prozommy)) {
            $results_pages[] = [
                'srvdsc' => 'BDproZoomy',
                'srvlnk' => '/proZoomy/proZoomy.php',
                'srvlnk_' => '/proZoomy/BDproZoomy.php',
                'srvobs' => 'proZoomy',
                'srvlnk_logos' => '/proZoomy/BDproZoomy.php',
                'proZommy' => $prozommy,
                'infoProZoomy' => 'BDproZoomy',
                'to_score' => false,
                'color' => 'gren'
            ];
        }

        $ja_adicionou_regras = false;
        while ($rows = $results->fetch(PDO::FETCH_ASSOC)) {
            $rows['modal_actions'] = [];
            $srvlnk =  trim($rows['srvlnk']);
            $srvapp =  trim($rows['srvapp']);
            $rows['srvlnk'] = $srvlnk;
            $rows['srvdsc'] = trim($rows['srvdsc']);
            $rows['srvobs'] = trim($rows['srvobs']);

            $rows['to_score'] = false;
            $rows['proZommy'] = false;
            $rows['infoProZoomy'] = '';


            if ($srvapp == "S") {
                if ($srvlnk === "/mntrgr/mntrgr.chp" || $srvlnk === "/rnglst/rnglst.chp") {

                    // 2. Só entra aqui se ainda não tiver adicionado o grupo
                    if (!$ja_adicionou_regras) {
                        $rows['modal_actions'][] = [
                            'title' => 'Manutenção no cadastro de Regras de negócio',
                            'srvlnk' => '/mntrgr/mntrgr.chp',
                            'to_score' => true,
                            'type' => 'manutencao_regra_negocio'
                        ];
                        $rows['modal_actions'][] = [
                            'title' => 'Visualização no cadastro de Regras de negócio',
                            'srvlnk' => '/rnglst/rnglst.chp',
                            'to_score' => false,
                            'type' => 'visualiza_regra_negocio'
                        ];

                        $results_pages[] = [
                            'srvlnk' => 'REGRAS_DE_NEGOCIO',
                            'dados_regras' => $rows['modal_actions'],
                            'srvlnk_logos' => '/mntrgr/toScore.chp',
                            'to_score' => true,
                            'srvobs' => 'toScore',
                        ];

                        $ja_adicionou_regras = true; // 3. Marcamos que já foi adicionado
                    }

                    continue; // Pula para a próxima linha do banco
                }
            }

            switch ($srvlnk) {

                case "/familytree/familytree.chp":
                    $rows['color'] = 'gren';
                    $rows['srvlnk_logos'] = '/familytree/familytree.chp';
                    $rows['srvobs'] = 'proTree Family';
                    break;
                case "/arvsoc/arvsoc.chp":
                    $rows['color'] = 'white';
                    $rows['srvlnk_logos'] = '/arvsoc/familytreeCorp.chp';
                    $rows['srvobs'] = 'proTree Corp';
                    break;
                case "/matchface/matchface.chp":
                    $rows['color'] = 'yellow';
                    $rows['srvlnk_logos'] = '/matchface/matchface.chp';
                    $rows['srvobs'] = 'proMatch Face';
                    break;
                case "/matchnamecpf/matchnamecpf.chp":
                    $rows['color'] = 'gray';
                    $rows['srvlnk_logos'] = '/matchnamecpf/proMatch.chp';
                    $rows['srvobs'] = 'proMatch Name';
                    break;
                case "/matchnamecnpj/matchnamecnpj.chp":
                    $rows['color'] = 'red';
                    $rows['srvlnk_logos'] = '/matchnamecnpj/proMatch.chp';
                    $rows['srvobs'] = 'proMatch Corp';
                    break;
                case "/progestor/progestor.chp":
                    $rows['color'] = '#00005f';
                    $rows['srvlnk_logos'] = '/progestor/progestor.chp';
                    $rows['srvobs'] = 'proGestor';
                    break;
                case "/prospot/index.chp":
                    $rows['color'] = 'gren';
                    $rows['srvlnk_logos'] = '/prospot/prospot.chp';
                    $rows['srvobs'] = 'proSpot';
                    break;
                case "/prox/prox.chp":
                    $rows['color'] = 'gren';
                    $rows['srvlnk_logos'] = '/prox/prox.chp';
                    $rows['srvobs'] = 'proX';
                    break;
                case "/ferextpj/ferextpj.chp":
                    $rows['color'] = 'gren';
                    $rows['srvlnk_logos'] = '/ferextpj/proXctrat.chp';
                    $rows['srvobs'] = 'proXtract';
                    break;
                case "/mntrgr/mntrgr.chp":
                case "/rnglst/rnglst.chp":
                    $rows['srvlnk_logos'] = '/mntrgr/toScore.chp';
                    $rows['color'] = 'white';
                    $rows['srvobs'] = 'toScore';
                    break;
                default:
                    $rows['color'] = 'falha';
            }
            $results_pages[] = $rows;
        }

        return $results_pages;
    }

    public function get_data_user($contrato)
    {

        $conexaoBd = $this->conection->getInstance();


        $sql = "";
        $sql = "SELECT ctrid ,ctrcod ,ctrcli,ctrapl,cliemail
        FROM ctr, cli WHERE 
        ctrid = ? AND 
        ctrcli = cliid;";

        $dadosTransacao = [$contrato];
        $results = $conexaoBd->BD_COM->prepare($sql);
        $results->execute($dadosTransacao);


        if ($results->rowCount() == 0) {

            return false;
        }

        return $results->fetchAll(PDO::FETCH_ASSOC);
    }
}
