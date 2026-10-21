<?php
// Inclui o arquivo que faz a conexão com o banco de dados
require 'conexao.php';

// Se o usuário tentar acessar a página sem ser por um formulário (POST), joga ele de volta para o index
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

try {
    // Tenta deletar o livro do banco de dados usando o ID enviado
    $consulta = $pdo->prepare("DELETE FROM livros WHERE id = :id");
    $consulta->execute([':id' => (int)$_POST['id']]);
    $msg = 'Livro excluído.';
    
} catch (PDOException $erro) {
    // Se o banco travar a exclusão (ex: o livro está emprestado), captura o erro e avisa o usuário
    $msg = 'Este livro tem empréstimos registrados e não pode ser excluído.';
}

// Redireciona para a página inicial exibindo a mensagem do resultado
header('Location: index.php?msg=' . urlencode($msg));
exit;
