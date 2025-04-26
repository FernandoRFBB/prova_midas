<?php
session_start();
require_once '../../config/config.php';
include('../menu/menu.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Interesse inválido.');
}

$interesse_id = (int) $_GET['id'];

$sql_interesse = "
    SELECT i.nome, i.telefone, i.min_preco, i.max_preco, i.num_quartos, t.nome AS tipo, i.tipo_id
    FROM interesses i
    JOIN tipos t ON i.tipo_id = t.id
    WHERE i.id = :id
";
$db = $pdo->prepare($sql_interesse);
$db->execute([':id' => $interesse_id]);
$interesse = $db->fetch(PDO::FETCH_ASSOC);

if (!$interesse) {
    die('Interesse não encontrado.');
}

// Busca os bairros de interesse
$sql_bairros = "
    SELECT b.id, b.nome
    FROM interesses_bairros ib
    JOIN bairros b ON ib.bairro_id = b.id
    WHERE ib.interesse_id = :id
";
$db = $pdo->prepare($sql_bairros);
$db->execute([':id' => $interesse_id]);
$bairros = $db->fetchAll(PDO::FETCH_ASSOC);
$bairro_ids = array_column($bairros, 'id');

// Busca os imóveis de interesse
$sql_imoveis = "
    SELECT i.titulo, i.descricao, t.nome AS tipo, b.nome AS bairro, i.quartos, i.preco,
           CASE
               WHEN i.bairro_id IN (" . implode(',', $bairro_ids) . ") THEN 1
               ELSE 0
           END AS match_bairro,
           CASE
               WHEN i.tipo_id = :tipo_id THEN 1
               ELSE 0
           END AS match_tipo,
           CASE
               WHEN i.quartos >= :num_quartos THEN 1
               ELSE 0
           END AS match_quartos,
           CASE
               WHEN i.preco BETWEEN :min_preco AND :max_preco THEN 1
               ELSE 0
           END AS match_preco
    FROM imoveis i
    JOIN tipos t ON i.tipo_id = t.id
    JOIN bairros b ON i.bairro_id = b.id
    WHERE i.bairro_id IN (" . implode(',', $bairro_ids) . ")
       OR i.tipo_id = :tipo_id
       OR i.quartos >= :num_quartos
       OR i.preco BETWEEN :min_preco AND :max_preco
    ORDER BY match_bairro DESC, match_tipo DESC, match_quartos DESC, match_preco DESC
";
$db = $pdo->prepare($sql_imoveis);
$db->execute([
    ':tipo_id' => $interesse['tipo_id'],
    ':num_quartos' => $interesse['num_quartos'],
    ':min_preco' => $interesse['min_preco'],
    ':max_preco' => $interesse['max_preco']
]);
$imoveis = $db->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Midas - Imóveis para <?= htmlspecialchars($interesse['nome']) ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link href="../css/main.css" rel="stylesheet">
    </head>
    <body>

        <div class="container-main">
            <div class="container my-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Imóveis para <?= htmlspecialchars($interesse['nome']) ?></h2>
                    <a href="../interesse/listar.php" class="btn btn-secondary px-4">Voltar</a>
                </div>

                <div class="card card-custom p-4 bg-white">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Tipo</th>
                                <th>Bairro</th>
                                <th>Quartos</th>
                                <th style="width:150px">Preço</th>
                                <th>Critérios Correspondentes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($imoveis as $imovel): ?>
                            <tr>
                                <td><?= htmlspecialchars($imovel['titulo']) ?></td>
                                <td><?= htmlspecialchars($imovel['descricao']) ?></td>
                                <td><?= htmlspecialchars($imovel['tipo']) ?></td>
                                <td><?= htmlspecialchars($imovel['bairro']) ?></td>
                                <td><?= htmlspecialchars($imovel['quartos']) ?></td>
                                <td>R$ <?= number_format($imovel['preco'], 2, ',', '.') ?></td>
                                <td>
                                    <?= $imovel['match_bairro'] ? '<span class="badge bg-success">Bairro</span>' : '' ?>
                                    <?= $imovel['match_tipo'] ? '<span class="badge bg-primary">Tipo</span>' : '' ?>
                                    <?= $imovel['match_quartos'] ? '<span class="badge bg-warning">Quartos</span>' : '' ?>
                                    <?= $imovel['match_preco'] ? '<span class="badge bg-info">Preço</span>' : '' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($imoveis)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Nenhum imóvel encontrado para este interesse.</td>
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