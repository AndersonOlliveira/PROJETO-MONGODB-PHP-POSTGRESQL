<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width" />
    <title>Grupo Econômico -Configuração Limite de níveis</title>
    <link href="../../../css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="../../../css/bootstrap/docs.css" rel="stylesheet">
    <link href="../../../css/flatpickr/flatpickr.min.css" rel="stylesheet">
    <link href="../../../css/select/select2.min.css" rel="stylesheet">
    <link href="./../../css/jquery/jquery-ui.min.css" rel="stylesheet">
    <link href="../../../css/Relatorio/viewGrupo.css?v= <?= time(); ?>" rel="stylesheet">
</head>
<?php $tctrid = isset($_GET['tctrid']) ? $_GET['tctrid'] : 417039; ?>
<?php $tctraut = isset($_GET['tctraut']) ? $_GET['tctraut'] : 'a14beaccd7f530ea7e7c8847d35cd0af' ?>

<!-- ID VINDO DO BACKEND -->
<input type="hidden" id="d-id" value="<?= $tctrid ?>">
<input type="hidden" id="d-tctraut" value="<?= $tctraut ?>">

<!-- topbar -->
<div class="topbar ttitpg">
    <div class="topbar-brand">
        <!-- <div class="dot"></div> -->
        <td valign="top" class="ttitpg" width="40%">

            <div class="lkpr">
                <font class="ctitpg">Alterações Níveis de contrato </font>

                <!-- <small>Indicadores</small> -->
            </div>
        </td>
    </div>


    <div class="topbar-actions">

        <a id="" class="btn-top primary tacn voltar_menu"
            href="/viewGrupoEconocio"
            title="Serviços">
            <div class="lkpr">Menu de serviços</div>

        </a>

        <a id="" class="btn-top primary tacn voltar_menu"
            href="/viewGrupoEconocio"
            title="Serviços">
            <div class="lkpr">Início</div>

        </a>

        <a class="btn-top danger tacn voltar_menu"
            href="/srv/srvsai.chp?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
            title="Sair do sistema">
            <div class="lkpr">Sair</div>
        </a>
    </div>
</div>