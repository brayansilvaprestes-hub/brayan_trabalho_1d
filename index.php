<?php
// Inclui o arquivo que faz a conexão com o banco de dados
require 'conexao.php';

// Pega o texto digitado na busca. Se não digitaram nada, fica vazio ''
$busca = $_GET['busca'] ?? '';

// Comando SQL para buscar os livros e contar quantos estão emprestados no momento
$sql = "SELECT l.id, l.titulo, l.autor, l.ano, l.genero, l.quantidade,
               COUNT(e.id) AS emprestados
        FROM livros l
        LEFT JOIN emprestimos e
               ON e.livro_id = l.id AND e.data_devolucao IS NULL
        WHERE l.titulo LIKE :busca OR l.autor LIKE :busca2
        GROUP BY l.id, l.titulo, l.autor, l.ano, l.genero, l.quantidade
        ORDER BY l.titulo";

// Prepara e executa a busca no banco usando o texto digitado (com % para buscar partes do nome)
$consulta = $pdo->prepare($sql);
$consulta->execute([':busca' => "%$busca%", ':busca2' => "%$busca%"]);
$livros = $consulta->fetchAll();

// Inclui o topo visual do site
require 'topo.php';
?>
<!-- Formulário de busca que envia os dados via GET para a barra de endereço -->
<form method="get" class="busca">
  <input type="text" name="busca" value="<?= e($busca) ?>" placeholder="Buscar por título ou autor">
  <button>Buscar</button>
</form>

<!-- Se houver alguma mensagem de aviso (como 'Livro excluído'), mostra ela aqui -->
<?php if (isset($_GET['msg'])): ?>
  <p class="aviso"><?= e($_GET['msg']) ?></p>
<?php endif; ?>

<!-- Tabela que lista todos os livros encontrados -->
<table>
  <tr><th>Título</th><th>Autor</th><th>Ano</th><th>Gênero</th><th>Disponíveis</th><th></th></tr>
  <?php foreach ($livros as $livro): ?>
    <tr>
      <td><?= e($livro['titulo']) ?></td>
      <td><?= e($livro['autor']) ?></td>
      <td><?= e($livro['ano']) ?></td>
      <td><?= e($livro['genero']) ?></td>
      <!-- Faz a conta (Total - Emprestados) para mostrar quantos livros restam na estante -->
      <td><?= $livro['quantidade'] - $livro['emprestados'] ?> de <?= $livro['quantidade'] ?></td>
      
      <!-- Botões de ação para cada livro -->
      <td class="acoes">
        <!-- Link que leva para a página de edição passando o ID do livro -->
        <a href="editar.php?id=<?= $livro['id'] ?>">Editar</a>
        
        <!-- Formulário para deletar o livro. O 'onsubmit' abre uma caixinha perguntando se tem certeza -->
        <form method="post" action="excluir.php" onsubmit="return confirm('Excluir este livro?')">
          <input type="hidden" name="id" value="<?= $livro['id'] ?>">
          <button class="perigo">Excluir</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<!-- Mostra o total de livros que apareceram na lista -->
<p class="total"><?= count($livros) ?> livro(s) encontrado(s).</p>

<?php 
// Inclui o rodapé visual do site
require 'rodape.php'; 
?>
