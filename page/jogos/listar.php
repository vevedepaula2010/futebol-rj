<?php
require_once __DIR__ . '/../../conexao.php';
$db = new Database();
$pdo = $db->getConnection();

// Consulta com joins para pegar os nomes dos times, competição e temporada
$sql = "SELECT 
            j.id,
            j.data,
            j.hora,
            j.local,
            casa.nome AS time_casa,
            fora.nome AS time_fora,
            j.gols_time_casa,
            j.gols_time_fora,
            c.nome AS competicao,
            t.ano AS temporada
        FROM jogos j
        LEFT JOIN times casa ON j.time_casa_id = casa.id
        LEFT JOIN times fora ON j.time_fora_id = fora.id
        LEFT JOIN competicoes c ON j.competicao_id = c.id
        LEFT JOIN temporadas t ON j.temporada_id = t.id
        ORDER BY j.data DESC, j.hora DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$jogos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Jogos - Gestão Futebol</title>
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
        
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
            background: white;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .matches-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .match-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }
        
        .match-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .match-header {
            padding: 15px 20px;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            text-align: center;
            position: relative;
        }
        
        .match-id {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .match-competition {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .match-date {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .match-date i {
            color: rgba(255,255,255,0.8);
        }
        
        .match-teams {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }
        
        .team {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 40%;
        }
        
        .team-logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        
        .team-logo i {
            font-size: 1.8rem;
            color: #1e88e5;
        }
        
        .team-name {
            font-weight: 600;
            text-align: center;
        }
        
        .match-score {
            font-size: 2rem;
            font-weight: 700;
            min-width: 80px;
            text-align: center;
        }
        
        .match-details {
            padding: 20px;
        }
        
        .match-info {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 15px;
        }
        
        .info-icon {
            width: 30px;
            height: 30px;
            background-color: #e3f2fd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e88e5;
        }
        
        .info-content {
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
        
        .match-footer {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .match-action {
            color: #1e88e5;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .match-action i {
            margin-right: 5px;
        }
        
        .match-action:hover {
            color: #1565c0;
        }
        
        .no-matches {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            grid-column: 1 / -1;
        }
        
        .no-matches i {
            font-size: 4rem;
            color: #e0e0e0;
            margin-bottom: 15px;
        }
        
        .no-matches h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .add-match-btn {
            margin-top: 20px;
        }
        
        .match-result {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
            font-weight: 500;
        }
        
        .win {
            color: #4caf50;
        }
        
        .draw {
            color: #ff9800;
        }
        
        .loss {
            color: #f44336;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/nav.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Jogos Cadastrados</h1>
        <a href="adicionar_jogo.php" class="btn-primary">
            <i class="fas fa-plus-circle"></i> Adicionar Jogo
        </a>
    </div>

    <div class="filters">
        <div class="filter-group">
            <label for="filter-competition">Competição</label>
            <select id="filter-competition">
                <option value="">Todas as Competições</option>
                <!-- Opções de competições seriam preenchidas via PHP na implementação real -->
            </select>
        </div>
        
        <div class="filter-group">
            <label for="filter-season">Temporada</label>
            <select id="filter-season">
                <option value="">Todas as Temporadas</option>
                <!-- Opções de temporadas seriam preenchidas via PHP na implementação real -->
            </select>
        </div>
        
        <div class="filter-group">
            <label for="filter-team">Time</label>
            <select id="filter-team">
                <option value="">Todos os Times</option>
                <!-- Opções de times seriam preenchidas via PHP na implementação real -->
            </select>
        </div>
        
        <div class="filter-group">
            <label for="filter-date">Período</label>
            <input type="date" id="filter-date">
        </div>
    </div>

    <?php if (count($jogos) > 0): ?>
        <div class="matches-grid">
            <?php foreach ($jogos as $j): 
                $data = date('d/m/Y', strtotime($j['data']));
                $hora = $j['hora'] ? date('H:i', strtotime($j['hora'])) : '--:--';
                $golsCasa = $j['gols_time_casa'] ?? '-';
                $golsFora = $j['gols_time_fora'] ?? '-';
                
                // Determinar resultado
                $resultado = '';
                $resultClass = '';
                if (is_numeric($golsCasa) && is_numeric($golsFora)) {
                    if ($golsCasa > $golsFora) {
                        $resultado = 'Vitória do ' . htmlspecialchars($j['time_casa']);
                        $resultClass = 'win';
                    } elseif ($golsCasa < $golsFora) {
                        $resultado = 'Vitória do ' . htmlspecialchars($j['time_fora']);
                        $resultClass = 'loss';
                    } else {
                        $resultado = 'Empate';
                        $resultClass = 'draw';
                    }
                }
            ?>
                <div class="match-card">
                    <div class="match-header">
                        <div class="match-id">ID: <?= $j['id'] ?></div>
                        <div class="match-competition"><?= htmlspecialchars($j['competicao']) ?> • <?= $j['temporada'] ?></div>
                        <div class="match-date">
                            <i class="fas fa-calendar-alt"></i>
                            <span><?= $data ?></span>
                            <i class="fas fa-clock"></i>
                            <span><?= $hora ?></span>
                        </div>
                        <div class="match-teams">
                            <div class="team">
                                <div class="team-logo">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="team-name"><?= htmlspecialchars($j['time_casa']) ?></div>
                            </div>
                            
                            <div class="match-score">
                                <?= $golsCasa ?> - <?= $golsFora ?>
                            </div>
                            
                            <div class="team">
                                <div class="team-logo">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="team-name"><?= htmlspecialchars($j['time_fora']) ?></div>
                            </div>
                        </div>
                        
                        <?php if ($resultado): ?>
                            <div class="match-result <?= $resultClass ?>">
                                <i class="fas fa-trophy"></i> <?= $resultado ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="match-details">
                        <div class="match-info">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Local do Jogo</div>
                                <div class="info-value"><?= htmlspecialchars($j['local']) ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="match-footer">
                        <a href="detalhes_jogo.php?id=<?= $j['id'] ?>" class="match-action">
                            <i class="fas fa-search"></i> Detalhes
                        </a>
                        <a href="editar_jogo.php?id=<?= $j['id'] ?>" class="match-action">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-matches">
            <i class="fas fa-futbol"></i>
            <h3>Nenhum jogo cadastrado</h3>
            <p>Adicione o primeiro jogo para começar a gestão</p>
            <a href="adicionar_jogo.php" class="btn-primary add-match-btn">
                <i class="fas fa-plus-circle"></i> Adicionar Jogo
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>