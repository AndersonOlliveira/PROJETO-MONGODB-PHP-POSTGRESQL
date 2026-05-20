<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <title>Bootstrap demo</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Parcelas em Atraso</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/flatpickr/flatpickr.min.css" rel="stylesheet">
    <link href="../css/Relatorio/viewRelatorio.css?v= <?= time(); ?>" rel="stylesheet">


</head>
<style>
    .container-barra {
        width: 100%;
        max-width: 400px;
        height: 20px;
        background-color: #ddd;
        border-radius: 10px;
        overflow: hidden;
        /* Garante que a barra interna respeite os cantos arredondados */
    }

    .barra-carregando {
        width: 0;
        height: 100%;
        background-color: #4caf50;
        border-radius: 10px;
        animation: carregar 2s ease-in-out infinite;
        /* Animação infinita de 2 segundos */
    }

    @keyframes carregar {
        0% {
            width: 0%;
        }

        50% {
            width: 100%;
        }

        100% {
            width: 0%;
        }
    }
</style>

<body>
    </head>

    <body>

        <div class="container-fluid p-4">

            <!-- TOPO -->
            <div class="top-bar">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                    <div>
                        <h4 class="mb-1 fw-bold">
                            Relatório de parcelas em atraso
                        </h4>

                        <small class="text-muted">
                            Parcelas pendentes ou vencidas com gestão de tratativas
                        </small>
                    </div>

                    <div class="d-flex gap-3">

                        <button class="btn btn-outline-primary px-4 rounded-pill">
                            Menu de serviços
                        </button>

                        <button class="btn btn-outline-secondary px-4 rounded-pill">
                            Início
                        </button>

                        <button class="btn btn-outline-danger px-4 rounded-pill">
                            Sair do sistema
                        </button>

                    </div>

                </div>

                <!-- FILTROS -->
                <div class="row mt-4 g-3 align-items-end">



                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            Data inicial
                        </label>
                        <input type="text" class="form-control" id="range" placeholder="De">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            Data final
                        </label>
                        <input type="text" class="form-control " id="range_ate" placeholder="Até">

                    </div>
                    <div class="col-md-2">
                        <span id="error-message" class="text-danger" style="display: none;"></span>
                    </div>




                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select class="form-select">
                            <option>Todos</option>
                            <option>Pendente</option>
                            <option>Resolvido</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            Dias atraso
                        </label>

                        <select class="form-select">
                            <option>Todos</option>
                            <option>1-10</option>
                            <option>11-30</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="container-fluid p-4">
                <!-- TABELA -->
                <div class="table-container">

                    <table id='table-relatorio' class="table align-middle">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Perfil Cliente</th>
                                <th>Compra/Crédito</th>
                                <th>N.Nro</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Doc. Ger.</th>
                                <th>Dias atraso</th>
                                <th>Vendedor</th>
                                <th>Última tratativa</th>
                                <th>Status da tratativa</th>
                                <th>Próxima ação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="corpo-tabela-relatorio">

                        </tbody>
                    </table>

                </div>
            </div>

            <div id="carregamento" class="container-barra" style="display: none;">
                <div class="barra-carregando"></div>
            </div>


            <!-- Modal -->
            <div class="modal fade" id="modal-adicionar" tabindex="-1" aria-labelledby="modal-Adicionar-Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modal-Adicionar-Label">Nova Tratativa </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form class="row g-3 needs-validation" novalidate>
                                <div class="col-md-4">
                                    <label for="validationCustom01" class="form-label">Cliente</label>
                                    <input type="text" class="form-control" id="validationCustom01" value="Mark" required>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustom02" class="form-label">Last name</label>
                                    <input type="text" class="form-control" id="validationCustom02" value="Otto" required>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="validationCustomUsername" class="form-label">Username</label>
                                    <div class="input-group has-validation">
                                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                                        <input type="text" class="form-control" id="validationCustomUsername" aria-describedby="inputGroupPrepend" required>
                                        <div class="invalid-feedback">
                                            Please choose a username.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="validationCustom03" class="form-label">City</label>
                                    <input type="text" class="form-control" id="validationCustom03" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid city.
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="validationCustom04" class="form-label">State</label>
                                    <select class="form-select" id="validationCustom04" required>
                                        <option selected disabled value="">Choose...</option>
                                        <option>...</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a valid state.
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="validationCustom05" class="form-label">Zip</label>
                                    <input type="text" class="form-control" id="validationCustom05" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid zip.
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="invalidCheck" required>
                                        <label class="form-check-label" for="invalidCheck">
                                            Agree to terms and conditions
                                        </label>
                                        <div class="invalid-feedback">
                                            You must agree before submitting.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary" type="submit">Submit form</button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

    </body>

</html>



<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>


<script type="text/javascript" src="../Scripts/jquery/jquery.min.js"></script>

<!-- <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> -->

<script src="../../Scripts/tratativa.js"></script>


<!-- Bootstrap -->
<!-- <script type="text/javascript" src="../Scripts/propper/popper.min.js"></script>
<script type="text/javascript" src="../Scripts/bootstrap/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="../Scripts/bootstrap/bootstrap.min.js"></script> -->

<!-- DataTables principal -->
<script type="text/javascript" src="../Scripts/datatable/dataTables.js"></script>

<!-- Extensão Buttons -->
<script type="text/javascript" src="../Scripts/datatable/dataTablesbuttons.js"></script>

<!-- Dependências de exportação -->
<script type="text/javascript" src="../Scripts/jszip/jszip.min.js"></script>
<!-- <script type="text/javascript" src="../Scripts/pdfMake/pdfmake.min.js"></script> -->
<script type="text/javascript" src="../Scripts/pdfMake/vfs_fonts.js"></script>

<!-- Botões HTML5 -->
<script type="text/javascript" src="../Scripts/datatable/buttonshtml5.min.js"></script>

<!-- Flatpickr -->
<script type="text/javascript" src="../Scripts/flatpickr/flatpickr.js"></script>
<script type="text/javascript" src="../Scripts/flatpickr/languageFlatPickr.js"></script>
<!-- sweetAlert -->
<script type="text/javascript" src="../Scripts/sweetAlert/sweetAlert.js"></script>
<script type="text/javascript" src="../Scripts/flatpickr/flatpickr.js"></script>
<script type="text/javascript" src="../Scripts/flatpickr/languageFlatPickr.js"></script>

</body>

</html>