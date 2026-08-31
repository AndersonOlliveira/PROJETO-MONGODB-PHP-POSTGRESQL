<?php require_once('partial/cabecalho.php') ?>

<head>
    <link href="../../../css/proDecision/style_v1_consultas.css?v= <?= time(); ?>" rel="stylesheet">
</head>

<body>
    <div class="container-fluid p-0 d-flex">
          <?php require_once 'partial/menuLateral.php'; ?>
           <div class="pg-content-wrapper">
             <div class="pg-table-section">
        <div class="container">
                    <div id="teste" class="row">
                        <form class="col-sm-12">
                           <table id="lista_status"lass="text-center tableJobs"
                                style="font-size:10px; width:100%; border-radius:0;">
                                <thead>
                                    <tr>
                                        <th>log</th>
                                        <th>setor</th>
                                         <th>usuário</th>
                                         <th>Justificativa</th>
                                       <th>status</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div><!-- /teste -->
                </div><!-- /pg-table-section -->

            </div><!-- /pg-content-wrapper -->
        </div><!-- /flex-fill -->
        
	<?php require_once('partial/footer.php') ?>
    <!-- <script src="../../../Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script> -->
    <script src="Scripts/ProDecisionConsultas.js?v=<?= time(); ?>"></script>