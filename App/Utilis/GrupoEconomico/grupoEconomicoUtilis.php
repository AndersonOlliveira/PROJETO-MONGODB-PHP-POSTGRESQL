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

        require_once __DIR__ . '/AuxiliaresGrupo.php';

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


    public function process_dados_grupo_OLD($dados)
    {
        $retorno_tipo = $this->functionsGrupo->getDados_tabela_grupo($dados['idAcao']);

        if (isset($retorno_tipo['error'])) {
            return $retorno_tipo;
        }

        // Valida limite apenas se não for ação de "limite null"
        if ($dados['idAcao'] != AuxiliaresGrupo::TIPO_ATUALIZAR_LIMITE_NULL) {
            $retorno_value = $this->functionsGrupo->validar_campo_limite($dados['value_limite']);
            if (isset($retorno_value['error'])) {
                return $retorno_value;
            }
        }

        $retorno_busca = [];
        $resultados = [];
        $localizado = false;



        if (is_array($dados['contratos_afetar']) && !empty($dados['contratos_afetar'])) {
            foreach ($dados['contratos_afetar'] as $contrato_afetar) {

                $retorno_busca_nivel = $this->modelGrupo->nivel_cliente_grupo($contrato_afetar);

                // Caso já exista limite cadastrado
                if (isset($retorno_busca_nivel[0]) && $retorno_busca_nivel[0] == AuxiliaresGrupo::VALIDAR_CAMPO_LIMITE) {
                    $retorno_busca[] = [
                        'contrato' => $contrato_afetar,
                        'nivel' => true
                    ];
                    $localizado = true;

                    // Se ação for atualizar, permite alteração
                    if (
                        $dados['idAcao'] == AuxiliaresGrupo::TIPO_ATUALIZAR_LIMITE
                        || $dados['idAcao'] == AuxiliaresGrupo::TIPO_ATUALIZAR_LIMITE_NULL
                    ) {

                        $resultados[] = $this->modelGrupo->Upsert_grupo(
                            $retorno_tipo,
                            $contrato_afetar,
                            $dados['value_limite'],
                            $dados['idAcao'],
                            $dados['c_interno']
                        );
                    } else {
                        // Se ação for inserir, mas já existe, retorna erro
                        $resultados[] = [
                            'error' => true,
                            'message' => "Contrato {$contrato_afetar} já possui limite cadastrado. Utilize a opção alterar."
                        ];
                    }
                } else {
                    // Caso não exista limite e ação seja inserir
                    if ($dados['idAcao'] == AuxiliaresGrupo::TIPO_INSERIR_TODOS) {
                        $resultados[] = $this->modelGrupo->Upsert_grupo(
                            $retorno_tipo,
                            $contrato_afetar,
                            $dados['value_limite'],
                            $dados['idAcao'],
                            $dados['c_interno']
                        );
                    }
                }

                // if($resultados){

                // }
            }
        } else {
            return [
                'error' => true,
                'message' => 'contratos está vazio ou não é um array'
            ];
        }

        return [
            'existentes' => $retorno_busca,
            'info_process' => $resultados,
            'info' => !empty($resultados)
        ];
    }
    public function process_dados_grupo($dados)
    {
        $retorno_tipo = $this->functionsGrupo->getDados_tabela_grupo($dados['idAcao']);
        if (isset($retorno_tipo['error'])) {
            return $retorno_tipo;
        }

        // Valida limite apenas se não for ação de atualizar limite null
        if ($dados['idAcao'] != AuxiliaresGrupo::TIPO_ATUALIZAR_LIMITE_NULL) {
            $retorno_value = $this->functionsGrupo->validar_campo_limite($dados['value_limite']);
            if (isset($retorno_value['error'])) {
                return $retorno_value;
            }
        }


        $resultados = [];
        $existentes = [];

        if (is_array($dados['contratos_afetar']) && !empty($dados['contratos_afetar'])) {
            foreach ($dados['contratos_afetar'] as $contrato_afetar) {

                $retorno_busca_nivel = $this->modelGrupo->nivel_cliente_grupo($contrato_afetar);

                // Caso não exista nenhum registro → INSERIR
                if ($retorno_busca_nivel === false) {
                    $resultados[] = $this->modelGrupo->Upsert_grupo(
                        $retorno_tipo,
                        $contrato_afetar,
                        $dados['value_limite'],
                        $dados['idAcao'],
                        $dados['c_interno']
                    );
                    continue;
                }

                // Se existe mas está vazio → pode atualizar
                if ($retorno_busca_nivel[0]['nivel_atual'] === AuxiliaresGrupo::VALIDAR_CAMPO_LIMITE) {

                    $resultados[] = $this->modelGrupo->Upsert_grupo(
                        $this->functionsGrupo->getDados_tabela_grupo(AuxiliaresGrupo::TIPO_ATUALIZAR_LIMITE),
                        $contrato_afetar,
                        $dados['value_limite'],
                        AuxiliaresGrupo::TIPO_ATUALIZAR_LIMITE,
                        $dados['c_interno']
                    );
                    continue;
                }

                if ($dados['idAcao'] != AuxiliaresGrupo::TIPO_INSERIR_TODOS) {
                    switch ($dados['idAcao']) {
                        case AuxiliaresGrupo::TIPO_ATUALIZAR_LIMITE:
                        case AuxiliaresGrupo::TIPO_ATUALIZAR_LIMITE_NULL:
                            $resultados[] = $this->modelGrupo->Upsert_grupo(
                                $retorno_tipo,
                                $contrato_afetar,
                                $dados['value_limite'],
                                $dados['idAcao'],
                                $dados['c_interno']
                            );
                            break;
                    }
                }

                // Se já existe e está preenchido → não pode alterar
                $existentes[] = [
                    'contrato' => $contrato_afetar,
                    'nivel' => $retorno_busca_nivel[0]['nivel_atual']
                ];
                $resultados[] = [
                    'error' => true,
                    'message' => "Contrato {$contrato_afetar} já possui limite cadastrado ({$retorno_busca_nivel[0]['nivel_atual']}). Utilize a opção alterar."
                ];
            }
        } else {
            return [
                'error' => true,
                'message' => 'contratos está vazio ou não é um array'
            ];
        }

        return [
            'existentes' => $existentes,
            'info_process' => $resultados,
            'info' => !empty($resultados)
        ];
    }
}
