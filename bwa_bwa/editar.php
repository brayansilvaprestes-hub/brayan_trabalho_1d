<?php
// Puxa o arquivo que conecta com o banco de dados
require 'conexao.php';

// Pega o ID do livro enviado pela URL ou pelo formulário (garante que seja um número)
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Verifica se o usuário clicou no botão para salvar as alterações (envio POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cria o comando SQL para atualizar os dados do livro no banco
    $sql = "UPDATE livros
            SET titulo = :titulo, autor = :autor, ano = :ano, genero = :genero, quantidade = :quantidade
            WHERE id = :id";
    
    // Prepara o banco de dados para receber o comando de forma segura
    $consulta = $pdo->prepare($sql);
    
    // Envia os novos dados preenchidos no formulário direto para o banco
    $consulta->execute([
        ':titulo'     => $_POST['titulo'],
        ':autor'      => $_POST['autor'],
        ':ano'        => $_POST['ano'] !== '' ? (int)$_POST['ano'] : null, // Se estiver vazio, salva como nulo
        ':genero'     => $_POST['genero'],
        ':quantidade' => (int)$_POST['quantidade'],
        ':id'         => $id,
    ]);
    
    // Redireciona o usuário de volta para a página inicial com uma mensagem de sucesso
    header('Location: index.php?msg=' . urlencode('Livro atualizado!'));
    exit; // Para a execução do código aqui
}

// Busca no banco os dados atuais do livro usando o ID
$consulta = $pdo->prepare("SELECT * FROM livros WHERE id = :id");
$consulta->execute([':id' => $id]);
$livro = $consulta->fetch(); // Guarda os dados do livro nesta variável

// Se o ID não existir no banco, para o site e avisa o usuário
if (!$livro) {
    die('Livro não encontrado.');
}

// Puxa o arquivo de topo/cabeçalho do site
require 'topo.php';
?>

<h2>Editar livro</h2>
<!-- Formulário que envia os dados de volta para esta mesma página -->
<form method="post" class="ficha">
  <!-- Campo invisível que guarda o ID do livro que está sendo editado -->
  <input type="hidden" name="id" value="<?= $livro['id'] ?>">
  
  <!-- Campos do formulário já preenchidos com os dados atuais do livro (usando a função de segurança 'e') -->
  <label>Título <input name="titulo" value="<?= e($livro['titulo']) ?>" required maxlength="150"></label>
  <label>Autor <input name="autor" value="<?= e($livro['autor']) ?>" required maxlength="100"></label>
  <label>Ano <input name="ano" type="number" value="<?= e($livro['ano']) ?>"></label>
  <label>Gênero <input name="genero" value="<?= e($livro['genero']) ?>" maxlength="50"></label>
  <label>Quantidade <input name="quantidade" type="number" min="1" value="<?= e($livro['quantidade']) ?>" required></label>
  
  <button>Salvar alterações</button>
</form>

<?php 
// Puxa o arquivo de rodapé do site
require 'rodape.php'; 
?>
