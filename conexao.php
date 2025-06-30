<?php
// conexao.php (ou o nome do seu arquivo de conexão)

$user = 'user'; // Substitua pelo seu usuário Oracle
$password = 'senha'; // Substitua pela sua senha Oracle
$dsn = 'dns'; // Ex: 'localhost/XE' ou '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=XE)))'

try {
    $conn = oci_connect($user, $password, $dsn);
    if (!$conn) {
        $e = oci_error();
        throw new Exception("Erro de conexão com o Oracle: " . $e['message']);
    }
    // echo "Conexão com o Oracle estabelecida com sucesso!"; // Para testar a conexão
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
    // O ideal é logar o erro em produção e não mostrá-lo ao usuário final
    exit; // Interrompe a execução do script se houver erro na conexão
}

?>