<?php

session_start();
require_once '../../config/config.php';
include('../menu/menu.php');

$sql = "
    SELECT 
        i.id, 
        i.titulo, 
        i.descricao, 
        i.preco, 
        i.quartos, 
        t.nome AS tipo, 
        b.nome AS bairro
    FROM imoveis i
    JOIN tipos t ON i.tipo_id = t.id
    JOIN bairros b ON i.bairro_id = b.id
    ORDER BY i.titulo
";

$db = $pdo->query($sql);
$imoveis = $db->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Midas - Imóveis</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/main.css" rel="stylesheet">
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
                    <h2 class="fw-bold">Imóveis</h2>
                    <a href="form.php" class="btn btn-success px-4">+ Novo Imóvel</a>
                </div>

                <div class="card card-custom p-4 bg-white">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Bairro</th>
                                <th>Preço</th>
                                <th>Quartos</th>
                                <th>Tipo</th>
                                <th class="text-center" style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($imoveis as $imovel): ?>
                            <tr>
                                <td><?= htmlspecialchars($imovel['titulo']) ?></td>
                                <td><?= htmlspecialchars($imovel['descricao']) ?></td>
                                <td><?= htmlspecialchars($imovel['bairro']) ?></td>
                                <td>R$ <?= number_format((float) $imovel['preco'], 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars($imovel['quartos']) ?></td>
                                <td><?= htmlspecialchars($imovel['tipo']) ?></td>
                                <td class="text-center">
                                    <a href="form.php?id=<?= $imovel['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                    <a href="excluir.php?id=<?= $imovel['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($imoveis)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Nenhum imóvel encontrado.</td>
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