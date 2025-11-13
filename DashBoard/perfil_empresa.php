<?php
header('Content-Type: application/json; charset=utf-8');

// Modo DEBUG (true -> inclui mensagens detalhadas). Em produção coloque false.
$DEBUG = true;

try {
    mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "pecaaq";

    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");

    // Se você usa sessão para identificar o usuário, descomente:
    session_start();
    $id_usuario = $_SESSION['id_usuario'] ?? 0;
    if ($id_usuario === 0) {
        // Se não usa sessão, remova esse bloco e teste com um id fixo para debug:
        // $id_usuario = 1;
        echo json_encode(['ok'=>false,'msg'=>'Nenhum usuário logado (session vazia).'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $conn->prepare("SELECT id_usuario, nome_razao_social, email, telefone, documento FROM usuarios WHERE id_usuario = ? LIMIT 1");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(['ok'=>false,'msg'=>'Usuário não encontrado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $row = $res->fetch_assoc();
    echo json_encode(array_merge(['ok'=>true], $row), JSON_UNESCAPED_UNICODE);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Em DEV mostre detalhe, em PROD envie mensagem genérica
    $msg = $DEBUG ? $e->getMessage() : 'Erro de conexão com o banco';
    echo json_encode(['ok'=>false,'msg'=> $msg], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
