<?php
header('Content-Type: application/json');

// ======================
// Recebe dados do POST
// ======================
$id_usuario = intval($_POST['id_usuario'] ?? 0);
$email      = trim($_POST['email'] ?? '');
$telefone   = trim($_POST['telefone'] ?? '');

// ======================
// Validação básica
// ======================
if ($id_usuario === 0 || empty($email) || empty($telefone)) {
    echo json_encode([
        'ok'  => false,
        'msg' => 'Dados incompletos. Preencha email e telefone corretamente.'
    ]);
    exit;
}

// ======================
// Conexão com o banco
// ======================
$conn = new mysqli("localhost", "root", "", "pecaaq");
if ($conn->connect_error) {
    echo json_encode([
        'ok'  => false,
        'msg' => 'Erro de conexão com o banco de dados.'
    ]);
    exit;
}

// ======================
// Atualiza na tabela usuarios
// ======================
$stmt1 = $conn->prepare("UPDATE usuarios SET email = ?, telefone = ? WHERE id_usuario = ?");
$stmt1->bind_param("ssi", $email, $telefone, $id_usuario);
$ok1 = $stmt1->execute();
$stmt1->close();

// ======================
// Atualiza na tabela fornecedores
// ======================
$stmt2 = $conn->prepare("UPDATE fornecedores SET email_comercial = ?, telefone_comercial = ? WHERE id_usuario_representante = ?");
$stmt2->bind_param("ssi", $email, $telefone, $id_usuario);
$ok2 = $stmt2->execute();
$stmt2->close();

// ======================
// Resultado final
// ======================
if ($ok1 || $ok2) {
    echo json_encode([
        'ok' => true,
        'msg' => 'Perfil atualizado com sucesso!',
        'dados' => [
            'email' => $email,
            'telefone' => $telefone
        ]
    ]);
} else {
    echo json_encode([
        'ok' => false,
        'msg' => 'Erro ao atualizar perfil nas tabelas.'
    ]);
}

$conn->close();
?>
