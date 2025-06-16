<?php
require_once __DIR__ . '/../../conexao.php';
$db = new Database();
$pdo = $db->getConnection();

$sql = "SELECT id, nome, cidade, estadio, fundacao, logo FROM times";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$times = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Times - Gestão Futebol</title>
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
        
        .teams-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .team-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }
        
        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .team-header {
            padding: 20px 20px 15px;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            text-align: center;
            position: relative;
        }
        
        .team-id {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .team-logo-container {
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
        
        .team-logo {
            max-width: 85%;
            max-height: 85%;
            object-fit: contain;
        }
        
        .default-logo {
            font-size: 2.5rem;
            color: #1e88e5;
        }
        
        .team-name {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 3px rgba(0,0,0,0.2);
        }
        
        .team-body {
            padding: 20px;
        }
        
        .team-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .info-item {
            display: flex;
            align-items: flex-start;
        }
        
        .info-icon {
            width: 30px;
            height: 30px;
            background-color: #e3f2fd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
            color: #1e88e5;
        }
        
        .info-content {
            flex: 1;
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
        
        .team-footer {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .team-action {
            color: #1e88e5;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .team-action i {
            margin-right: 5px;
        }
        
        .team-action:hover {
            color: #1565c0;
        }
        
        .no-teams {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            grid-column: 1 / -1;
        }
        
        .no-teams i {
            font-size: 4rem;
            color: #e0e0e0;
            margin-bottom: 15px;
        }
        
        .no-teams h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .add-team-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/nav.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Times Cadastrados</h1>
        <a href="adicionar_time.php" class="btn-primary">
            <i class="fas fa-plus-circle"></i> Adicionar Time
        </a>
    </div>

    <?php if (count($times) > 0): ?>
        <div class="teams-grid">
            <?php foreach ($times as $t): 
                $fundacao = date('d/m/Y', strtotime($t['fundacao']));
                $idade = date_diff(date_create($t['fundacao']), date_create('today'))->y;
            ?>
                <div class="team-card">
                    <div class="team-header">
                        <div class="team-id">ID: <?= $t['id'] ?></div>
                        <div class="team-logo-container">
                            <?php if (!empty($t['logo']) && is_string($t['logo'])): 
                                // Verifica se o logo é uma string válida
                                $mime = 'image/png'; // Assume PNG por padrão
                                if (function_exists('finfo_open')) {
                                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                    $mime = finfo_buffer($finfo, $t['logo']) ?: 'image/png';
                                }
                                $base64 = base64_encode($t['logo']); ?>
                                <img src="data:<?= $mime ?>;base64,<?= $base64 ?>" alt="Logo <?= htmlspecialchars($t['nome']) ?>" class="team-logo">
                            <?php else: ?>
                                <i class="fas fa-shield-alt default-logo"></i>
                            <?php endif; ?>
                        </div>
                        <h2 class="team-name"><?= htmlspecialchars($t['nome']) ?></h2>
                    </div>
                    
                    <div class="team-body">
                        <div class="team-info">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-city"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Cidade</div>
                                    <div class="info-value"><?= htmlspecialchars($t['cidade']) ?></div>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-stadium"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Estádio</div>
                                    <div class="info-value"><?= htmlspecialchars($t['estadio']) ?></div>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Fundação</div>
                                    <div class="info-value"><?= $fundacao ?> (<?= $idade ?> anos)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="team-footer">
                        <a href="editar_time.php?id=<?= $t['id'] ?>" class="team-action">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="detalhes_time.php?id=<?= $t['id'] ?>" class="team-action">
                            <i class="fas fa-info-circle"></i> Detalhes
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-teams">
            <i class="fas fa-users-slash"></i>
            <h3>Nenhum time cadastrado</h3>
            <p>Adicione o primeiro time para começar a gestão</p>
            <a href="adicionar_time.php" class="btn-primary add-team-btn">
                <i class="fas fa-plus-circle"></i> Adicionar Time
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>