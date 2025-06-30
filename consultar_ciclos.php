<?php
// consultar_ciclos.php

$nome_arquivo_json = 'ciclos_de_vida.json';
$dados_ciclos = [];

if (file_exists($nome_arquivo_json)) {
    $conteudo_json = file_get_contents($nome_arquivo_json);
    $dados_ciclos = json_decode($conteudo_json, true);

    if ($dados_ciclos === null && json_last_error() !== JSON_ERROR_NONE) {
        // Erro na decodificação JSON, arquivo pode estar corrompido ou vazio
        $dados_ciclos = [];
        $erro_json = "Erro ao ler o arquivo JSON. Pode estar corrompido ou vazio.";
    }
} else {
    $erro_json = "O arquivo 'ciclos_de_vida.json' não foi encontrado.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Ciclos de Vida</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        h1 {
            color: #0056b3;
            text-align: center;
        }
        .top-links {
            text-align: center;
            margin-bottom: 20px;
        }
        .top-links a {
            margin: 0 10px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .top-links a:hover {
            text-decoration: underline;
        }
        table {
            width: 80%; /* Tabela um pouco menor */
            margin: 20px auto; /* Centraliza a tabela */
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .no-data {
            text-align: center;
            color: #555;
            margin-top: 30px;
        }
        .error-message {
            text-align: center;
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Ciclos de Vida dos Produtos Salvos</h1>

    <div class="top-links">
        <a href="index.php">Pesquisar Produtos</a> |
        <a href="consultar_ciclos.php">Consultar Ciclos de Vida</a>
    </div>

    <?php if (isset($erro_json)): ?>
        <p class="error-message"><?php echo htmlspecialchars($erro_json); ?></p>
    <?php elseif (empty($dados_ciclos)): ?>
        <p class="no-data">Nenhum ciclo de vida de produto foi salvo ainda.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Referência</th>
                    <th>Subgrupo</th>
                    <th>Item</th>
                    <th>Ciclo de Vida</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dados_ciclos as $dado): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dado['referencia']); ?></td>
                        <td><?php echo htmlspecialchars($dado['subgrupo']); ?></td>
                        <td><?php echo htmlspecialchars($dado['item']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($dado['ciclo_vida'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>