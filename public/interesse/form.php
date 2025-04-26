<?php
session_start();
require_once '../../config/config.php';
include('../menu/menu.php');

$isEdit = false;
$id = null;
$nome = '';
$telefone = '';
$tipo_id = '';
$min_preco = '';
$max_preco = '';
$num_quartos = '';
$bairros_selecionados = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    $isEdit = true;

    $sql = "SELECT * FROM interesses WHERE id = :id";
    $db = $pdo->prepare($sql);
    $db->execute([':id' => $id]);
    $interesse = $db->fetch(PDO::FETCH_ASSOC);

    if ($interesse) {
        $nome = $interesse['nome'];
        $telefone = $interesse['telefone'];
        $tipo_id = $interesse['tipo_id'];
        $min_preco = $interesse['min_preco'];
        $max_preco = $interesse['max_preco'];
        $num_quartos = $interesse['num_quartos'];

        $sql = "SELECT bairro_id FROM interesses_bairros WHERE interesse_id = :id";
        $db = $pdo->prepare($sql);
        $db->execute([':id' => $id]);
        $bairros_selecionados = $db->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $_SESSION['mensagem'] = "Interesse não encontrado.";
        header('Location: listar.php');
        exit;
    }
}

$tipos = $pdo->query("SELECT id, nome FROM tipos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$bairros = $pdo->query("SELECT id, nome FROM bairros ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $tipo_id = $_POST['tipo_id'];
    $min_preco = $_POST['min_preco'];
    $max_preco = $_POST['max_preco'];
    $num_quartos = $_POST['num_quartos'];
    $bairros_selecionados = $_POST['bairros'] ?? [];

    $min_preco = str_replace('.', '', $min_preco);
    $min_preco = str_replace(',', '.', $min_preco);
    $max_preco = str_replace('.', '', $max_preco);
    $max_preco = str_replace(',', '.', $max_preco);

    if (!is_numeric($min_preco) || !is_numeric($max_preco) || !is_numeric($num_quartos)) {
        $error = "Por favor, insira valores válidos nos campos de número.";
    } elseif ($min_preco < 1 && $max_preco < 1) {
        $error = "Por favor, preencha pelo menos um dos campos de preço.";
    } elseif (!empty($nome) && !empty($telefone) && !empty($tipo_id)) {
        if ($isEdit) {
            $sql = "UPDATE interesses SET nome = :nome, telefone = :telefone, tipo_id = :tipo_id, 
                    min_preco = :min_preco, max_preco = :max_preco, num_quartos = :num_quartos WHERE id = :id";
            $db = $pdo->prepare($sql);
            $db->execute([
                ':nome' => $nome,
                ':telefone' => $telefone,
                ':tipo_id' => $tipo_id,
                ':min_preco' => $min_preco,
                ':max_preco' => $max_preco,
                ':num_quartos' => $num_quartos,
                ':id' => $id
            ]);

            $pdo->prepare("DELETE FROM interesses_bairros WHERE interesse_id = :id")->execute([':id' => $id]);
            foreach ($bairros_selecionados as $bairro_id) {
                $pdo->prepare("INSERT INTO interesses_bairros (interesse_id, bairro_id) VALUES (:interesse_id, :bairro_id)")
                    ->execute([':interesse_id' => $id, ':bairro_id' => $bairro_id]);
            }

            $_SESSION['mensagem'] = "Interesse atualizado com sucesso!";
            header('Location: listar.php');
        } else {
            $sql = "INSERT INTO interesses (nome, telefone, tipo_id, min_preco, max_preco, num_quartos) 
                    VALUES (:nome, :telefone, :tipo_id, :min_preco, :max_preco, :num_quartos)";
            $db = $pdo->prepare($sql);
            $db->execute([
                ':nome' => $nome,
                ':telefone' => $telefone,
                ':tipo_id' => $tipo_id,
                ':min_preco' => $min_preco,
                ':max_preco' => $max_preco,
                ':num_quartos' => $num_quartos
            ]);

            $id = $pdo->lastInsertId();

            foreach ($bairros_selecionados as $bairro_id) {
                $pdo->prepare("INSERT INTO interesses_bairros (interesse_id, bairro_id) VALUES (:interesse_id, :bairro_id)")
                    ->execute([':interesse_id' => $id, ':bairro_id' => $bairro_id]);
            }
            
            header('Location: ../recomendacao/recomendacoes.php?id=' . $id);
        }
        exit;
    } else {
        $error = "Por favor, preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title><?= $isEdit ? 'Editar Interesse' : 'Criar Interesse' ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/main.css" rel="stylesheet">
        
        <!-- Styles para multiselect -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    
    </head>
    <body>

        <script src="../js/main.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <div class="container-main">
            <div class="container my-5">
                <h2 class="mb-4"><?= $isEdit ? 'Editar Interesse' : 'Criar Interesse' ?></h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($nome) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($telefone) ?>" required>
                        </div>
                    </div>
                    <div class="row">
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
                        <div class="col-md-6 mb-3">
                            <label for="num_quartos" class="form-label">Número de Quartos</label>
                            <input type="number" class="form-control" id="num_quartos" name="num_quartos" value="<?= htmlspecialchars($num_quartos) ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="min_preco" class="form-label">Preço Mínimo</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control" id="min_preco" name="min_preco" 
                                       value="<?= htmlspecialchars(number_format((float) $min_preco, 2, ',', '.')) ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="max_preco" class="form-label">Preço Máximo</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control" id="max_preco" name="max_preco" 
                                       value="<?= htmlspecialchars(number_format((float) $max_preco, 2, ',', '.')) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bairros" class="form-label">Bairros</label>
                            <select class="form-select" id="bairros" name="bairros[]" data-placeholder="Selecione os bairros" multiple>
                                <?php foreach ($bairros as $bairro): ?>
                                    <option value="<?= $bairro['id'] ?>" <?= in_array($bairro['id'], $bairros_selecionados) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($bairro['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Salvar Alterações' : 'Criar Interesse' ?></button>
                            <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#bairros').select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: 'Selecione os bairros',
            closeOnSelect: false,
            allowClear: true,
            dropdownParent: $('.container'),
            dropdownAutoWidth: true,
        });

        const camposNumericos = ['#telefone', '#num_quartos', '#min_preco', '#max_preco'];
        camposNumericos.forEach(function (nome) {
            const campo = document.querySelector(nome);

            if (nome != "#telefone") {
                campo.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9.,]/g, '');
                });
            } else {
                campo.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
        });
    });

    const minPrecoInput = document.getElementById('min_preco');
    const maxPrecoInput = document.getElementById('max_preco');

    minPrecoInput.addEventListener('input', correcaoInputMonetario);
    minPrecoInput.addEventListener('blur', correcaoBlurInputMonetario);
    maxPrecoInput.addEventListener('input', correcaoInputMonetario);
    maxPrecoInput.addEventListener('blur', correcaoBlurInputMonetario);
</script>