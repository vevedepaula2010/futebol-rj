<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="/style/nav.css">

<style>
    /* Estilos adicionais para o botão de logoff */
    .navbar-logout {
        margin-left: auto;
        background-color: #e74c3c;
        border-radius: 4px;
        transition: background-color 0.3s;
    }
    
    .navbar-logout:hover {
        background-color: #c0392b;
    }
    
    .navbar-logout .navbar-link {
        color: white !important;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    /* Espaçamento para o menu principal */
    .navbar-menu {
        display: flex;
        gap: 5px;
    }
</style>

<nav class="navbar">
    <div class="container navbar-container">
        <a href="/page/home.php" class="logo navbar-logo">
            <img src="/img/logo.png" alt="Logo Futebol RJ">
        </a>
        <ul class="nav-links navbar-menu">
            <li class="navbar-item"><a href="/page/jogadores/listar.php" class="navbar-link">Jogadores</a></li>
            <li class="navbar-item"><a href="/page/times/listar.php" class="navbar-link">Times</a></li>
            <li class="navbar-item"><a href="/page/jogos/listar.php" class="navbar-link">Jogos</a></li>
            <li class="navbar-item"><a href="/page/lesoes/listar.php" class="navbar-link">Lesões</a></li>
            <li class="navbar-item"><a href="/page/performance/listar.php" class="navbar-link">Performance</a></li>
            
            <!-- Botão de Logoff -->
            <li class="navbar-item navbar-logout">
                <a href="/index.php" class="navbar-link">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </li>
        </ul>
    </div>
</nav>