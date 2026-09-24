<?php
require_once "config.php";
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'criar' || $acao === 'editar') {
        $nome = $_POST['nome'];
        $categoria = $_POST['categoria'];
        $fabricante = $_POST['fabricante'];
        $descricao = $_POST['descricao'];
        $preco = str_replace(',', '.', $_POST['preco']);
        $estoque = (int)$_POST['estoque'];
        $receita = isset($_POST['precisa_receita']) ? 1 : 0;

        if ($acao === 'criar') {
            $stmt = $pdo->prepare("INSERT INTO medicamentos (nome, categoria, fabricante, descricao, preco, estoque, precisa_receita) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$nome, $categoria, $fabricante, $descricao, $preco, $estoque, $receita]);
            $msg = "Medicamento cadastrado com sucesso!";
        } else {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE medicamentos SET nome=?, categoria=?, fabricante=?, descricao=?, preco=?, estoque=?, precisa_receita=? WHERE id=?");
            $stmt->execute([$nome, $categoria, $fabricante, $descricao, $preco, $estoque, $receita, $id]);
            $msg = "Medicamento atualizado com sucesso!";
        }
    }
}

if (isset($_GET['excluir'])) {
    $stmt = $pdo->prepare("DELETE FROM medicamentos WHERE id=?");
    $stmt->execute([(int)$_GET['excluir']]);
    $msg = "Medicamento excluído.";
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM medicamentos WHERE id=?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

$lista = $pdo->query("SELECT * FROM medicamentos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>CRUD Medicamentos</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<header><h1>💊 Admin - Medicamentos</h1><nav><a href="index.php">Voltar ao site</a></nav></header>
<div class="container">
    <?php if ($msg): ?><div class="alert alert-ok"><?= $msg ?></div><?php endif; ?>

    <form class="form-box" method="post">
        <input type="hidden" name="acao" value="<?= $editando ? 'editar' : 'criar' ?>">
        <?php if ($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>
        <h3><?= $editando ? 'Editar medicamento' : 'Novo medicamento' ?></h3>
        <label>Nome</label>
        <input type="text" name="nome" required value="<?= htmlspecialchars($editando['nome'] ?? '') ?>">
        <label>Categoria</label>
        <input type="text" name="categoria" required value="<?= htmlspecialchars($editando['categoria'] ?? '') ?>">
        <label>Fabricante</label>
        <input type="text" name="fabricante" required value="<?= htmlspecialchars($editando['fabricante'] ?? '') ?>">
        <label>Descrição</label>
        <textarea name="descricao"><?= htmlspecialchars($editando['descricao'] ?? '') ?></textarea>
        <label>Preço (R$)</label>
        <input type="text" name="preco" required value="<?= htmlspecialchars($editando['preco'] ?? '') ?>">
        <label>Estoque</label>
        <input type="number" name="estoque" required value="<?= htmlspecialchars($editando['estoque'] ?? 0) ?>">
        <label><input type="checkbox" name="precisa_receita" style="width:auto; display:inline;" <?= (!empty($editando['precisa_receita'])) ? 'checked' : '' ?>> Requer receita médica</label>
        <button class="btn btn-add" type="submit" style="margin-top:14px;"><?= $editando ? 'Salvar alterações' : 'Cadastrar' ?></button>
        <?php if ($editando): ?><a href="crud_medicamentos.php" class="btn btn-del">Cancelar</a><?php endif; ?>
    </form>

    <h3>Medicamentos cadastrados</h3>
    <table>
        <tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th>Receita</th><th>Ações</th></tr>
        <?php foreach ($lista as $m): ?>
        <tr>
            <td><?= $m['id'] ?></td>
            <td><?= htmlspecialchars($m['nome']) ?></td>
            <td><?= htmlspecialchars($m['categoria']) ?></td>
            <td>R$ <?= number_format($m['preco'],2,',','.') ?></td>
            <td><?= $m['estoque'] ?></td>
            <td><?= $m['precisa_receita'] ? 'Sim' : 'Não' ?></td>
            <td>
                <a class="btn btn-edit" href="?editar=<?= $m['id'] ?>">Editar</a>
                <a class="btn btn-del" href="?excluir=<?= $m['id'] ?>" onclick="return confirm('Excluir este medicamento?');">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
