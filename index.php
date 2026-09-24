<?php
require_once "config.php";
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : "";

if ($busca !== "") {
    $stmt = $pdo->prepare("SELECT * FROM medicamentos WHERE nome LIKE :b OR categoria LIKE :b ORDER BY nome");
    $stmt->execute(['b' => "%$busca%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM medicamentos ORDER BY nome");
}
$medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Farmácia Valeu Fera</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1> Farmácia Valeu Fera </h1>
    <nav>
        <a href="index.php">Início</a>
        <a href="crud_medicamentos.php">Admin: Medicamentos</a>
        <a href="crud_clientes.php">Admin: Clientes</a>
        <a href="crud_vendas.php">Admin: Vendas</a>
        <a href="criptografia.php">Criptografia PHP</a>
    </nav>
</header>

<div class="container">
    <form method="get" style="margin-bottom:20px;">
        <input type="text" name="busca" placeholder="Buscar medicamento ou categoria..."
               value="<?= htmlspecialchars($busca) ?>"
               style="width:100%; padding:10px; border-radius:6px; border:1px solid #ccc;">
    </form>

    <h2>Nossos produtos</h2>
    <div class="grid">
        <?php if (count($medicamentos) === 0): ?>
            <p>Nenhum medicamento encontrado.</p>
        <?php endif; ?>

        <?php foreach ($medicamentos as $m): ?>
            <div class="card">
                <span class="badge <?= $m['precisa_receita'] ? 'receita' : '' ?>">
                    <?= $m['precisa_receita'] ? 'Requer receita' : 'Venda livre' ?>
                </span>
                <h3><?= htmlspecialchars($m['nome']) ?></h3>
                <div class="estoque"><?= htmlspecialchars($m['categoria']) ?> · <?= htmlspecialchars($m['fabricante']) ?></div>
                <p><?= htmlspecialchars($m['descricao']) ?></p>
                <div class="preco">R$ <?= number_format($m['preco'], 2, ',', '.') ?></div>
                <div class="estoque">Em estoque: <?= (int)$m['estoque'] ?> unidades</div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
