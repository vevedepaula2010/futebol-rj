<!DOCTYPE html>
<html lang="pt-br">
<?php
include_once("../header.php");
?>
<div class="container">
    <h2 class="titulo-pagina">Listar Times</h2>
    <nav class="menu-navegacao">
        <a href="../home.php">Início</a> |
        <a href="cadastrar.php">Cadastrar Time</a> |
        <a href="../jogadores/listar.php">Jogadores</a> |
        <a href="../jogos/listar.php">Jogos</a> |
        <a href="../lesoes/listar.php">Lesões</a> |
        <a href="../performance/listar.php">Performance</a>
    </nav>
    <?php

    class TimesListar {
        private $conn;

        public function __construct() {
            $this->conn = new mysqli("localhost", "root", "", "dbrfutebol");
            if ($this->conn->connect_error) {
                die("Falha na conexão: " . $this->conn->connect_error);
            }
        }

        public function exibirTabela() {
            $sql = "SELECT id, nome, cidade, estadio, fundacao, logo FROM times";
            $result = $this->conn->query($sql); 

            if ($result && $result->num_rows > 0) {
                echo "<table class='tabela-times'>";
                echo "<tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Nome</th>
                        <th>Cidade</th>
                        <th>Estádio</th>
                        <th>Fundação</th>
                      </tr>";
                while($row = $result->fetch_assoc()) {
                    $logoImg = '';
                    if (!empty($row['logo'])) {
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mime = $finfo->buffer($row['logo']);
                        if (!$mime) $mime = 'image/png';
                        $base64 = base64_encode($row['logo']);
                        $logoImg = "<img src='data:$mime;base64,$base64' alt='Logo' class='logo-time'>";
                    } else {
                        $logoImg = "<span class='sem-logo'>(sem logo)</span>";
                    }
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>$logoImg</td>
                            <td>{$row['nome']}</td>
                            <td>{$row['cidade']}</td>
                            <td>{$row['estadio']}</td>
                            <td>{$row['fundacao']}</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='msg-vazia'>Nenhum time cadastrado.</p>";
            }
        }

        public function __destruct() {
            $this->conn->close();
        }
    }

    $listar = new TimesListar();
    $listar->exibirTabela();
    ?>
</div>
</body>
</html>