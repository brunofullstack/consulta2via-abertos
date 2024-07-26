<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar faturas em aberto</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        .container {
            margin-top: 50px;
        }

        .table-container {
            margin-top: 30px;
        }

        .table-container table {
            width: 100%;
        }

        .modal-content {
            max-width: 600px;
            margin: auto;
        }

        .modal-body pre {
            white-space: pre-wrap;
        }

        .btn-orange {
            --bs-btn-color: #fff;
            --bs-btn-bg: #fc3423;
            --bs-btn-border-color: #fc3423;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #0b5ed7;
            --bs-btn-hover-border-color: #0a58ca;
            --bs-btn-focus-shadow-rgb: 49, 132, 253;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #0a58ca;
            --bs-btn-active-border-color: #0a53be;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            --bs-btn-disabled-color: #fff;
            --bs-btn-disabled-bg: #fc3423;
            --bs-btn-disabled-border-color: #fc3423;
        }

        .active>.page-link,
        .page-link.active {
            z-index: 3;
            color: #ffffff;
            background-color: #fc3423;
            border-color: #ffffff;
        }
        }

        .pagination {
            --bs-pagination-active-color: #fff;
            --bs-pagination-active-bg: #fc3423;
            --bs-pagination-active-border-color: #fc3423;
            list-style: none;
        }

        #tabelaCobrancas tbody tr {
            cursor: pointer;
        }
    </style>

</head>

<body>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <!-- <img src="logo.png" alt="Logo" width="30" height="24" class="d-inline-block align-text-top"> -->
                <img src="logo.png" alt="Logo" width="120" class="d-inline-block align-text-top">
            </a>
        </div>
    </nav>

    <div class="container">

        <h3 class="text-center">Consultar faturas em aberto</h3>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="cpfCnpjInput">Digite o CPF/CNPJ</label>
                        <input type="text" class="form-control" id="cpfCnpjInput" name="cpfcnpj" placeholder="CPF/CNPJ"
                            required>
                    </div>
                    <button type="submit" class="btn btn-orange mt-3">Consultar</button>
                </form>
            </div>
        </div>

        <div id="resultado" class="table-container">
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cpfcnpj'])):
                $cpfCnpj = $_POST['cpfcnpj'];
                $token = getenv('API_TOKEN'); // Substitua pelo token fornecido pela Ksys Sistemas de Gestão
                // Remover pontos, traços e barras
                $cpfCnpj = preg_replace('/[^\d]/', '', $cpfCnpj);

                // Requisição para obter cod_cliente
                $urlCliente = "https://api-bemtevi.ksys.net.br/cliente";
                $dataCliente = json_encode(["cpfcnpj" => $cpfCnpj]);

                $optionsCliente = [
                    "http" => [
                        "header" => "Content-Type: application/json\r\n" .
                            "token: $token\r\n",
                        "method" => "GET",
                        "content" => $dataCliente,
                    ],
                ];

                $contextCliente = stream_context_create($optionsCliente);
                $responseCliente = file_get_contents($urlCliente, false, $contextCliente);

                if ($responseCliente === FALSE)
                {
                    $resultado = ['error' => 'Erro ao consultar a API de cliente.'];
                } else
                {
                    $resultadoCliente = json_decode($responseCliente, true);
                    if (isset($resultadoCliente['data'][0]['cod_cliente']))
                    {
                        $codCliente = $resultadoCliente['data'][0]['cod_cliente'];
                        $nomeCliente = $resultadoCliente['data'][0]['nome'];
                        
                        // Requisição para obter cobranças usando cod_cliente
                        $urlCobrancas = "https://api-bemtevi.ksys.net.br/cobrancas";
                        $data = [
                            "codcliente" => $codCliente,
                            "situacao" => 0,
                            "abertas" => 1
                        ];
                        $optionsCobrancas = [
                            "http" => [
                                "header" => "Content-Type: application/json\r\n" .
                                            "token: $token\r\n",
                                "method" => "GET",
                                "content" => json_encode($data),
                            ],
                        ];
                        
                        $contextCobrancas = stream_context_create($optionsCobrancas);
                        $responseCobrancas = file_get_contents($urlCobrancas, false, $contextCobrancas);
                        
                        if ($responseCobrancas === FALSE)
                        {
                            $resultado = ['error' => 'Erro ao consultar a API de cobranças.'];
                        } else
                        {
                            $resultado = json_decode($responseCobrancas, true);
                        }
                        
                    } else
                    {
                        $resultado = ['error' => 'Cliente não encontrado.'];
                    }
                }
                ?>

                <?php if (isset($resultado) && !isset($resultado['error'])): ?>
                    <?php if (!empty($resultado['data'])): ?>
                        <h4><strong>Cliente: </strong> <?= $nomeCliente ?></h4>
                        <h6><strong>código: </strong> <?= $codCliente ?></h6>
                        <table id="tabelaCobrancas" class="table table-striped">
                            <thead>
                                <tr>
                                <th>Código da Cobrança</th>
                                    <th>Valor da Cobrança</th>
                                    <th>Data de Vencimento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultado['data'] as $cobranca): ?>

                                    <?php
                                    $dataVencimento = new DateTime($cobranca['datavencimento']);
                                    $dataFormatada = $dataVencimento->format('d/m/Y');
                                    ?>

                                    <tr data-cod_cliente="<?php echo htmlspecialchars($cobranca['cod_cliente']); ?>"
                                        data-codcobranca="<?php echo htmlspecialchars($cobranca['codcobranca']); ?>" class="consulta">
                                        <td><?php echo htmlspecialchars($cobranca['codcobranca']); ?></td>
                                        <td>R$ <?php echo htmlspecialchars($cobranca['valorcobranca']); ?></td>
                                        <td><?php echo htmlspecialchars($dataFormatada); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info mt-3">Nenhuma cobrança encontrada.</div>
                    <?php endif; ?>
                <?php elseif (isset($resultado['error'])): ?>
                    <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($resultado['error']); ?></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalhesModalLabel">Detalhes da Cobrança</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="detalhesCobranca"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery, DataTables, and Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Inicializando DataTables
            $('#tabelaCobrancas').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json"
                }
            });

            // Evento de clique na classe .consulta
            $('.consulta').on('click', function () {
                var codCliente = $(this).data('cod_cliente');
                var codCobranca = $(this).data('codcobranca');

                $.ajax({
                    url: "consultar_segunda_via.php",
                    method: "POST",
                    data: {
                        codcliente: codCliente,
                        codcobranca: codCobranca
                    },
                    success: function (response) {
                        // Parseando a resposta JSON
                        var data = JSON.parse(response);

                        // Montando o conteúdo para exibir
                        var content = `
                        <div class="mb-3">
                            <h5>Download do Boleto em PDF:</h5>
                            <p><a href="${data.caminho_pdf}" target="_blank">${data.caminho_pdf}</a></p>
                        </div>
                        <div class="">
                            <h5>Código de Barras:</h5>
                            <p>${data.cod_barras}</p>
                        </div>
                        <div class="mb-3">
                            <h5>Detalhes da Cobrança:</h5>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Código:</strong> ${data.cobranca.codigo}</li>
                                <li class="list-group-item"><strong>Valor:</strong> R$ ${data.cobranca.valorcobranca}</li>
                                <li class="list-group-item"><strong>Data de Vencimento:</strong> ${data.cobranca.datavencimento}</li>
                                <li class="list-group-item"><strong>Nosso Número:</strong> ${data.cobranca.nossoNumero}</li>
                                <li class="list-group-item"><strong>Seu Número:</strong> ${data.cobranca.seuNumero}</li>
                                <!-- Adicione outros detalhes necessários -->
                            </ul>
                        </div>
                    `;

                        // Inserindo o conteúdo formatado na div e exibindo o modal
                        $('#detalhesCobranca').html(content);
                        $('#detalhesModal').modal('show');
                    },
                    error: function () {
                        $('#detalhesCobranca').text("Erro ao obter os detalhes da cobrança.");
                        $('#detalhesModal').modal('show');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#cpfCnpjInput').mask('000.000.000-00', {
                onKeyPress: function (cpfcnpj, e, field, options) {
                    var masks = ['000.000.000-00', '00.000.000/0000-00'];
                    var mask = (cpfcnpj.length > 14) ? masks[1] : masks[0];
                    $('#cpfCnpjInput').mask(mask, options);
                }
            });
        });
    </script>


</body>

</html>