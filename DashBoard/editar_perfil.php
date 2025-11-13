<?php
header('Content-Type: application/json');

// ======================
// Recebe dados do POST
// ======================
$id_usuario = intval($_POST['id_usuario'] ?? 0);
$email      = trim($_POST['email'] ?? '');
$telefone   = trim($_POST['telefone'] ?? '');

// Validação básica
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
// Atualiza email e telefone
// ======================
$stmt = $conn->prepare("UPDATE usuarios SET email=?, telefone=? WHERE id_usuario=?");
$stmt->bind_param("ssi", $email, $telefone, $id_usuario);

if ($stmt->execute()) {
    echo json_encode([
        'ok'  => true,
        'msg' => 'Perfil atualizado com sucesso!',
        'dados' => [
            'email' => $email,
            'telefone' => $telefone
        ]
    ]);
} else {
    echo json_encode([
        'ok'  => false,
        'msg' => 'Erro ao atualizar perfil.'
    ]);
}

$stmt->close();
$conn->close();
?>
