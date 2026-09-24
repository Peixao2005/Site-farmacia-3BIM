<?php
require_once "config.php";
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];

    if ($acao === 'criar') {
        $senha_hash = password_hash($_POST['senha'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO clientes (nome, cpf, email, telefone, endereco, senha_hash) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$nome, $cpf, $email, $telefone, $endereco, $senha_hash]);
        $msg = "Cliente cadastrado com sucesso!";
    } elseif ($acao === 'editar') {
        $id = (int)$_POST['id'];
        if (!empty($_POST['senha'])) {
            $senha_hash = password_hash($_POST['senha'], PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE clientes SET nome=?, cpf=?, email=?, telefone=?, endereco=?, senha_hash=? WHERE id=?");
            $stmt->execute([$nome, $cpf, $email, $telefone, $endereco, $senha_hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE clientes SET nome=?, cpf=?, email=?, telefone=?, endereco=? WHERE id=?");
            $stmt->execute([$nome, $cpf, $email, $telefone, $endereco, $id]);
        }
        $msg = "Cliente atualizado com sucesso!";
    }
}

if (isset($_GET['excluir'])) {
    $stmt = $pdo->prepare("DELETE FROM clientes WHERE id=?");
    $stmt->execute([(int)$_GET['excluir']]);
    $msg = "Cliente excluído.";
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id=?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

$lista = $pdo->query("SELECT * FROM clientes ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>CRUD Clientes</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<header><h1>👤 Admin - Clientes</h1><nav><a href="index.php">Voltar ao site</a></nav></header>
<div class="container">
    <?php if ($msg): ?><div class="alert alert-ok"><?= $msg ?></div><?php endif; ?>

    <form class="form-box" method="post">
        <input type="hidden" name="acao" value="<?= $editando ? 'editar' : 'criar' ?>">
        <?php if ($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>
        <h3><?= $editando ? 'Editar cliente' : 'Novo cliente' ?></h3>
        <label>Nome</label>
        <input type="text" name="nome" required value="<?= htmlspecialchars($editando['nome'] ?? '') ?>">
        <label>CPF</label>
        <input type="text" name="cpf" required value="<?= htmlspecialchars($editando['cpf'] ?? '') ?>">
        <label>E-mail</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($editando['email'] ?? '') ?>">
        <label>Telefone</label>
        <input type="text" name="telefone" value="<?= htmlspecialchars($editando['telefone'] ?? '') ?>">
        <label>Endereço</label>
        <input type="text" name="endereco" value="<?= htmlspecialchars($editando['endereco'] ?? '') ?>">
        <label>Senha <?= $editando ? '(deixe vazio para não alterar)' : '' ?></label>
        <input type="password" name="senha" <?= $editando ? '' : 'required' ?>>
        <button class="btn btn-add" type="submit" style="margin-top:14px;"><?= $editando ? 'Salvar alterações' : 'Cadastrar' ?></button>
        <?php if ($editando): ?><a href="crud_clientes.php" class="btn btn-del">Cancelar</a><?php endif; ?>
    </form>

    <h3>Clientes cadastrados</h3>
    <table>
        <tr><th>ID</th><th>Nome</th><th>CPF</th><th>E-mail</th><th>Telefone</th><th>Ações</th></tr>
        <?php foreach ($lista as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['nome']) ?></td>
            <td><?= htmlspecialchars($c['cpf']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['telefone']) ?></td>
            <td>
                <a class="btn btn-edit" href="?editar=<?= $c['id'] ?>">Editar</a>
                <a class="btn btn-del" href="?excluir=<?= $c['id'] ?>" onclick="return confirm('Excluir este cliente?');">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
