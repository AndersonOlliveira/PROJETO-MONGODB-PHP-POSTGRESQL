

<?php $tctrid = isset($_GET['tctrid']) ? $_GET['tctrid'] : 417039; ?>
<?php $tctraut = isset($_GET['tctraut']) ? $_GET['tctraut'] : 'af7636678c6db2d8f12730339f19e81e' ?>
<head>
    <link href="../../../css/proDecision/style_v1_menu.css?v= <?= time(); ?>" rel="stylesheet">
</head>

<div id="bdSidebar" class="prodecision-menu d-flex flex-column flex-shrink-0 p-3 offcanvas-md offcanvas-start">

    <!-- Logo / marca -->
    <a href="#" class="navbar-brand">
        <div class="brand-logo-wrapper">
            <img src="img/proDecision_logo_escrito_branco.png" alt="proDecision" />
        </div>
        <span class="brand-name">pro<span class="brand-light">Decision</span></span>
    </a>

    <hr />

    <!-- Navegação principal -->
    <div class="sidebar-scroll-area">
        <ul class="mynav nav nav-pills flex-column mb-auto">

            <li class="nav-item mb-1">
                <a href="/proDecision?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
                    class="<?php if ($page == 'index') print 'active'; ?>">
                    <i class="fa fa-home"></i>
                    Consultas
                </a>
                <!-- campos ocultos usados por JS em toda a página -->
                <input type="hidden" id="tctrid" name="tctrid" value="<?php print $tctrid; ?>">
                <input type="hidden" id="tctraut" name="tctraut" value="<?php print $tctraut; ?>">
            </li>

            <li class="nav-item mb-1">
                <a href="/proDecisionAcao?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
                    class="js-link-acao <?php if ($page == 'acao') print 'active'; ?>">
                    <i class="fa fa-file"></i>
                    Ação
                </a>
            </li>

            <li class="nav-item mb-1">
                <a href="/proDecisionComite?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
                    class="<?php if ($page == 'comite') print 'active'; ?>">
                    <i class="fa fa-upload"></i>
                    Comitê
                </a>
            </li>
            
            <li class="nav-item mb-1">
                <a href="/proDecisionStatus?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>"
                    class="<?php if ($page == 'status') print 'active'; ?>">
                    <i class="fa fa-upload"></i>
                    Status
                </a>
            </li>

            <!-- Legado oculto — preservado para não quebrar JS externo -->
            <li class="nav-item mb-1" style="display:none !important;">
                <a href="/srv/srvcns.chp?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>" class="mx-auto p-2 text-black">
                    <img src="img/shape.png" alt="" width="20" />
                    Status
                </a>
            </li>
            <li class="nav-item mb-1" style="display:none !important;">
                <a href="/srv/srvcns.chp?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>" class="mx-auto p-2 text-black">
                    <i class="fa fa-power-off"></i>
                    Serviços
                </a>
            </li>
        </ul>
    </div>

    <!-- Perfil -->
    <div id="sidebar-profile" class="sidebar-profile">
        <div class="profile-label">Perfil</div>
        <a href="/srv/srvsai.chp?tctrid=<?php print $tctrid; ?>&tctraut=<?php print $tctraut; ?>" style="display:none !important;">
            Desconectar do Sistema
        </a>
    </div>

    <!-- Usuário logado -->
    <div id="sidebar-user" class="sidebar-user">
        <div class="user-avatar"><?php print isset($nmusr) ? mb_substr($nmusr, 0, 1, 'UTF-8') : 'U'; ?></div>
        <div class="user-info">
            <div id="user-name" class="user-name"><?php print isset($nmusr) ? $nmusr : 'Usuário'; ?></div>
            <div id="user-id" class="user-id"></div>
        </div>
    </div>




</div>

<!-- Modal proZoomy — mantido para compatibilidade -->
<div class="modal fade" id="modalProZoomy" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ProZoomy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table width="95%" border="0" align="center">
                    <tr>
                        <td class="ttitpg" width="90%" align="left">
                            <font class="recret lkpr">MAPAS</font>
                        </td>
                    </tr>
                </table>
                <table width="95%" border="0" align="center" id="tabelaMapasProZoomy">
                    <tbody id="listaMapasProZoomy"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal proCura — mantido para compatibilidade -->
<div class="modal fade" id="modalProCura" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">proCura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table width="95%" border="0" align="center">
                    <tr>
                        <td class="ttitpg" width="90%" align="left">
                            <font class="recret lkpr"></font>
                        </td>
                    </tr>
                </table>
                <table width="95%" border="0" align="center" id="tabelaMapasProCura">
                    <tbody id="listaMapasProCura"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal MANUTENCAO — mantido para compatibilidade -->
<div class="modal fade" id="modalManutecao" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">* Serviços relacionados a regra de negócios </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table width="95%" border="0" align="center">
                    <tr>
                        <td class="ttitpg" width="90%" align="left">
                            <font class="recret lkpr"></font>
                        </td>
                    </tr>
                </table>
                <table width="95%" border="0" align="center" id="tabelaManutecao">
                    <tbody id="listaManutencao"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
