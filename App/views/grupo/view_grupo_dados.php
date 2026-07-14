<?php require_once('partial/cabecalho.php') ?>



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
<div class="row mt-4">
    <div class="col-md-8 text-center">
        <button type="submit" id="btn-incluir" class="btn btn-primary bot btn-incluir">Incluir</button>
        <button type="submit" id="btn-desfazer" class="btn btn-primary bot btn-desfazer">Desfazer</button>
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