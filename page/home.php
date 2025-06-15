<?php
// Aqui você pode adicionar lógica de sessão/autenticação se desejar
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo - Gestão Futebol</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
   
    <link rel="stylesheet" href="/futebol-rj/style/global.css">
    
</head>
<body>

<?php include __DIR__ . '/../includes/nav.php'; ?>

<div class="welcome-container">
    <h1>Gestão Futebol</h1>
    <div class="subtitle">Gerencie campeonatos, equipes e estatísticas de forma simples, eficiente e profissional.</div>

    <!-- Explicação e funcionalidades -->
    <div class="site-description">
        <h2>Sobre o Sistema</h2>
        <p>
            O <strong>Gestão Futebol</strong> é um sistema web completo para administração e acompanhamento de campeonatos de futebol. Desenvolvido com foco na organização e praticidade, permite que federações, clubes e organizadores mantenham suas competições sempre atualizadas.
        </p>

        <h3>Funcionalidades Principais</h3>
        <ul>
            <li><strong>Cadastro de Times e Jogadores:</strong> Gerencie elencos com informações detalhadas, como posição, altura, peso, idade e mais.</li>
            <li><strong>Controle de Competições:</strong> Crie torneios de diferentes tipos (Estaduais, Copas, Amistosos, etc.) e temporadas.</li>
            <li><strong>Acompanhamento de Partidas:</strong> Registre dados de jogos com escalações, gols, assistências, estatísticas e árbitros.</li>
            <li><strong>Estatísticas Detalhadas:</strong> Monitore o desempenho individual de cada jogador com notas, passes, finalizações e cartões.</li>
            <li><strong>Gestão de Lesões e Transferências:</strong> Controle histórico médico e movimentações entre clubes.</li>
            <li><strong>Login de Usuário:</strong> Controle de acesso para administradores com segurança e simplicidade.</li>
        </ul>

        <h3>Benefícios do Sistema</h3>
        <ul>
            <li>Visualização clara e organizada de informações esportivas.</li>
            <li>Agilidade na atualização de resultados e tabelas.</li>
            <li>Melhoria na gestão técnica dos clubes.</li>
            <li>Facilidade de acesso em diferentes dispositivos (PC, tablets, smartphones).</li>
        </ul>

        <h3>Tecnologias Utilizadas</h3>
        <ul>
            <li><strong>PHP:</strong> Lógica de aplicação e integração com banco de dados.</li>
            <li><strong>MySQL:</strong> Armazenamento de dados estruturados.</li>
            <li><strong>HTML5 & CSS3:</strong> Estrutura e estilo da interface.</li>
            <li><strong>JavaScript (opcional):</strong> Interações dinâmicas e validações.</li>
        </ul>

        <h3>Objetivo</h3>
        <p>
            Oferecer uma solução prática e eficiente para quem precisa organizar e acompanhar o desenvolvimento de competições de futebol,
            com foco em confiabilidade, organização e facilidade de uso.
        </p>
    </div>

    <a href="index.php" class="login-link">Ir para o Login</a>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?>

</body>
</html>
