<?php
require_once '../conexao.php';

// Buscar times e posições para os selects
$db = new Database();
$pdo = $db->getConnection();

$times = $pdo->query("SELECT id, nome FROM times")->fetchAll();
$posicoes = $pdo->query("SELECT id, nome FROM posicoes")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ...validação e tratamento dos campos...
    $nome = $_POST['nome'];
    $data_nascimento = $_POST['data_nascimento'];
    $nacionalidade = $_POST['nacionalidade'];
    $numero_camisa = $_POST['numero_camisa'];
    $posicao_id = $_POST['posicao_id'];
    $time_id = $_POST['time_id'];
    $altura = $_POST['altura'];
    $peso = $_POST['peso'];

    $stmt = $pdo->prepare("INSERT INTO jogadores (nome, data_nascimento, nacionalidade, numero_camisa, posicao_id, time_id, altura, peso) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $data_nascimento, $nacionalidade, $numero_camisa, $posicao_id, $time_id, $altura, $peso]);
    echo "<div>Jogador cadastrado com sucesso!</div>";
}
?>

<?php include '../nav.php'; ?>

<h2>Cadastrar Jogador</h2>
<form method="post">
    <label>Nome: <input type="text" name="nome" required></label><br>
    <label>Data de Nascimento: <input type="date" name="data_nascimento"></label><br>
    <label>Nacionalidade: <input type="text" name="nacionalidade"></label><br>
    <label>Número da Camisa: <input type="number" name="numero_camisa"></label><br>
    <label>Posição:
        <select name="posicao_id">
            <?php foreach ($posicoes as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <label>Time:
        <select name="time_id">
            <?php foreach ($times as $t): ?>
                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <label>Altura (m): <input type="number" step="0.01" name="altura"></label><br>
    <label>Peso (kg): <input type="number" step="0.01" name="peso"></label><br>
    <button type="submit">Salvar</button>
</form>