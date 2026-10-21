<?php
// Inclui o arquivo que faz a conexão com o banco de dados
require 'conexao.php';

// Se o usuário enviou algum formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Se clicou no botão de devolver o livro
    if (isset($_POST['devolver'])) {
        // Prepara o comando para colocar a data de hoje na devolução
        $consulta = $pdo->prepare("UPDATE emprestimos SET data_devolucao = CURDATE() WHERE id = :id");
        $consulta->execute([':id' => (int)$_POST['devolver']]);
        $msg = 'Devolução registrada.';
        
    // Se não for devolução, é um novo empréstimo
    } else {
        // Prepara o comando para salvar o empréstimo com a data de hoje
        $consulta = $pdo->prepare("INSERT INTO emprestimos (livro_id, aluno_id, data_emprestimo)
                                   VALUES (:livro, :aluno, CURDATE())");
        $consulta->execute([':livro' => (int)$_POST['livro_id'], ':aluno' => (int)$_POST['aluno_id']]);
        $msg = 'Empréstimo registrado.';
    }
    // Recarrega a página enviando a mensagem de sucesso
    header('Location: emprestimos.php?msg=' . urlencode($msg));
    exit;
}

// Busca a lista de empréstimos juntando os dados de alunos e livros
$sql = "SELECT e.id, a.nome, a.turma, l.titulo, e.data_emprestimo, e.data_devolucao
        FROM emprestimos e
        JOIN alunos a ON a.id = e.aluno_id
        JOIN livros l ON l.id = e.livro_id
        ORDER BY e.data_devolucao IS NOT NULL, e.data_emprestimo DESC";
$emprestimos = $pdo->query($sql)->fetchAll();

// Busca todos os alunos ordenados por nome para colocar no formulário
$alunos = $pdo->query("SELECT id, nome, turma FROM alunos ORDER BY nome")->fetchAll();

// Busca todos os livros ordenados por título para colocar no formulário
$livros = $pdo->query("SELECT id, titulo FROM livros ORDER BY titulo")->fetchAll();

// Inclui o topo visual do site
require 'topo.php';
?>
<h2>Novo empréstimo</h2>

<!-- Se houver mensagem de sucesso na barra de endereço, exibe ela aqui -->
<?php if (isset($_GET['msg'])): ?>
  <p class="aviso"><?= e($_GET['msg']) ?></p>
<?php endif; ?>

<!-- Formulário para cadastrar um novo empréstimo -->
<form method="post" class="linha">
  <!-- Caixa de seleção com a lista de alunos -->
  <select name="aluno_id" required>
    <option value="">Aluno…</option>
    <?php foreach ($alunos as $a): ?>
      <option value="<?= $a['id'] ?>"><?= e($a['nome']) ?> (<?= e($a['turma']) ?>)</option>
    <?php endforeach; ?>
  </select>
  
  <!-- Caixa de seleção com a lista de livros -->
  <select name="livro_id" required>
    <option value="">Livro…</option>
    <?php foreach ($livros as $l): ?>
      <option value="<?= $l['id'] ?>"><?= e($l['titulo']) ?></option>
    <?php endforeach; ?>
  </select>
  <button>Emprestar</button>
</form>

<h2>Empréstimos</h2>
<!-- Tabela que lista todos os empréstimos já feitos -->
<table>
  <tr><th>Aluno</th><th>Turma</th><th>Livro</th><th>Retirado em</th><th>Situação</th></tr>
  <?php foreach ($emprestimos as $emp): ?>
    <tr>
      <td><?= e($emp['nome']) ?></td>
      <td><?= e($emp['turma']) ?></td>
      <td><?= e($emp['titulo']) ?></td>
      <!-- Formata a data do empréstimo para o padrão brasileiro (dia/mês/ano) -->
      <td><?= date('d/m/Y', strtotime($emp['data_emprestimo'])) ?></td>
      <td>
        <!-- Se o livro ainda não foi devolvido, mostra o botão de devolução -->
        <?php if ($emp['data_devolucao'] === null): ?>
          <form method="post">
            <button name="devolver" value="<?= $emp['id'] ?>">Registrar devolução</button>
          </form>
        <!-- Se já foi devolvido, mostra a data da devolução formatada -->
        <?php else: ?>
          Devolvido em <?= date('d/m/Y', strtotime($emp['data_devolucao'])) ?>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php 
// Inclui o rodapé visual do site
require 'rodape.php'; 
?>
