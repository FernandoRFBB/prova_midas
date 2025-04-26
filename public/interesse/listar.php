<?php

session_start();
require_once '../../config/config.php';
include('../menu/menu.php');

$sql = "
    SELECT 
        i.id, 
        i.nome, 
        i.telefone, 
        i.min_preco, 
        i.max_preco, 
        i.num_quartos, 
        t.nome AS tipo, 
        b.nome AS bairro
    FROM interesses i
    JOIN tipos t ON i.tipo_id = t.id
    LEFT JOIN interesses_bairros ib ON ib.interesse_id = i.id
    LEFT JOIN bairros b ON ib.bairro_id = b.id
    ORDER BY i.id
";

$db = $pdo->query($sql);
$linhas = $db->fetchAll(PDO::FETCH_ASSOC);

$interesses = [];

foreach ($linhas as $interesse) {
    $id = $interesse['id'];
    if (!isset($interesses[$id])) {
        $interesses[$id] = [
            'id' => $id,
            'nome' => $interesse['nome'],
            'telefone' => $interesse['telefone'],
            'min_preco' => $interesse['min_preco'],
            'max_preco' => $interesse['max_preco'],
            'num_quartos' => $interesse['num_quartos'],
            'tipo' => $interesse['tipo'],
            'bairros' => []
        ];
    }

    if ($interesse['bairro']) {
        $interesses[$id]['bairros'][] = $interesse['bairro'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Midas - Interesses</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link href="../css/main.css" rel="stylesheet">
        <style>
            .table .text-center .btn {
                white-space: nowrap;
                margin-right: 5px;
            }
        </style>
    </head>
    <body>

        <div class="container-main">
            <div class="container my-5">
                <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="alert alert-info">
                    <?= htmlspecialchars($_SESSION['mensagem']) ?>
                </div>
                <?php unset($_SESSION['mensagem']); ?>
                <?php endif; ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Interesses dos Clientes</h2>
                    <a href="form.php" class="btn btn-success px-4">+ Novo Interesse</a>
                </div>
                <div class="card card-custom p-4 bg-white">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>Tipo</th>
                                <th>Quartos</th>
                                <th>Faixa de Preço</th>
                                <th>Bairros</th>
                                <th class="text-center" style="width: 200px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($interesses as $i): ?>
                            <tr>
                                <td><?= htmlspecialchars($i['nome']) ?></td>
                                <td><?= htmlspecialchars($i['telefone']) ?></td>
                                <td><?= htmlspecialchars($i['tipo']) ?></td>
                                <td><?= htmlspecialchars($i['num_quartos']) ?></td>
                                <td>
                                    R$ <?= number_format($i['min_preco'], 0, ',', '.') ?> - 
                                    R$ <?= number_format($i['max_preco'], 0, ',', '.') ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= implode('</span> <span class="badge bg-secondary">', array_map('htmlspecialchars', $i['bairros'])) ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="../recomendacao/recomendacoes.php?id=<?= $i['id'] ?>" class="btn btn-sm btn-outline-info" title="Ver Imóveis">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="form.php?id=<?= $i['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                    <a href="excluir.php?id=<?= $i['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($interesses)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Nenhum interesse encontrado.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>