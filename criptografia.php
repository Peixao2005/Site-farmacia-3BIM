<?php
$textoOriginal = "Farmácia Valeu Fera";
$resultados = [];

$resultados['MD5'] = md5($textoOriginal);


$resultados['SHA-1'] = sha1($textoOriginal);

$resultados['SHA-256 (hash())'] = hash('sha256', $textoOriginal);

$resultados['password_hash() - bcrypt'] = password_hash($textoOriginal, PASSWORD_BCRYPT);

$chave = "chave-secreta-da-farmacia-2026!!";
$metodo = "AES-256-CBC";
$iv = openssl_cipher_iv_length($metodo);
$ivBytes = str_repeat("0", $iv); 
$criptografado = openssl_encrypt($textoOriginal, $metodo, $chave, 0, $ivBytes);
$descriptografado = openssl_decrypt($criptografado, $metodo, $chave, 0, $ivBytes);
$resultados['AES-256-CBC (openssl_encrypt) - criptografado'] = $criptografado;
$resultados['AES-256-CBC (openssl_decrypt) - texto restaurado'] = $descriptografado;

$resultados['Base64 (codificação, não é criptografia)'] = base64_encode($textoOriginal);

$verificacao = password_verify($textoOriginal, $resultados['password_hash() - bcrypt']) ? "Válida" : "Inválida";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Criptografia no PHP</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1> Tipos de Criptografia no PHP</h1>
    <nav><a href="index.php">Voltar ao site</a></nav>
</header>
<div class="container">
    <p>Texto original usado nos exemplos: <strong><?= htmlspecialchars($textoOriginal) ?></strong></p>
    <table>
        <tr><th>Tipo</th><th>Resultado</th></tr>
        <?php foreach ($resultados as $tipo => $valor): ?>
        <tr>
            <td><?= htmlspecialchars($tipo) ?></td>
            <td style="word-break: break-all;"><?= htmlspecialchars($valor) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr><td>Verificação com password_verify()</td><td><?= $verificacao ?></td></tr>
    </table>

    <h3>Explicação rápida</h3>
    <ul>
        <li><strong>MD5 / SHA-1</strong>: hashes antigos, rápidos mas considerados fracos para segurança.</li>
        <li><strong>SHA-256</strong>: hash mais forte, usado em integridade de dados.</li>
        <li><strong>password_hash() (bcrypt)</strong>: ideal para senhas, pois usa "salt" automático e é lento de propósito contra ataques de força bruta.</li>
        <li><strong>AES-256-CBC (openssl_encrypt/decrypt)</strong>: criptografia simétrica reversível, usada para dados que precisam ser recuperados depois (ex: dados sensíveis do cliente).</li>
        <li><strong>Base64</strong>: apenas codificação para transporte de dados binários em texto, não protege informação.</li>
    </ul>
</div>
</body>
</html>
