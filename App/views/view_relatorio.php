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


</head>

<body>

    <style>
        body {
            background: #f5f7fb;
            font-family: Arial, Helvetica, sans-serif;
        }

        .top-bar {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }

        .table-container {
            background: #fff;
            border-radius: 14px;
            padding: 15px;
            overflow-x: auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }

        table {
            min-width: 1700px;
        }

        thead th {
            font-size: 13px;
            color: #2463eb !important;
            border-bottom: 2px solid #2463eb !important;
            white-space: nowrap;
        }

        tbody td {
            font-size: 13px;
            vertical-align: middle;
        }

        .badge-custom {
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pendente {
            background: #fff1c7;
            color: #a87800;
        }

        .badge-andamento {
            background: #dbeafe;
            color: #2563eb;
        }

        .badge-resolvido {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-semretorno {
            background: #e5e7eb;
            color: #374151;
        }

        .dias-atraso {
            color: #ef4444;
            font-weight: bold;
        }

        .acoes i {
            cursor: pointer;
            margin-right: 12px;
            font-size: 18px;
            color: #6b7280;
        }

        .cliente {
            font-weight: 600;
            color: #374151;
        }

        .ultima-trativa small {
            color: #6b7280;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 4px;
        }


        .celula-destaque {
            color: red !important;
            border: 1px solid #ddd !important;
            padding: 8px !important;
        }
    </style>

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

                        <input type="date" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            Data final
                        </label>

                        <input type="date" class="form-control">
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

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Buscar
                        </label>

                        <input type="text" class="form-control"
                            placeholder="Buscar cliente, parcela...">
                    </div>

                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary">
                            Buscar
                        </button>
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


<script
    src="https://code.jquery.com/jquery-3.7.1.js"
    integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous"></script>
<script src="../../Scripts/tratativa.js"></script>
<script>
    console.log('carregando tratativa.js');
</script>
</body>

</html>