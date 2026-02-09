<?php

/**
 * Clase para registro de actividades del sistema
 * Guarda logs diarios en formato JSON
 */
class RegistroActividad
{
  private $directorio = '../public/logs/'; // Directorio donde se guardarán los archivos JSON

  public function __construct()
  {
    // Verificar si el directorio existe, si no, crearlo
    if (!file_exists($this->directorio)) {
      mkdir($this->directorio, 0777, true);
    }
  }

  /**
   * Guarda una nueva actividad en el archivo JSON correspondiente al día.
   *
   * @param string $usuario Nombre del usuario.
   * @param string $pantalla Pantalla donde ocurrió la actividad.
   * @param string $actividad Acción realizada (listar, guardar, activar, desactivar, etc.).
   * @param string $mensaje Mensaje adicional sobre la actividad.
   */
public function registrarActividad($usuario, $pantalla, $actividad, $mensaje, $tipo)
{
    date_default_timezone_set('Europe/Madrid'); // Ajusta según tu zona horaria
    // Obtener la fecha actual para nombrar el archivo
    $fechaActual = date('Y-m-d');
    $archivo = $this->directorio . $fechaActual . '.json';

    // Si el archivo no existe, crearlo vacío y asignarle permisos completos
    if (!file_exists($archivo)) {
        file_put_contents($archivo, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        chmod($archivo, 0777); // Asignar permisos totales al archivo
    }

    // Cargar el contenido existente del archivo
    $contenido = file_get_contents($archivo);
    $actividades = json_decode($contenido, true) ?? [];

    // Crear el nuevo registro de actividad
    $nuevaActividad = [
        'usuario' => $usuario,
        'pantalla' => $pantalla,
        'actividad' => $actividad,
        'mensaje' => $mensaje,
        'tipo' => $tipo,
        'fecha_hora' => date('Y-m-d H:i:s')
    ];

    // Agregar la nueva actividad al array
    $actividades[] = $nuevaActividad;

    // Guardar el array actualizado en el archivo JSON
    file_put_contents($archivo, json_encode($actividades, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}


  /**
   * Lista todas las actividades registradas en un archivo JSON específico.
   *
   * @param string $fecha La fecha del archivo a listar (formato YYYY-MM-DD).
   * @return array Un array con las actividades registradas o un mensaje de error.
   */
  public function listarActividades($fecha)
  {
    $archivo = $this->directorio . $fecha . '.json';

    if (!file_exists($archivo)) {
      return ['error' => "No existe ningún registro para la fecha $fecha."];
    }

    $contenido = file_get_contents($archivo);
    return json_decode($contenido, true) ?? [];
  }

    public function generarToken($length = 30) 
    {
      // Caracteres permitidos (letras minúsculas y números)
      $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[random_int(0, $charactersLength - 1)];
      }
      return $randomString;
  }

}

?>
