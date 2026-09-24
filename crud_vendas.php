<?php
require_once "config.php";
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $cliente_id = (int)$_POST['cliente_id'];
    $medicamento_id = (int)$_POST['medicamento_id'];
    $quantidade = (int)$_POST['quantidade'];
    $status = $_POST['status'];

    $stmtPreco = $pdo->prepare("SELECT preco FROM medicamentos WHERE id=?");
    $stmtPreco->execute([$medicamento_id]);
    $preco = $stmtPreco->fetchColumn();
    $valor_total = $preco * $quantidade;

    if ($acao === 'criar') {
        $stmt = $pdo->prepare("INSERT INTO vendas (cliente_id, medicamento_id, quantidade, valor_total, status) VALUES (?,?,?,?,?)");
        $stmt->execute([$cliente_id, $medicamento_id, $quantidade, $valor_total, $status]);
        $msg = "Venda registrada com sucesso!";
    } elseif ($acao === 'editar') {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("UPDATE vendas SET cliente_id=?, medicamento_id=?, quantidade=?, valor_total=?, status=? WHERE id=?");
        $stmt->execute([$cliente_id, $medicamento_id, $quantidade, $valor_total, $status, $id]);
        $msg = "Venda atualizada com sucesso!";
    }
}

if (isset($_GET['excluir'])) {
    $stmt = $pdo->prepare("DELETE FROM vendas WHERE id=?");
    $stmt->execute([(int)$_GET['excluir']]);
    $msg = "Venda excluída.";
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM vendas WHERE id=?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

$clientes = $pdo->query("SELECT id, nome FROM clientes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$medicamentos = $pdo->query("SELECT id, nome, preco FROM medicamentos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

$lista = $pdo->query("
    SELECT v.*, c.nome AS cliente_nome, m.nome AS medicamento_nome
    FROM vendas v
    JOIN clientes c ON c.id = v.cliente_id
    JOIN medicamentos m ON m.id = v.medicamento_id
    ORDER BY v.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>CRUD Vendas</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<header><h1>🧾 Admin - Vendas</h1><nav><a href="index.php">Voltar ao site</a></nav></header>
<div class="container">
    <?php if ($msg): ?><div class="alert alert-ok"><?= $msg ?></div><?php endif; ?>

    <form class="form-box" method="post">
        <input type="hidden" name="acao" value="<?= $editando ? 'editar' : 'criar' ?>">
        <?php if ($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>
        <h3><?= $editando ? 'Editar venda' : 'Nova venda' ?></h3>

        <label>Cliente</label>
        <select name="cliente_id" required>
            <?php foreach ($clientes as $c): ?>
            <option value="<?= $c['id'] ?>" <?= (isset($editando) && $editando['cliente_id']==$c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Medicamento</label>
        <select name="medicamento_id" required>
            <?php foreach ($medicamentos as $m): ?>
            <option value="<?= $m['id'] ?>" <?= (isset($editando) && $editando['medicamento_id']==$m['id']) ? 'selected' : '' ?>><?= htmlspecialchars($m['nome']) ?> (R$ <?= number_format($m['preco'],2,',','.') ?>)</option>
            <?php endforeach; ?>
        </select>

        <label>Quantidade</label>
        <input type="number" name="quantidade" min="1" required value="<?= htmlspecialchars($editando['quantidade'] ?? 1) ?>">

        <label>Status</label>
        <select name="status" required>
            <?php foreach (['Pendente','Pago','Cancelado','Entregue'] as $st): ?>
            <option value="<?= $st ?>" <?= (isset($editando) && $editando['status']==$st) ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>

        <button class="btn btn-add" type="submit" style="margin-top:14px;"><?= $editando ? 'Salvar alterações' : 'Registrar venda' ?></button>
        <?php if ($editando): ?><a href="crud_vendas.php" class="btn btn-del">Cancelar</a><?php endif; ?>
    </form>

    <h3>Vendas registradas</h3>
    <table>
        <tr><th>ID</th><th>Cliente</th><th>Medicamento</th><th>Qtd</th><th>Total</th><th>Status</th><th>Data</th><th>Ações</th></tr>
        <?php foreach ($lista as $v): ?>
        <tr>
            <td><?= $v['id'] ?></td>
            <td><?= htmlspecialchars($v['cliente_nome']) ?></td>
            <td><?= htmlspecialchars($v['medicamento_nome']) ?></td>
            <td><?= $v['quantidade'] ?></td>
            <td>R$ <?= number_format($v['valor_total'],2,',','.') ?></td>
            <td><?= $v['status'] ?></td>
            <td><?= $v['data_venda'] ?></td>
            <td>
                <a class="btn btn-edit" href="?editar=<?= $v['id'] ?>">Editar</a>
                <a class="btn btn-del" href="?excluir=<?= $v['id'] ?>" onclick="return confirm('Excluir esta venda?');">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
