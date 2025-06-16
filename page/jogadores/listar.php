<?php
require_once __DIR__ . '/../../conexao.php';
$db = new Database();
$pdo = $db->getConnection();

$sql = "SELECT j.id, j.nome, j.data_nascimento, j.nacionalidade, j.numero_camisa, 
               p.nome as posicao, t.nome as time, j.altura, j.peso
        FROM jogadores j
        LEFT JOIN posicoes p ON j.posicao_id = p.id
        LEFT JOIN times t ON j.time_id = t.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$jogadores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Jogadores - Gestão Futebol</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/style/global.css">
    <style>
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .dashboard-title {
            font-size: 1.8rem;
            color: #1e88e5;
            margin: 0;
        }
        
        .btn-primary {
            display: inline-flex;
            align-items: center;
            padding: 10px 15px;
            background-color: #1e88e5;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #1565c0;
        }
        
        .btn-primary i {
            margin-right: 8px;
        }
        
        .players-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .player-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }
        
        .player-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .player-header {
            padding: 20px 20px 15px;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            text-align: center;
            position: relative;
        }
        
        .player-id {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .player-number {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.2);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .player-avatar-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            overflow: hidden;
            border: 3px solid white;
        }
        
        .player-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .default-avatar {
            font-size: 2.5rem;
            color: #1e88e5;
        }
        
        .player-name {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 3px rgba(0,0,0,0.2);
        }
        
        .player-position {
            font-size: 1.1rem;
            margin-top: 5px;
            opacity: 0.9;
        }
        
        .player-body {
            padding: 20px;
        }
        
        .player-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 2px;
        }
        
        .info-value {
            font-weight: 500;
            color: #333;
        }
        
        .player-team {
            display: flex;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }
        
        .team-logo {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e3f2fd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: #1e88e5;
            overflow: hidden;
        }
        
        .team-logo img {
            max-width: 100%;
            max-height: 100%;
        }
        
        .team-name {
            font-weight: 500;
            color: #333;
        }
        
        .player-footer {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .player-action {
            color: #1e88e5;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .player-action i {
            margin-right: 5px;
        }
        
        .player-action:hover {
            color: #1565c0;
        }
        
        .no-players {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            grid-column: 1 / -1;
        }
        
        .no-players i {
            font-size: 4rem;
            color: #e0e0e0;
            margin-bottom: 15px;
        }
        
        .no-players h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .add-player-btn {
            margin-top: 20px;
        }
        
        .player-stats {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .stat-item {
            flex: 1;
            text-align: center;
            padding: 8px;
            border-radius: 6px;
            background: #f5f5f5;
        }
        
        .stat-value {
            font-weight: bold;
            font-size: 1.1rem;
            color: #1e88e5;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: #666;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/nav.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Jogadores Cadastrados</h1>
        <a href="adicionar_jogador.php" class="btn-primary">
            <i class="fas fa-plus-circle"></i> Adicionar Jogador
        </a>
    </div>

    <?php if (count($jogadores) > 0): ?>
        <div class="players-grid">
            <?php foreach ($jogadores as $j): 
                $dataNasc = date('d/m/Y', strtotime($j['data_nascimento']));
                $idade = date_diff(date_create($j['data_nascimento']), date_create('today'))->y;
                $altura = number_format($j['altura'], 2, ',', '.');
                $peso = number_format($j['peso'], 2, ',', '.');
            ?>
                <div class="player-card">
                    <div class="player-header">
                        <div class="player-id">ID: <?= $j['id'] ?></div>
                        <div class="player-number"><?= $j['numero_camisa'] ?></div>
                        <div class="player-avatar-container">
                            <i class="fas fa-user default-avatar"></i>
                        </div>
                        <h2 class="player-name"><?= htmlspecialchars($j['nome']) ?></h2>
                        <div class="player-position"><?= htmlspecialchars($j['posicao']) ?></div>
                    </div>
                    
                    <div class="player-body">
                        <div class="player-info">
                            <div class="info-item">
                                <span class="info-label">Nacionalidade</span>
                                <span class="info-value"><?= htmlspecialchars($j['nacionalidade']) ?></span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">Nascimento</span>
                                <span class="info-value"><?= $dataNasc ?> (<?= $idade ?> anos)</span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">Altura</span>
                                <span class="info-value"><?= $altura ?> m</span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">Peso</span>
                                <span class="info-value"><?= $peso ?> kg</span>
                            </div>
                        </div>
                        
                        <?php if (!empty($j['time'])): ?>
                            <div class="player-team">
                                <div class="team-logo">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="team-name"><?= htmlspecialchars($j['time']) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="player-footer">
                        <a href="editar_jogador.php?id=<?= $j['id'] ?>" class="player-action">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="detalhes_jogador.php?id=<?= $j['id'] ?>" class="player-action">
                            <i class="fas fa-chart-line"></i> Estatísticas
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-players">
            <i class="fas fa-user-slash"></i>
            <h3>Nenhum jogador cadastrado</h3>
            <p>Adicione o primeiro jogador para começar a gestão</p>
            <a href="adicionar_jogador.php" class="btn-primary add-player-btn">
                <i class="fas fa-plus-circle"></i> Adicionar Jogador
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>