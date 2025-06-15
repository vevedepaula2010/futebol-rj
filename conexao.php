<?php
class Database
{
    // Configurações de conexão
    private string $host = 'sql102.infinityfree.com';
    private string $db   = 'if0_39239672_dbrfutebol';
    private string $user = 'if0_39239672';
    private string $pass = '296745Ve';
    private string $charset = 'utf8mb4';

    
    // Instância do PDO
    private ?PDO $pdo = null;

    // Construtor: conecta ao banco
    public function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            throw new RuntimeException('Erro na conexão: ' . $e->getMessage());
        }
    }

    // Retorna a conexão PDO
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Executa uma query com prepared statements
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Executa comandos como INSERT/UPDATE/DELETE
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    // Retorna o último ID inserido (opcional)
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
