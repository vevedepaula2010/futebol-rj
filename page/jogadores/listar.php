<?php
require_once __DIR__ . '/../../conexao.php';
$db = new Database();
$pdo = $db->getConnection();

$sql = "SELECT j.id, j.nome, j.data_nascimento, j.nacionalidade, j.numero_camisa, 
               p.nome as posicao, t.nome as time, j.altura, j.peso
        FROM jogadores j
        LEFT JOIN posicoes p ON j.posicao_id = p.id
        LEFT JOIN times t ON j.time_id = t.id";
$lista = $pdo->query($sql)->fetchAll();
?>

<?php include __DIR__ . '/../../includes/nav.php'; ?>



<h2>Listar Jogadores</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Data Nasc.</th>
        <th>Nacionalidade</th>
        <th>Nº Camisa</th>
        <th>Posição</th>
        <th>Time</th>
        <th>Altura</th>
        <th>Peso</th>
    </tr>
    <?php foreach ($lista as $j): ?>
    <tr>
        <td><?= $j['id'] ?></td>
        <td><?= htmlspecialchars($j['nome']) ?></td>
        <td><?= $j['data_nascimento'] ?></td>
        <td><?= htmlspecialchars($j['nacionalidade']) ?></td>
        <td><?= $j['numero_camisa'] ?></td>
        <td><?= htmlspecialchars($j['posicao']) ?></td>
        <td><?= htmlspecialchars($j['time']) ?></td>
        <td><?= $j['altura'] ?></td>
        <td><?= $j['peso'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>