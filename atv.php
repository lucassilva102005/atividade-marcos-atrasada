<?php
session_start();

if (!isset($_SESSION['saldo'])) {
    $_SESSION['saldo'] = 0.0;
    $_SESSION['extrato'] = [];
}

$mensagem = "";
$tipo_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acao = $_POST['acao'];
    $valor = floatval($_POST['valor'] ?? 0);

    if ($acao == 'depositar') {
        if ($valor > 0) {
            $_SESSION['saldo'] += $valor;
            $_SESSION['extrato'][] = "Depósito: + R$ " . number_format($valor, 2, ',', '.');
            $mensagem = "Depósito de R$ " . number_format($valor, 2, ',', '.') . " realizado!";
            $tipo_msg = "sucesso";
        }
    } 
    elseif ($acao == 'sacar') {
        if ($valor > $_SESSION['saldo']) {
            $mensagem = "Erro: Saldo insuficiente!";
            $tipo_msg = "erro";
        } elseif ($valor <= 0) {
            $mensagem = "Erro: Valor inválido!";
            $tipo_msg = "erro";
        } else {
            $_SESSION['saldo'] -= $valor;
            $_SESSION['extrato'][] = "Saque: - R$ " . number_format($valor, 2, ',', '.');
            $mensagem = "Saque de R$ " . number_format($valor, 2, ',', '.') . " realizado!";
            $tipo_msg = "sucesso";
        }
    } 
    elseif ($acao == 'limpar') {
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Caixa Eletrônico PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🏧 Banco Digital PHP</h1>
        
        <div class="saldo-card">
            <p>Saldo Atual</p>
            <h2>R$ <?php echo number_format($_SESSION['saldo'], 2, ',', '.'); ?></h2>
        </div>

        <?php if ($mensagem): ?>
            <div class="alerta <?php echo $tipo_msg; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="number" name="valor" step="0.01" placeholder="Valor R$" required>
            <div class="botoes">
                <button type="submit" name="acao" value="depositar" class="btn-deposito">Depositar</button>
                <button type="submit" name="acao" value="sacar" class="btn-saque">Sacar</button>
            </div>
            <button type="submit" name="acao" value="limpar" class="btn-limpar">Resetar Conta</button>
        </form>

        <div class="extrato">
            <h3>📜 Extrato</h3>
            <ul>
                <?php foreach (array_reverse($_SESSION['extrato']) as $item): ?>
                    <li><?php echo $item; ?></li>
                <?php endforeach; ?>
                <?php if (empty($_SESSION['extrato']))

?>php