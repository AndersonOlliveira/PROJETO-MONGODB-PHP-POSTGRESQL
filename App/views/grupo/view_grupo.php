<?php require_once('partial/cabecalho.php') ?>



<div class="container search-container">
    <form id="pesquisar">
        <div class="row g-4 align-items-start">


            <div class="col-md-8">
                <div class="d-flex flex-column">

                    <label for="inputDados" class="label-title">Informe o dado a ser pesquisado</label>
                    <input type="text" id="inputDados" class="form-control form-control-custom">
                </div>
                <div class="mb-3">
                    <select id="n_clientes" class="form-control" style="width: 100%;">
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="tdt3" width="50%">
                    <div class="form-check form-check-reverse">
                        <label class="form-check-label lkpr" for="contratoCliente">Pesquisar por contato </label>
                        <input class="form-check-input text-start d-block" type="radio" name="tipoPesquisa" id="c_contrato">
                    </div>
                    <div class="form-check form-check-reverse">
                        <input class="form-check-input text-start d-block" type="radio" name="tipoPesquisa" id="c_rede">
                        <label class="form-check-label lkpr" for="codRede">Pesquisar por Cod. Rede</label>
                    </div>
                    <div class="form-check form-check-reverse">
                        <input class="form-check-input text-start d-block" type="radio" name="tipoPesquisa" id="n_cliente">
                        <label class="form-check-label lkpr" for="NomeCliente">Pesquisar pro Nome Cliente</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-8 text-center">
                <button type="submit" class="btn btn-primary bot btn-salvar-area">Consultar</button>
                <button type="button" class="btn btn-primary bot btn-salvar-area">Desfazer</button>
            </div>
        </div>
    </form>
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

</body>

</html>