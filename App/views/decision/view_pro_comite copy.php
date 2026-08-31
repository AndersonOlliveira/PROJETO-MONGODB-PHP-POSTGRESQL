<?php require_once('partial/cabecalho.php') ?>



<body>
     <div class="container-fluid p-0 d-flex">
        <?php require_once('partial/menuLateral.php'); ?>
	
        <div class="bg-light flex-fill">
<br>
        
		     <div class="pg-table-section">
                    <div id="tabela_consulta">
                        <form>
                            <table id="lista_comite" class="text-center" style="font-size:10px; width:100%; border-radius:0;">
                                <thead>
                                    <tr class="list-jobs">
                                        <th>Selecianar Agentes</th>
                                        <th>Log</th>
                                        <th>Consulta</th>
                                        <th>CPF/CNPJ</th>
                                        <th>Score</th>
                                        <!-- <th>Decision</th>
                                        <th>Ações</th> -->
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div><!-- /teste -->
                </div><!-- /pg-table-section -->
			

            </div><!-- /pg-content-wrapper -->
        </div><!-- /flex-fill -->
        
          <!-- <div class="container">
                <div class="row justify-content-md-center">
                   
                    <div class="col-md-auto statusC muito">
                     Atribur a mim
                    </div>
                    
                </div>
          </div> -->
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
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form id="aprovar_comite">
        <button type="submit" class="btn btn-primary">Aprovar</button>
         </form>  
    </div>
    </div>
  </div>
</div>

	<?php require_once('partial/footer.php') ?>
    <!-- <script src="../../../Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script> -->
    <script src="Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script>