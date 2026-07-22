<?php require_once('partial/cabecalho.php') ?>


<!-- <button type="button" class="btn btn-primary notification_alerts"> -->

<!-- <div class="row">
    <div class="col-12 col-md-8">.col-12 .col-md-8</div>
    <div class="col-6 col-md-4">.col-6 .col-md-4</div>
</div> -->
<!-- <div class="container-input">
    <div class="profile-button dt-button">
        <button type="button" class="bot" id="notificationButton">
            <img src="../img/alerts.svg" alt="Descrição da imagem" width="30" height="30">
            <span class="badge badge-light info_notification" id="notificationCount">0</span>
        </button>
    </div>
</div> -->
<div class="profile-button dropdown">
    <button
        type="button"
        class="bot dropdown-toggle"
        id="notificationButton"
        data-bs-toggle="dropdown" aria-expanded="false">
        <img src="../img/alerts.svg" width="30" height="30">
        <span class="badge badge-light info_notification" id="notificationCount" width="30" height="30">0</span>
    </button>
    <div class="dropdown-menu dropdown-menu-right" id="notificationList" aria-labelledby="notificationButton">

    </div>
</div>


<!-- SE FOR REDE LIBERA OS OPTIONS ABAIXO -->
<div class="container-input">
    <form id="cadastrar-limite">
        <div class="minha-div">
            <label class="lkpr quebra" for="exampleFormControlInput1" class="form-label">Limite Nível</label>
            <input type="number" class="form-control" id="limit_config">
        </div>
</div>
<div class="container-input">
    <div class="minha-div">
        <input class="form-check-input-" type="radio" name="incluir_contrato" id="radio-todos">
        <label class="form-check-label- lkpr" for="radio-todos">
            Incluir para todos os contratos
        </label>
    </div>
</div>
<div class="container-input">
    <div class="minha-div">
        <input class="form-check-input-" type="radio" name="incluir_contrato" id="radio-selecionado">
        <label class="form-check-label- lkpr" for="radio-selecionado">
            Incluir somente para o contrato selecionado
        </label>
    </div>
</div>
<div class="container-input">
    <div class="col-md-12 text-center">
        <button type="submit" id="btn-incluir" class="btn btn-primary bot btn-incluir">Incluir</button>
        <button type="submit" id="btn-desfazer" class="btn btn-primary bot btn-desfazer">Limpar Filtros</button>
    </div>
</div>
</form>
<div class="spaco"></div>
<div class="container">
    <table id="dados_redes" class="display">
        <thead>
            <tr>
                <th>selecionar</th>
                <th>rede</th>
                <th>Contrato</th>
                <th>Nome</th>
                <th>Limite de nível</th>
                <th>Opçöes</th>
            </tr>
        </thead>

    </table>


</div>


<div class="spaco"></div>
<table width=100% align=center class="cdt3">
    <tr>
        <td width=100% align=left>
            <font class="ctitpg"> © </font> Copyright - 2026 PROSCORE<BR>&nbsp;Versão 3.0 - producao<BR /><b>proScore</b>
            <font class="ctitpg"> ® </font> Todos os direitos reservados</br>As informações contidas neste documento são confidenciais e de uso exclusivo de AREA DE TESTE E QUALIDADE
        </td>
    </tr>
</table>
<div class="toast-wrapper" id="toast-wrapper"></div>

<?php require_once('partial/footer.php') ?>