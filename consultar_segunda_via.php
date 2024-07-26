<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codcliente = $_POST['codcliente'];
    $codcobranca = $_POST['codcobranca'];
    $token = '1oUT7Jv66a13a7a80d46'; // Substitua pelo token fornecido pela Ksys Sistemas de Gestão

    $data = [
        'codcliente' => $codcliente,
        'codcobranca' => $codcobranca
    ];

    $url = 'https://api-bemtevi.ksys.net.br/cobranca/segundaVia';
    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n" .
                        "token: $token\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    echo($response);die();

    if ($response === FALSE) {
        echo json_encode(['error' => 'Erro ao consultar a API.']);
    } else {
        echo $response;
    }
}
?>
