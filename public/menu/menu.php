<?php
    session_start();
    if (!isset($_SESSION['usuario'])) {
        header('Location: ../index.php');
        exit;
    }
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-controls="menuLateral" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="#">Midas</a>        
    </div>
</nav>

<div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="menuLateral" aria-labelledby="menuLateralLabel" style="width: 250px;" data-bs-backdrop="false">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="menuLateralLabel">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <li class="nav-item">
                <a class="nav-link" href="../imoveis/listar.php">🏢 Imóveis</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../interesse/listar.php">👥 Interesses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">⚙️ Configurações</a>
            </li>
        </ul>
        <div class="mt-4 text-center">
            <a href="../logout.php" class="btn btn-danger px-4">🚪 Sair</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
