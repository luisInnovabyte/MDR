<?php
class Conexion
{
    protected $conect;

    public function __construct()
    {
        $Json_conf = __DIR__ . '/conexion.json';

        if (!file_exists($Json_conf)) {
            throw new Exception("Error: El archivo de configuración no existe");
        }

        $json = file_get_contents($Json_conf);
        $config = json_decode($json, true);

        if ($config === null) {
            throw new Exception("Error: No se pudo parsear el archivo de configuración");
        }

        try {
            $port = isset($config['port']) ? $config['port'] : '3306';
            $dsn = "mysql:host={$config['host']};port=$port;dbname={$config['database']};charset={$config['charset']}";
            $this->conect = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConexion()
    {
        return $this->conect; // ✅ Método para obtener la conexión
    }


    // Método para cerrar la conexión (opcional en PDO)
    public function cerrar()
    {
        $this->conect = null; // Cerrar la conexión asignando null
    }
}
