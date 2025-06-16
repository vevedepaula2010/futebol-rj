<?php
require_once __DIR__ . '/../../conexao.php';
$db = new Database();
$pdo = $db->getConnection();

// Consulta para obter as estatísticas de performance dos jogadores
$sql = "SELECT
            ej.id,
            j.nome AS jogador,
            p.nome AS posicao,
            t.nome AS time,
            g.data AS data_jogo,
            tc.nome AS time_casa,
            tf.nome AS time_fora,
            c.nome AS competicao,
            ej.minutos_jogados,
            ej.gols,
            ej.assistencias,
            ej.finalizacoes,
            ej.passes_certos,
            ej.cartao_amarelo,
            ej.cartao_vermelho,
            ej.nota
        FROM estatisticas_jogo ej
        JOIN jogadores j ON ej.jogador_id = j.id
        JOIN posicoes p ON j.posicao_id = p.id
        JOIN times t ON j.time_id = t.id
        JOIN jogos g ON ej.jogo_id = g.id
        JOIN times tc ON g.time_casa_id = tc.id
        JOIN times tf ON g.time_fora_id = tf.id
        JOIN competicoes c ON g.competicao_id = c.id
        ORDER BY g.data DESC, ej.nota DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$performances = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular estatísticas gerais
$total_jogos = count($performances);
$media_nota = 0;
$total_gols = 0;
$total_assistencias = 0;

if ($total_jogos > 0) {
    $soma_notas = 0;
    foreach ($performances as $p) {
        $soma_notas += $p['nota'];
        $total_gols += $p['gols'];
        $total_assistencias += $p['assistencias'];
    }
    $media_nota = round($soma_notas / $total_jogos, 2);
}

// Função para calcular a eficiência
function calcularEficiencia($performance) {
    $minutos = $performance['minutos_jogados'];
    if ($minutos == 0) return 0;
    
    $gols = $performance['gols'] * 100;
    $assistencias = $performance['assistencias'] * 80;
    $passes = $performance['passes_certos'] * 0.2;
    $finalizacoes = $performance['finalizacoes'] * 0.5;
    
    $eficiencia = ($gols + $assistencias + $passes + $finalizacoes) / $minutos;
    
    // Penalizações por cartões
    if ($performance['cartao_vermelho']) {
        $eficiencia *= 0.7; // Reduz 30% por cartão vermelho
    } elseif ($performance['cartao_amarelo']) {
        $eficiencia *= 0.9; // Reduz 10% por cartão amarelo
    }
    
    return round($eficiencia, 2);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance dos Jogadores - Gestão Futebol</title>
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
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 10px 0;
            color: #333;
        }
        
        .stat-label {
            font-size: 1rem;
            color: #666;
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
        
        .performance-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }
        
        .performance-table th {
            background-color: #1e88e5;
            color: white;
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
        }
        
        .performance-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .performance-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .player-info {
            display: flex;
            align-items: center;
        }
        
        .player-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: #757575;
            font-weight: bold;
        }
        
        .player-name {
            font-weight: 600;
        }
        
        .player-position {
            font-size: 0.85rem;
            color: #666;
        }
        
        .match-info {
            display: flex;
            flex-direction: column;
        }
        
        .match-teams {
            font-weight: 600;
        }
        
        .match-date {
            font-size: 0.85rem;
            color: #666;
        }
        
        .competition-info {
            display: flex;
            align-items: center;
        }
        
        .competition-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e3f2fd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: #1e88e5;
        }
        
        .stats-container-small {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .stat-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .stat-goals {
            background-color: #e8f5e9;
            color: #4caf50;
        }
        
        .stat-assists {
            background-color: #e3f2fd;
            color: #1e88e5;
        }
        
        .stat-passes {
            background-color: #fff8e1;
            color: #ff8f00;
        }
        
        .stat-shots {
            background-color: #fbe9e7;
            color: #ff5722;
        }
        
        .card-yellow {
            background-color: #fffde7;
            color: #f9a825;
            border: 1px solid #f9a825;
        }
        
        .card-red {
            background-color: #ffebee;
            color: #f44336;
            border: 1px solid #f44336;
        }
        
        .rating {
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .rating-high {
            color: #4caf50;
        }
        
        .rating-medium {
            color: #ff9800;
        }
        
        .rating-low {
            color: #f44336;
        }
        
        .efficiency {
            font-weight: 600;
            font-size: 0.9rem;
            padding: 4px 8px;
            border-radius: 20px;
            text-align: center;
        }
        
        .efficiency-high {
            background-color: #e8f5e9;
            color: #4caf50;
        }
        
        .efficiency-medium {
            background-color: #fff8e1;
            color: #ff8f00;
        }
        
        .efficiency-low {
            background-color: #ffebee;
            color: #f44336;
        }
        
        .no-performances {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .no-performances i {
            font-size: 3rem;
            color: #e0e0e0;
            margin-bottom: 15px;
        }
        
        .no-performances h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #1e88e5;
        }
        
        .pagination a:hover {
            background-color: #1e88e5;
            color: white;
        }
        
        .pagination .active {
            background-color: #1e88e5;
            color: white;
            border-color: #1e88e5;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/nav.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Performance dos Jogadores</h1>
        <a href="adicionar_performance.php" class="btn-primary">
            <i class="fas fa-plus-circle"></i> Nova Performance
        </a>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-value"><?= $total_jogos ?></div>
            <div class="stat-label">Performances Registradas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $media_nota ?></div>
            <div class="stat-label">Média de Nota</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $total_gols ?></div>
            <div class="stat-label">Total de Gols</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $total_assistencias ?></div>
            <div class="stat-label">Total de Assistências</div>
        </div>
    </div>

    <div class="filters">
        <div class="filter-group">
            <label for="filter-player">Jogador</label>
            <select id="filter-player">
                <option value="">Todos os Jogadores</option>
                <!-- Opções de jogadores seriam preenchidas via PHP na implementação real -->
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
            <label for="filter-competition">Competição</label>
            <select id="filter-competition">
                <option value="">Todas as Competições</option>
                <!-- Opções de competições seriam preenchidas via PHP na implementação real -->
            </select>
        </div>
        
        <div class="filter-group">
            <label for="filter-date">Data</label>
            <input type="date" id="filter-date">
        </div>
    </div>

    <?php if (count($performances) > 0): ?>
        <table class="performance-table">
            <thead>
                <tr>
                    <th>Jogador</th>
                    <th>Jogo</th>
                    <th>Competição</th>
                    <th>Estatísticas</th>
                    <th>Minutos</th>
                    <th>Eficiência</th>
                    <th>Nota</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($performances as $perf): 
                    $eficiencia = calcularEficiencia($perf);
                    $ratingClass = $perf['nota'] >= 7.5 ? 'rating-high' : ($perf['nota'] >= 6.0 ? 'rating-medium' : 'rating-low');
                    $efficiencyClass = $eficiencia >= 2.0 ? 'efficiency-high' : ($eficiencia >= 1.0 ? 'efficiency-medium' : 'efficiency-low');
                ?>
                    <tr>
                        <td>
                            <div class="player-info">
                                <div class="player-avatar"><?= substr($perf['jogador'], 0, 1) ?></div>
                                <div>
                                    <div class="player-name"><?= htmlspecialchars($perf['jogador']) ?></div>
                                    <div class="player-position"><?= htmlspecialchars($perf['posicao']) ?> - <?= htmlspecialchars($perf['time']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="match-info">
                                <div class="match-teams"><?= htmlspecialchars($perf['time_casa']) ?> vs <?= htmlspecialchars($perf['time_fora']) ?></div>
                                <div class="match-date"><?= date('d/m/Y', strtotime($perf['data_jogo'])) ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="competition-info">
                                <div class="competition-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div><?= htmlspecialchars($perf['competicao']) ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="stats-container-small">
                                <?php if ($perf['gols'] > 0): ?>
                                    <div class="stat-badge stat-goals">
                                        <i class="fas fa-futbol"></i> <?= $perf['gols'] ?> G
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($perf['assistencias'] > 0): ?>
                                    <div class="stat-badge stat-assists">
                                        <i class="fas fa-assistive-listening-systems"></i> <?= $perf['assistencias'] ?> A
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($perf['passes_certos'] > 0): ?>
                                    <div class="stat-badge stat-passes">
                                        <i class="fas fa-exchange-alt"></i> <?= $perf['passes_certos'] ?> P
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($perf['finalizacoes'] > 0): ?>
                                    <div class="stat-badge stat-shots">
                                        <i class="fas fa-bullseye"></i> <?= $perf['finalizacoes'] ?> F
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($perf['cartao_amarelo']): ?>
                                    <div class="stat-badge card-yellow">
                                        <i class="fas fa-square"></i> Amarelo
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($perf['cartao_vermelho']): ?>
                                    <div class="stat-badge card-red">
                                        <i class="fas fa-square"></i> Vermelho
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= $perf['minutos_jogados'] ?>'</td>
                        <td>
                            <div class="efficiency <?= $efficiencyClass ?>">
                                <?= $eficiencia ?> EF
                            </div>
                        </td>
                        <td class="rating <?= $ratingClass ?>">
                            <?= number_format($perf['nota'], 1) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Paginação seria implementada na versão final -->
        <div class="pagination">
            <a href="#"><i class="fas fa-chevron-left"></i></a>
            <a href="#" class="active">1</a>
            <a href="#">2</a>
            <a href="#">3</a>
            <span>...</span>
            <a href="#">10</a>
            <a href="#"><i class="fas fa-chevron-right"></i></a>
        </div>
    <?php else: ?>
        <div class="no-performances">
            <i class="fas fa-chart-line"></i>
            <h3>Nenhuma performance registrada</h3>
            <p>Registre as primeiras estatísticas de jogo para começar a análise</p>
            <a href="adicionar_performance.php" class="btn-primary" style="margin-top: 15px;">
                <i class="fas fa-plus-circle"></i> Adicionar Performance
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>