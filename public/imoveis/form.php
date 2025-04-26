<?php
session_start();
require_once '../../config/config.php';
include('../menu/menu.php');

$isEdit = false;
$id = null;
$titulo = '';
$descricao = '';
$tipo_id = '';
$bairro_id = '';
$quartos = '';
$preco = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    $isEdit = true;

    $sql = "SELECT * FROM imoveis WHERE id = :id";
    $db = $pdo->prepare($sql);
    $db->execute([':id' => $id]);
    $imovel = $db->fetch(PDO::FETCH_ASSOC);

    if ($imovel) {
        $titulo = $imovel['titulo'];
        $descricao = $imovel['descricao'];
        $tipo_id = $imovel['tipo_id'];
        $bairro_id = $imovel['bairro_id'];
        $quartos = $imovel['quartos'];
        $preco = $imovel['preco'];
    } else {
        $_SESSION['mensagem'] = "Imóvel não encontrado.";
        header('Location: listar.php');
        exit;
    }
}

$tipos = $pdo->query("SELECT id, nome FROM tipos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$bairros = $pdo->query("SELECT id, nome FROM bairros ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $tipo_id = $_POST['tipo_id'];
    $bairro_id = $_POST['bairro_id'];
    $quartos = $_POST['quartos'];
    $preco = $_POST['preco'];

    $preco = str_replace('.', '', $preco);
    $preco = str_replace(',', '.', $preco);

    if (!empty($titulo) && !empty($tipo_id) && !empty($bairro_id) && $preco > 0) {
        if ($isEdit) {
            $sql = "UPDATE imoveis SET titulo = :titulo, descricao = :descricao, tipo_id = :tipo_id, 
                    bairro_id = :bairro_id, quartos = :quartos, preco = :preco WHERE id = :id";
            $db = $pdo->prepare($sql);
            $db->execute([
                ':titulo' => $titulo,
                ':descricao' => $descricao,
                ':tipo_id' => $tipo_id,
                ':bairro_id' => $bairro_id,
                ':quartos' => $quartos,
                ':preco' => $preco,
                ':id' => $id
            ]);
            $_SESSION['mensagem'] = "Imóvel atualizado com sucesso!";
        } else {
            $sql = "INSERT INTO imoveis (titulo, descricao, tipo_id, bairro_id, quartos, preco) 
                    VALUES (:titulo, :descricao, :tipo_id, :bairro_id, :quartos, :preco)";
            $db = $pdo->prepare($sql);
            $db->execute([
                ':titulo' => $titulo,
                ':descricao' => $descricao,
                ':tipo_id' => $tipo_id,
                ':bairro_id' => $bairro_id,
                ':quartos' => $quartos,
                ':preco' => $preco
            ]);
            $_SESSION['mensagem'] = "Imóvel criado com sucesso!";
        }

        header('Location: listar.php');
        exit;
    } else {
        $error = "Por favor, preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title><?= $isEdit ? 'Editar Imóvel' : 'Criar Imóvel' ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/main.css" rel="stylesheet">
    </head>
    <body>
        <script src="../js/main.js"></script>
        <div class="container-main">
            <div class="container my-5">
                <h2 class="fw-bold mb-4"><?= $isEdit ? 'Editar Imóvel' : 'Criar Novo Imóvel' ?></h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($titulo) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tipo_id" class="form-label">Tipo</label>
                            <select class="form-control" id="tipo_id" name="tipo_id" required>
                                <option value="">Selecione</option>
                                <?php foreach ($tipos as $tipo): ?>
                                    <option value="<?= $tipo['id'] ?>" <?= $tipo_id == $tipo['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bairro_id" class="form-label">Bairro</label>
                            <select class="form-control" id="bairro_id" name="bairro_id" required>
                                <option value="">Selecione</option>
                                <?php foreach ($bairros as $bairro): ?>
                                    <option value="<?= $bairro['id'] ?>" <?= $bairro_id == $bairro['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($bairro['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quartos" class="form-label">Número de Quartos</label>
                            <input type="number" class="form-control" id="quartos" name="quartos" value="<?= htmlspecialchars($quartos) ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="preco" class="form-label">Preço</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control" id="preco" name="preco" 
                                       value="<?= htmlspecialchars(number_format((float) $preco, 2, ',', '.')) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= htmlspecialchars($descricao) ?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-success"><?= $isEdit ? 'Atualizar' : 'Salvar' ?></button>
                            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

<script>
    const precoInput = document.getElementById('preco');

    precoInput.addEventListener('input', correcaoInputMonetario);

    precoInput.addEventListener('blur', correcaoBlurInputMonetario);
</script>