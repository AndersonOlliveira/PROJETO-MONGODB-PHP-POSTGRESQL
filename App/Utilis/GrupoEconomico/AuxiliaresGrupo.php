<?php

class AuxiliaresGrupo
{

    const TIPO_ATUALIZAR_LIMITE = 5; // INFO PARA ALTERAÇÃO DO LIMITE
    const TIPO_ATUALIZAR_LIMITE_NULL = 4; // INFO PARA ALTERAÇÃO DO LIMITE  NULL
    const TIPO_ATUALIZAR_LIMITE_INSERT = 1;
    const TIPO_P_CONTATO = NULL;
    const TIPO_INSERIR_TODOS = 2;
    const TIPO_INSERIR_SELECIONADO = 1;
    const VALIDAR_CAMPO_LIMITE = 'VAZIO';
    const BUSCA_ID_GRUPO_ECONOMICO = 'GRUPO ECONOMICO';

    const ID_BUSCA_INFO_REDES = 2;

    const ID_BUSCA_INFO_REDES_CONTRATOS = 1;
    const ID_BUSCA_INFO_REDES_CONTRATOS_FIVE = 5;

    const TEXTOS_INSERIR = [
        1 => [
            'TEXTO' => 'Sucesso ao inserir limite!',

        ],
        //REGRA PARA TALVEZ LIMPA O CAMPO DE  ATIVO E INATIVO
        2 => [
            'TEXTO' => 'Sucesso ao Cadastar limite!', // PARA CADASTRASTRO PRO ID 
        ],
        3 => [
            'TEXTO' => 'Dados Atualizados',

        ],
        4 => [
            'TEXTO' => 'Sucesso em remover limite!'
        ],
        5 => [
            'TEXTO' => 'Dados Atualizado com sucesso!'
        ]
    ];
    const TEXTOS_INSERIR_OCORRENCIAS = [
        1 => [
            'TEXTO' => 'Foi Realizado a configuração do limite para o ! ',

        ],
        //REGRA PARA TALVEZ LIMPA O CAMPO DE  ATIVO E INATIVO
        2 => [
            'TEXTO' => 'Foi Cadastrado novo limite para o contrato ', // PARA CADASTRASTRO PRO ID 
        ],
        3 => [
            'TEXTO' => 'Dados Atualizados ',

        ],
        4 => [
            'TEXTO' => 'Foi Realizado a remoção do limite limite! '
        ],
        5 => [
            'TEXTO' => 'Foi Realizado a alteração do limite! '
        ]
    ];

    public static function getDataAtual()
    {
        return date('Y-m-d');
    }
}
