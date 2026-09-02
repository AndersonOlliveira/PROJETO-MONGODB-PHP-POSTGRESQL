<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width" />
    <title>proDecision</title>
    <link href="../../../css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="../../../css/bootstrap/docs.css" rel="stylesheet">
    <link href="../../../css/flatpickr/flatpickr.min.css" rel="stylesheet">
    <link href="../../../css/select/select2.min.css" rel="stylesheet">
    <link href="./../../css/jqueryCssAuto/jquery-ui.css" rel="stylesheet">
    <link href="./../../css/datatable/datatables.min.css" rel="stylesheet">
    <link href="./../../css/datatable/dataTables.dataTables.css" rel="stylesheet">
    <link href="./../../css/datatable/buttons.dataTables.css" rel="stylesheet">
    <link href="../../../css/proDecision/style_v1.3.css?v= <?= time(); ?>" rel="stylesheet">
</head>


<?php $tctrid = isset($_GET['tctrid']) ? $_GET['tctrid'] : 417039; ?>
<?php $tctraut = isset($_GET['tctraut']) ? '1b46b9b9a3bf06bbdf35c0ccb41add80': $_GET['tctraut']  ?>



<!-- ID VINDO DO BACKEND -->
<input type="hidden" id="d-id-tctrid" value="<?= $tctrid ?>">
<input type="hidden" id="d-tctraut" value="<?= $tctraut ?>">
 <div class="pg-topbar">

    <!-- Grupo de botões: ◄ ► -->
    <div class="pg-btn-group">
        <a class="btn-nav"
            onclick="history.go(-1); return false;"
            href="#"
            title="Voltar">
            <span class="arrow arrow-left"></span>
        </a>
        <a class="btn-nav"
            onclick="history.go(1); return false;"
            href="#"
            title="Avançar">
            <span class="arrow arrow-right"></span>
        </a>

        <!-- divisor interno -->
        <span style="width:1px; height:16px; background:#d4d4d4; margin: 0 2px;"></span>

        <a class="btn-nav btn-home"
            href="/srv/srvcns.chp?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
            title="Serviços">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#6e6e6e">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
            </svg>
        </a>
    </div>

    <!-- Botão apps grid (soluções contratadas) -->
    <div class="pg-apps-btn">
        <button class="pg-apps-trigger" id="pg-apps-trigger" title="Soluções contratadas">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
            </svg>
        </button>
        <div class="pg-apps-dropdown" id="pg-apps-dropdown">
            <div class="pg-apps-dropdown-title">Soluções contratadas</div>
            <div class="pg-apps-grid" id="pg-apps-grid">
                <span class="pg-apps-empty">Carregando...</span>
            </div>
        </div>
    </div>

    <!-- Botão Sair -->
    <a class="btn-sair"
        href="/srv/srvsai.chp?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
        title="Sair do sistema">
        <svg xmlns="http://www.w3.org/2000/svg" width="15.765" height="16" viewBox="0 0 15.765 16">
            <g id="Group" transform="translate(-1237 -36)">
                <path id="Vector" d="M.36.232A1,1,0,0,1,1.768.36l2.083,2.5A1,1,0,0,1,2.315,4.14L.232,1.64A1,1,0,0,1,.36.232Z" transform="translate(1248.667 40.5)" fill="#4b4b4b" fill-rule="evenodd" />
                <path id="Vector-2" d="M.36,4.286A1,1,0,0,1,.231,2.878L2.315.378a1,1,0,1,1,1.536,1.28l-2.083,2.5A1,1,0,0,1,.36,4.286Z" transform="translate(1248.667 42.982)" fill="#4b4b4b" fill-rule="evenodd" />
                <path id="Vector-3" d="M14.5,8a1,1,0,0,1-1,1H7A1,1,0,1,1,7,7h6.5a1,1,0,0,1,1,1ZM0,1A1,1,0,0,1,1,0h9a1,1,0,1,1,0,2H1A1,1,0,0,1,0,1ZM0,15a1,1,0,0,1,1-1h9a1,1,0,1,1,0,2H1a1,1,0,0,1-1-1Z" transform="translate(1237 36)" fill="#4b4b4b" fill-rule="evenodd" />
                <path id="Vector-4" d="M10,0a1,1,0,0,1,1,1V5A1,1,0,1,1,9,5V1a1,1,0,0,1,1-1Zm0,10a1,1,0,0,1,1,1v4a1,1,0,1,1-2,0V11a1,1,0,0,1,1-1ZM1,0A1,1,0,0,1,2,1V15a1,1,0,1,1-2,0V1A1,1,0,0,1,1,0Z" transform="translate(1237 36)" fill="#4b4b4b" fill-rule="evenodd" />
            </g>
        </svg>
    </a>

    <!-- Divisor -->
    <div style="width:1px; height:24px; background:#dde3ed;"></div>

    <!-- Logo proScore -->
    <div class="logo-proscore">
        <img src="img/logoProscore.png" alt="proScore" />
    </div>

</div>

<!-- ── Legado oculto: mantém IDs e hrefs no DOM ── -->
<h2 class="m-3 mb-5 h2Progestor">
    Maximize Seus Dados: Simplicidade, Rapidez e Economia!
</h2>
<nav class="menu-nav" style="--bs-breadcrumb-divider: '>';">
    <ul class="list-group list-group-horizontal menu-list">
        <a id="bt_inicio" href="index.chp?r=index&tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>">
            <li class="list-group-item <?php if ($page == 'index')     print 'active'; ?>">Concultas <img class="imgSuperior" src="img/upload.png"></li>
        </a>
        <a id="bt_extrato" href="index.chp?r=extrato&tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>">
            <li class="list-group-item <?php if ($page == 'extrato')   print 'active'; ?>">Ações <img class="imgSuperior" src="img/file.png"></li>
        </a>
        <a id="bt_grafico" href="index.chp?r=dashboard&tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>">
            <li class="list-group-item <?php if ($page == 'dashboard') print 'active'; ?>">Comitê <img class="imgSuperior" src="img/dashboard.png"></li>
        </a>
    </ul>
</nav>
<hr />


