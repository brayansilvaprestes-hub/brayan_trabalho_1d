<?php
// Configurações de acesso ao banco de dados
$host = 'localhost';     // Endereço do banco (seu computador)
$banco = 'biblioteca';   // Nome do banco de dados
$user = 'root';         // Usuário padrão do XAMPP
$senha = '';            // Senha padrão (vazia no XAMPP)

try {
    // Tenta conectar ao banco de dados usando a tecnologia PDO
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $user, $senha);
    
    // Ativa os alertas de erro para ajudar você a achar bugs no código
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Faz os resultados do banco virem organizados pelo nome das colunas
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $erro) {
    // Se a conexão falhar, para o site e mostra esta mensagem de ajuda
    die('Não consegui conectar ao banco: ' . $erro->getMessage() . '<br>Confira se o MySQL está ligado no painel do XAMPP e se o banco.sql foi importado.');
}

// Função de segurança para proteger o site contra invasões (XSS)
function e($texto) {
    // Transforma códigos perigosos em texto comum antes de mostrar na tela
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}
?>
