<?php
$pdo = new PDO('sqlite:' . __DIR__ . '/../db/banco.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);