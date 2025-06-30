<?php
// salvar_ciclo.php

// Debug 1: Antes de qualquer coisa
echo "Debug 1: Script iniciando.<br>"; 
flush(); // Garante que a saída seja enviada imediatamente

header('Content-Type: application/json'); // Define o tipo de conteúdo da resposta como JSON

// Debug 2: Após o header (importante, pois headers não podem ser enviados após saída)
// Se você vir "Debug 1" e uma página em branco, o erro é no header.
// Se você vir "Debug 1" e "Debug 2", e depois em branco, o erro está mais abaixo.
echo "Debug 2: Header enviado.<br>"; 
flush();

// Se o problema fosse aqui, veríamos o Debug 2
// Remova isso depois do teste: exit("Debug: parando aqui."); 

$response = ['status' => 'error', 'message' => '']; // Estrutura da resposta padrão

try {
    // Debug 3: Antes de pegar o conteúdo da requisição
    echo "Debug 3: Antes de pegar input.<br>";
    flush();

    // Pega o corpo da requisição POST (onde o JSON está)
    $json_data = file_get_contents('php://input');
    
    // Debug 4: Após pegar o conteúdo da requisição
    echo "Debug 4: Input pego.<br>";
    flush();

    $dados_recebidos = json_decode($json_data, true); // Decodifica o JSON para um array PHP associativo

    // Debug 5: Após decodificar JSON
    echo "Debug 5: JSON decodificado.<br>";
    flush();

    if (empty($dados_recebidos)) {
        throw new Exception('Nenhum dado recebido ou formato inválido.');
    }

    $nome_arquivo_json = 'ciclos_de_vida.json';
    $dados_existentes = [];

    // Debug 6: Antes de verificar arquivo existente
    echo "Debug 6: Antes de verificar arquivo JSON existente.<br>";
    flush();

    // Tenta ler o arquivo JSON existente
    if (file_exists($nome_arquivo_json)) {
        // Debug 7: Arquivo JSON existe
        echo "Debug 7: Arquivo JSON existe.<br>";
        flush();
        $conteudo_json = file_get_contents($nome_arquivo_json);
        $dados_existentes = json_decode($conteudo_json, true);
        if ($dados_existentes === null && json_last_error() !== JSON_ERROR_NONE) {
            $dados_existentes = [];
            // Debug 8: Erro ao decodificar JSON existente
            echo "Debug 8: Erro ao decodificar JSON existente.<br>";
            flush();
        }
    } else {
        // Debug 9: Arquivo JSON não existe
        echo "Debug 9: Arquivo JSON não existe (será criado).<br>";
        flush();
    }

    // Debug 10: Antes de processar dados recebidos
    echo "Debug 10: Antes de processar dados recebidos.<br>";
    flush();

    // Atualiza ou adiciona novos dados
    foreach ($dados_recebidos as $novo_dado) {
        $encontrado = false;
        foreach ($dados_existentes as $key => $dado_existente) {
            if ($dado_existente['referencia'] == $novo_dado['referencia'] &&
                $dado_existente['subgrupo'] == $novo_dado['subgrupo'] &&
                $dado_existente['item'] == $novo_dado['item']) {
                
                $dados_existentes[$key]['ciclo_vida'] = $novo_dado['ciclo_vida'];
                $encontrado = true;
                break;
            }
        }
        if (!$encontrado) {
            $dados_existentes[] = $novo_dado;
        }
    }

    // Debug 11: Antes de salvar no arquivo JSON
    echo "Debug 11: Antes de salvar no arquivo JSON.<br>";
    flush();

    // Salva os dados atualizados de volta no arquivo JSON
    if (file_put_contents($nome_arquivo_json, json_encode($dados_existentes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        $response['status'] = 'success';
        $response['message'] = 'Ciclos de vida salvos com sucesso!';
        // Debug 12: Salvo com sucesso
        echo "Debug 12: Salvo com sucesso.<br>";
        flush();
    } else {
        throw new Exception('Não foi possível escrever no arquivo JSON. Verifique as permissões.');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    // Debug 13: Erro na execução
    echo "Debug 13: Erro na execução: " . $e->getMessage() . "<br>";
    flush();
}

// Debug 14: Antes de retornar JSON
echo "Debug 14: Antes de retornar JSON.<br>";
flush();

// Remova as linhas echo e flush antes de header('Content-Type: application/json');
// e antes do echo json_encode() final em um ambiente de produção.
// Elas são usadas apenas para debug.

// Se você está vendo uma página em branco e seus display_errors estão ON,
// o erro pode estar acontecendo antes de qualquer "echo" ser processado ou
// ser um erro de "Headers already sent" devido a um espaço em branco no início do arquivo,
// ou inclusão de um arquivo com espaços em branco.

// Vamos garantir que a saída final seja JSON.
ob_clean(); // Limpa qualquer saída anterior (incluindo os "Debug X") antes do JSON final.
echo json_encode($response); // Retorna a resposta como JSON
exit; // Garante que nada mais seja impresso

?>