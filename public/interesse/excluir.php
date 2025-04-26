<?php

session_start();
require_once '../../config/config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    $sql = "DELETE FROM interesses WHERE id = :id";
    $db = $pdo->prepare($sql);

    if ($db->execute([':id' => $id])) {
        $_SESSION['mensagem'] = "Interesse excluído com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao excluir o interesse.";
    }
} else {
    $_SESSION['mensagem'] = "ID inválido.";
}

header('Location: listar.php');
exit;