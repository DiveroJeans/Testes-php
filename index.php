<?php
// index.php
require_once 'conexao.php'; // Inclui o arquivo de conexão

$produtos = []; // Array para armazenar os produtos
$query_sql = "SELECT   
                B.REFERENCIA,
                E.CDITEM_SUBGRUPO,
                E.CDITEM_ITEM,
                E.QTDE_ESTOQUE_ATU,
                IMG.IMAGEM
            FROM SYSTEXTIL.BASI_030 B
            INNER JOIN SYSTEXTIL.ESTQ_040 E ON E.CDITEM_NIVEL99 = B.NIVEL_ESTRUTURA AND E.CDITEM_GRUPO = B.REFERENCIA
            LEFT JOIN (SELECT   
                            NIVEL,
                            GRUPO,
                            SUBGRUPO,
                            ITEM,
                            'https://divero.systextil.com.br/systextil/img' || CAMINHO_IMAGEM AS IMAGEM
                        FROM SYSTEXTIL.BASI_750) IMG ON IMG.GRUPO = E.CDITEM_GRUPO AND IMG.SUBGRUPO = E.CDITEM_SUBGRUPO AND IMG.ITEM = E.CDITEM_ITEM
            WHERE 
                B.NIVEL_ESTRUTURA = 9
                AND (B.REFERENCIA LIKE 'BM%'
                OR B.REFERENCIA LIKE 'BT%'
                OR B.REFERENCIA LIKE 'EX%'
                OR B.REFERENCIA LIKE 'PL%'
                OR B.REFERENCIA LIKE 'PT%'
                OR B.REFERENCIA LIKE 'RB%'
                OR B.REFERENCIA LIKE 'CA%')
                AND E.QTDE_ESTOQUE_ATU > 0
                AND E.DEPOSITO = 003
            ORDER BY 
                B.REFERENCIA ASC";

try {
    $stmt = oci_parse($conn, $query_sql); // Prepara a query
    oci_execute($stmt); // Executa a query

    while ($row = oci_fetch_assoc($stmt)) {
        $produtos[] = $row; // Adiciona cada linha (produto) ao array
    }
    oci_free_statement($stmt); // Libera o statement
} catch (Exception $e) {
    echo "<p>Erro ao consultar produtos: " . $e->getMessage() . "</p>";
}

// oci_close($conn); 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa de Produtos e Ciclo de Vida</title>
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
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        img {
            max-width: 100px;
            height: auto;
            display: block;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 3px;
        }
        .ciclo-vida-botoes {
            display: flex;
            flex-wrap: wrap; /* Permite que os botões quebrem a linha se não houver espaço */
            gap: 5px; /* Espaçamento entre os botões */
            justify-content: center; /* Centraliza os botões */
        }
        .ciclo-vida-botoes button {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
            background-color: #f0f0f0;
            color: #333;
            font-size: 0.9em;
            transition: background-color 0.2s, border-color 0.2s;
        }
        .ciclo-vida-botoes button:hover {
            background-color: #e0e0e0;
        }
        .ciclo-vida-botoes button.selected {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        /* O botão "Salvar" não é mais necessário para o salvamento individual, mas pode ser mantido para outras ações */
        .btn-salvar {
            display: none; /* Esconde o botão de salvar, pois o salvamento é automático */
            width: 200px;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
        }
        .btn-salvar:hover {
            background-color: #218838;
        }
        .mensagem-status { /* Alterado para classe para permitir múltiplas mensagens */
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
            font-size: 0.9em;
            opacity: 0; /* Começa invisível */
            transition: opacity 0.5s ease-in-out; /* Transição suave */
        }
        .mensagem-status.show {
            opacity: 1; /* Torna visível */
        }
    </style>
</head>
<body>
    <h1>Pesquisa de Produtos</h1>

    <div class="top-links">
        <a href="index.php">Pesquisar Produtos</a> |
        <a href="consultar_ciclos.php">Consultar Ciclos de Vida</a>
    </div>

    <?php if (empty($produtos)): ?>
        <p style="text-align: center;">Nenhum produto encontrado com os critérios da pesquisa.</p>
    <?php else: ?>
        <form id="formProdutos"> 
            <table>
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Referência</th>
                        <th>Subgrupo</th>
                        <th>Item</th>
                        <th>Quantidade em Estoque</th>
                        <th>Ciclo de Vida</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td>
                                <?php if (!empty($produto['IMAGEM'])): ?>
                                    <img src="<?php echo htmlspecialchars($produto['IMAGEM']); ?>" alt="Imagem do Produto">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($produto['REFERENCIA']); ?></td>
                            <td><?php echo htmlspecialchars($produto['CDITEM_SUBGRUPO']); ?></td>
                            <td><?php echo htmlspecialchars($produto['CDITEM_ITEM']); ?></td>
                            <td><?php echo htmlspecialchars($produto['QTDE_ESTOQUE_ATU']); ?></td>
                            <td>
                                <div class="ciclo-vida-botoes" 
                                     data-referencia="<?php echo htmlspecialchars($produto['REFERENCIA']); ?>"
                                     data-subgrupo="<?php echo htmlspecialchars($produto['CDITEM_SUBGRUPO']); ?>"
                                     data-item="<?php echo htmlspecialchars($produto['CDITEM_ITEM']); ?>">
                                    <button type="button" data-ciclo="fixo">Fixo</button>
                                    <button type="button" data-ciclo="liberado">Liberado</button>
                                    <button type="button" data-ciclo="Finalizar">Finalizar</button>
                                    <div class="mensagem-status" id="status-<?php echo htmlspecialchars($produto['REFERENCIA'] . '-' . $produto['CDITEM_SUBGRUPO'] . '-' . $produto['CDITEM_ITEM']); ?>"></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn-salvar">Salvar Ciclos de Vida</button> 
        </form>
        <div id="mensagem-status-geral"></div> <?php endif; ?>

    <script>
        // Função para exibir mensagens de status temporariamente
        function showStatusMessage(elementId, message, type) {
            const statusElement = document.getElementById(elementId);
            if (!statusElement) return;

            statusElement.textContent = message;
            statusElement.style.color = type === 'success' ? 'green' : 'red';
            statusElement.classList.add('show'); // Torna visível

            setTimeout(() => {
                statusElement.classList.remove('show'); // Esconde após 3 segundos
            }, 3000);
        }

        // Adiciona um listener para os cliques nos botões de ciclo de vida
        document.querySelectorAll('.ciclo-vida-botoes button').forEach(button => {
            button.addEventListener('click', function() {
                const parentDiv = this.closest('.ciclo-vida-botoes');
                const referencia = parentDiv.dataset.referencia;
                const subgrupo = parentDiv.dataset.subgrupo;
                const item = parentDiv.dataset.item;
                const cicloSelecionado = this.dataset.ciclo;
                
                const produtoKey = `${referencia}-${subgrupo}-${item}`;
                const statusElementId = `status-${produtoKey}`; // ID específico para a mensagem de status do produto

                // Remove a classe 'selected' de todos os botões no grupo atual
                parentDiv.querySelectorAll('button').forEach(btn => {
                    btn.classList.remove('selected');
                });

                // Adiciona a classe 'selected' ao botão clicado
                this.classList.add('selected');

                // Prepara os dados para enviar (apenas o produto atual)
                const dadosParaSalvar = [{
                    referencia: referencia,
                    subgrupo: subgrupo,
                    item: item,
                    ciclo_vida: cicloSelecionado
                }];

                // Envia os dados para o script PHP via AJAX
                fetch('salvar_ciclo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dadosParaSalvar)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showStatusMessage(statusElementId, 'Salvo!', 'success');
                    } else {
                        showStatusMessage(statusElementId, 'Erro ao salvar: ' + data.message, 'error');
                        console.error('Erro ao salvar:', data.message);
                    }
                })
                .catch(error => {
                    showStatusMessage(statusElementId, 'Erro de comunicação.', 'error');
                    console.error('Erro na requisição:', error);
                });
            });
        });

        // Remove o listener de submit do formulário, pois o salvamento é individual
        document.getElementById('formProdutos').addEventListener('submit', function(event) {
            event.preventDefault(); // Apenas impede o envio padrão, mas não faz mais nada
            // console.log("Formulário submetido, mas salvamento é automático por botão.");
        });
    </script>
</body>
</html>