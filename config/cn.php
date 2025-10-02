<?php 
class CNpdo {
    private $conexion;
    private $host = "localhost";
    private $usuario = "root";  
    private $password = "";
    private $baseDatos = "productos_iniciales"; // <<--- cambiamos aquí
    private $charset = "utf8mb4";
    private $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    public function __construct() {
        $dsn = "mysql:host=$this->host;dbname=$this->baseDatos;charset=$this->charset";
        try {
            $this->conexion = new PDO($dsn, $this->usuario, $this->password, $this->opciones);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    public function getConexion() {
        return $this->conexion;
    }
    public function consulta($sql, $parametros = []) {
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }
    public function ejecutar($sql, $parametros = []) {
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute($parametros);
    }
}
?>
