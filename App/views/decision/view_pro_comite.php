<?php require_once('partial/cabecalho.php') ?>

<head>
    <link href="css/proDecision/style_v1_consultas.css?v= <?= time(); ?>" rel="stylesheet">
</head>

<body>
    <div class="container-fluid p-0 d-flex">
        <div class="bg-light flex-fill">

          <?php require_once 'partial/menuLateral.php'; ?>
           <div class="pg-content-wrapper">
             <div class="pg-table-section">
            <div class="container">
                <div class="row">
                    <div class="col order-last">
                <div class="form-check form-switch d-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="comiteSwitch" style="width: 2.5em; height: 1.25em; cursor: pointer;">
                        <label class="form-check-label text-secondary fw-semibold fs-5" for="comiteSwitch" style="cursor: pointer;">
                            Comitê ativado
                        </label>
                        </div>
                    </div>
                    
                </div>
            </div>
            </div>
                <div class="pg-table-section">
                    <div  class="teste">
                            <form class="col-sm-12">
                        
                                <table id="lista_comite" class="text-center" style="font-size:10px; width:100%; border-radius:0;">
                                    <thead>
                                        <tr class="list-jobs">
                                            <th>Selecianar Agentes</th>
                                            <th>#id</th>
                                            <th>Log</th>
                                            <th>Consulta</th>
                                            <th>Registro</th>
                                            <th>Score</th>
                                          
                                        </tr>
                                    </thead>
                                </table>
                            </form>
                        </div><!-- /teste -->
                    </div><!-- /pg-table-section -->

                
                <!--p>segunda tabela para ve o chat</p-->
                <BR><BR>
                <!-- CHAT DO COMITE -->
                 <label class="form-check-label text-secondary fw-semibold fs-5" for="comiteSwitch" style="cursor: pointer;">
                    Detalhamento de deliberações do comitê
                </label>

                <div class="pg-table-section">
                    <div  class="teste">
                            <form class="col-sm-12">
                        
                                <table id="lista_comite_chat" class="text-center" style="font-size:10px; width:100%; border-radius:0;">
                                    <thead>
                                        <tr class="list-jobs">
                                            <th>#id</th>
                                            <th>Data Consulta</th>
                                            <th>Consulta</th>
                                            <th>Registro</th>
                                            <th>Score</th>
                                            <th>Status</th>
                                            <th>setor</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                </table>
                            </form>
                        </div><!-- /teste -->
                    </div><!-- /pg-table-section -->

                </div><!-- /pg-content-wrapper -->
        </div><!-- /flex-fill -->
  
    </div><!-- /container-fluid -->



    <!-- MODAL PARA APROVACAO -->
     <div class="modal fade" id="ListarComiteModal" tabindex="-1" aria-labelledby="aprovarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="aprovarModalLabel">Selecionar Agentes</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="lista-checkboxes" class="list-group"></div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-mostrar-filtros" data-bs-dismiss="modal">Cancelar</button>
        <form id="aprovar_comite">
        <button type="submit" class="btn btn-primary btn-mostrar-filtros">Associar</button>
         </form>  
    </div>
    </div>
  </div>
</div>

  <div class="modal fade" id="modal-chat" tabindex="-1" aria-labelledby="mensagemModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="mensagemModalLabel">Chat comitê</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">


            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        Consulta
                    </h5>

                    <div>
                        Documento:
                        <strong id="chat-documento"></strong>
                    </div>

                    <div>
                        Score:
                        <strong id="chat-score"></strong>
                    </div>
                </div>
            </div>
             <div id="chat-mensagens" class="chat-container"></div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-mostrar-filtros" data-bs-dismiss="modal">Cancelar</button>
    
    </div>
    </div>
  </div>
</div>
           

</div>

	<?php require_once('partial/footer.php') ?>
    <!-- <script src="../../../Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script> -->
    <script src="Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script>