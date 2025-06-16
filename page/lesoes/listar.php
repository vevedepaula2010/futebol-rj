<!DOCTYPE html>
<?php
require_once __DIR__ . '/../../conexao.php';
$db = new Database();
$pdo = $db->getConnection();

// Adicione exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Consulta otimizada para trazer todos os jogadores e suas lesões
$sql = "SELECT 
            j.id AS jogador_id,
            j.nome AS jogador,
            t.nome AS tipo_lesao,
            l.data_inicio,
            l.data_fim,
            l.observacoes
        FROM jogadores j
        LEFT JOIN lesoes l ON l.jogador_id = j.id
        LEFT JOIN tipos_lesao t ON l.tipo_lesao_id = t.id
        ORDER BY j.nome ASC, l.data_inicio DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$lesoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Reorganizar os dados por jogador
$jogadores = [];
foreach ($lesoes as $l) {
    $id = $l['jogador_id'];
    
    if (!isset($jogadores[$id])) {
        $jogadores[$id] = [
            'nome' => $l['jogador'],
            'lesoes' => []
        ];
    }
    
    // Adiciona lesão apenas se houver dados válidos
    if (!empty($l['tipo_lesao']) || !empty($l['data_inicio']) || !empty($l['data_fim']) || !empty($l['observacoes'])) {
        $jogadores[$id]['lesoes'][] = [
            'tipo_lesao' => $l['tipo_lesao'] ?? 'Não especificado',
            'data_inicio' => $l['data_inicio'],
            'data_fim' => $l['data_fim'],
            'observacoes' => $l['observacoes'] ?? ''
        ];
    }
}

// Contadores para estatísticas
$total_jogadores = count($jogadores);
$jogadores_lesionados = 0;
$lesoes_ativas = 0;

foreach ($jogadores as $jogador) {
    if (count($jogador['lesoes']) > 0) {
        $jogadores_lesionados++;
        
        foreach ($jogador['lesoes'] as $lesao) {
            if (empty($lesao['data_fim']) || $lesao['data_fim'] > date('Y-m-d')) {
                $lesoes_ativas++;
            }
        }
    }
}
?>

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Lesões - Sistema de Futebol</title>
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
        
        .player-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        
        .player-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .player-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        .player-header {
            padding: 15px 20px;
            background-color: #1e88e5;
            color: white;
        }
        
        .player-name {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .player-status {
            font-size: 0.85rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .status-injured {
            background-color: #ff5252;
        }
        
        .status-recovered {
            background-color: #4caf50;
        }
        
        .status-available {
            background-color: #8bc34a;
        }
        
        .player-body {
            padding: 20px;
        }
        
        .injury-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .injury-item {
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .injury-item:last-child {
            border-bottom: none;
        }
        
        .injury-type {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            display: flex;
            align-items: center;
        }
        
        .injury-type i {
            margin-right: 8px;
            color: #ff9800;
        }
        
        .injury-dates {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .injury-date {
            display: flex;
            align-items: center;
        }
        
        .injury-date i {
            margin-right: 5px;
            font-size: 0.9rem;
        }
        
        .injury-notes {
            background-color: #f9f9f9;
            border-left: 3px solid #1e88e5;
            padding: 10px;
            font-size: 0.9rem;
            color: #555;
            border-radius: 0 4px 4px 0;
            margin-top: 8px;
        }
        
        .no-injuries {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        
        .no-injuries i {
            font-size: 2rem;
            color: #e0e0e0;
            margin-bottom: 10px;
            display: block;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            grid-column: 1 / -1;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #e0e0e0;
            margin-bottom: 15px;
        }
        
        .empty-state h3 {
            color: #666;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/nav.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Gestão de Lesões</h1>
        <a href="adicionar_lesao.php" class="btn-primary">
            <i class="fas fa-plus-circle"></i> Registrar Nova Lesão
        </a>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-value"><?= $total_jogadores ?></div>
            <div class="stat-label">Jogadores Cadastrados</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $jogadores_lesionados ?></div>
            <div class="stat-label">Jogadores com Lesões</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $lesoes_ativas ?></div>
            <div class="stat-label">Lesões Ativas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $total_jogadores - $jogadores_lesionados ?></div>
            <div class="stat-label">Jogadores Disponíveis</div>
        </div>
    </div>

    <?php if (count($jogadores) > 0): ?>
        <div class="player-grid">
            <?php foreach ($jogadores as $id => $jogador): 
                $temLesao = count($jogador['lesoes']) > 0;
                $lesaoAtiva = false;
                
                if ($temLesao) {
                    foreach ($jogador['lesoes'] as $lesao) {
                        if (empty($lesao['data_fim']) || $lesao['data_fim'] > date('Y-m-d')) {
                            $lesaoAtiva = true;
                            break;
                        }
                    }
                }
            ?>
                <div class="player-card">
                    <div class="player-header">
                        <h2 class="player-name">
                            <?= htmlspecialchars($jogador['nome']) ?>
                            <span class="player-status <?= $lesaoAtiva ? 'status-injured' : ($temLesao ? 'status-recovered' : 'status-available') ?>">
                                <?= $lesaoAtiva ? 'LESIONADO' : ($temLesao ? 'RECUPERADO' : 'DISPONÍVEL') ?>
                            </span>
                        </h2>
                    </div>
                    <div class="player-body">
                        <?php if ($temLesao): ?>
                            <ul class="injury-list">
                                <?php foreach ($jogador['lesoes'] as $lesao): 
                                    $dataFim = $lesao['data_fim'];
                                    $estaAtiva = empty($dataFim) || $dataFim > date('Y-m-d');
                                ?>
                                    <li class="injury-item">
                                        <div class="injury-type">
                                            <i class="fas fa-bandaid"></i> 
                                            <?= htmlspecialchars($lesao['tipo_lesao']) ?>
                                        </div>
                                        <div class="injury-dates">
                                            <span class="injury-date">
                                                <i class="fas fa-calendar-start"></i> 
                                                <?= $lesao['data_inicio'] ? date('d/m/Y', strtotime($lesao['data_inicio'])) : '--/--/----' ?>
                                            </span>
                                            <span class="injury-date <?= $estaAtiva ? 'text-danger' : '' ?>">
                                                <i class="fas fa-calendar-check"></i> 
                                                <?= $dataFim ? date('d/m/Y', strtotime($dataFim)) : ($estaAtiva ? 'Em Tratamento' : '--/--/----') ?>
                                            </span>
                                        </div>
                                        <?php if (!empty($lesao['observacoes'])): ?>
                                            <div class="injury-notes">
                                                <i class="fas fa-clipboard-list"></i> 
                                                <?= nl2br(htmlspecialchars($lesao['observacoes'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="no-injuries">
                                <i class="fas fa-check-circle"></i>
                                <p>Nenhuma lesão registrada</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-user-slash"></i>
            <h3>Nenhum jogador cadastrado no sistema</h3>
            <p>Adicione jogadores para começar a registrar lesões</p>
            <a href="/jogadores/adicionar_jogador.php" class="btn-primary" style="margin-top: 15px;">
                <i class="fas fa-user-plus"></i> Adicionar Jogador
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>