<?php
// Inclui o arquivo que faz a conexão com o banco de dados
require 'conexao.php';

// Se o usuário preencheu e enviou o formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepara o comando SQL para inserir o novo livro no banco
    $sql = "INSERT INTO livros (titulo, autor, ano, genero, quantidade)
            VALUES (:titulo, :autor, :ano, :genero, :quantidade)";
    $consulta = $pdo->prepare($sql);
    
    // Executa o comando trocando as marcações pelos dados digitados pelo usuário
    $consulta->execute([
        ':titulo'     => $_POST['titulo'],
        ':autor'      => $_POST['autor'],
        // Se o ano for enviado em branco, salva como nulo; senão, salva como número
        ':ano'        => $_POST['ano'] !== '' ? (int)$_POST['ano'] : null,
        ':genero'     => $_POST['genero'],
        ':quantidade' => (int)$_POST['quantidade'], // Garante que a quantidade seja um número inteiro
    ]);
    
    // Redireciona de volta para a página inicial avisando que deu certo
    header('Location: index.php?msg=' . urlencode('Livro cadastrado!'));
    exit;
}

// Inclui o topo visual do site
require 'topo.php';
?>
<h2>Novo livro</h2>

<!-- Formulário para cadastrar as informações do livro -->
<form method="post" class="ficha">
  <!-- O campo "required" obriga o preenchimento e o "maxlength" limita as letras -->
  <label>Título <input name="titulo" required maxlength="150"></label>
  <label>Autor <input name="autor" required maxlength="100"></label>
  <label>Ano <input name="ano" type="number"></label>
  <label>Gênero <input name="genero" maxlength="50"></label>
  <!-- O "min=1" impede colocar quantidades negativas ou zero -->
  <label>Quantidade <input name="quantidade" type="number" min="1" value="1" required></label>
  <button>Salvar</button>
</form>

<?php 
// Inclui o rodapé visual do site
require 'rodape.php'; 
?>
