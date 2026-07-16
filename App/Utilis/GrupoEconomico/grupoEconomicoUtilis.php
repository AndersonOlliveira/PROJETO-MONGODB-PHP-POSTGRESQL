<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class grupoEconomicoUtilis extends Controller
{


    protected $modelGrupo;
    protected $functionsGrupo;


    ##[Override]
    public function __construct()
    {
        require_once __DIR__ . '../../../models/grupoEconomico/grupoEconomico.php';
        $this->modelGrupo = new grupoEconomico();

        require_once __DIR__ . '../../validaCamposEconomico.php';
        $this->functionsGrupo = new validaCamposEconomico();
        return parent::__construct();
    }


    public function search($dados)
    {


        extract($dados);


        return $this->modelGrupo->lista_rde_loja($c_cliente_search, $tipo_busca);
    }
    public function search_limite($contrato)
    {
        return $this->modelGrupo->nivel_cliente_grupo($contrato);
    }

    public function process_dados_grupo($dados)
    {

        $retorno_tipo = $this->functionsGrupo->getDados_tabela_grupo($dados['id']);

        if (isset($retorno_tipo['error'])) {
            return $retorno_tipo;
        }


        $dados['value_limite'] = 2;
        $retorno_value = $this->functionsGrupo->validar_campo_limite($dados['value_limite']);

        if (isset($retorno_value['error'])) {
            return $retorno_value;
        }
        $resultado_insert = [];
        $retorno_busca = [];
        $localizado = false;
        // $dados['contratos_afetar'] = [];



        // VERIFICAR SE JÁ TEM CONFIGURAÇÃO PARA  O CONTRATOR 
        if (is_array($dados['contratos_afetar']) && !empty($dados['contratos_afetar'])) {
            print_r($dados['id']);
            foreach ($dados['contratos_afetar'] as $key => $contrato_afetar) {

                $retorno_busca_nivel = $this->modelGrupo->nivel_cliente_grupo($contrato_afetar);

                // Se já existe, adiciona em existentes
                if (isset($retorno_busca_nivel[0]) && !empty($retorno_busca_nivel[0])) {
                    $retorno_busca[] = [
                        'contrato' => $contrato_afetar,
                        'nivel' => true
                    ];
                    $localizado = true;

                    if ($localizado && $dados['id'] == 5) {

                        echo "TENHO O RESULTADO TRUE AQUI!!\n";
                        $resultado_insert[] = $this->modelGrupo->Upsert_grupo($retorno_tipo,  $contrato_afetar, $dados['value_limite'], $dados['id'], $dados['c_interno']);
                    }

                    if ($localizado && $dados['id'] == 4) {

                        echo "TENHO O RESULTADO TRUE AQUI!!\n";
                        $resultado_insert[] = $this->modelGrupo->Upsert_grupo($retorno_tipo,  $contrato_afetar, $dados['value_limite'], $dados['id'], $dados['c_interno']);
                    }
                    continue;
                }
                // Se não existe, insere/ e guarda o resultado
                if (!$localizado) {
                    echo "<pre>";
                    echo "saiu aqui!!";
                    // $resultado_insert[] = $this->modelGrupo->Upsert_grupo($retorno_tipo,  $contrato_afetar, $dados['value_limite'], $dados['id'], $dados['c_interno']);

                }
            }
        } else {
            return [
                'error' => true,
                'message' => 'contratos está vazio ou não é um array'
            ];
        }

        return [
            'existentes' => $retorno_busca,
            'inseridos' => $resultado_insert
        ];
    }
}
