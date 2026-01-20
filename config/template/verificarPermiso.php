<?php


session_start();

// Si no hay sesión iniciada, redirige a login
if (!isset($_SESSION['sesion_iniciada']) || $_SESSION['sesion_iniciada'] !== true) {
    header("Location: ../../view/Home/index.php"); // Ajusta ruta si es necesario
    exit();
}

// Si no definiste la variable $moduloActual en el archivo que incluye este,
// mejor lanza un error para evitar problemas
if (!isset($moduloActual)) {
    die("Error: No se definió el módulo actual para verificar permisos.");
}

// Obtener id_rol del usuario desde sesión
$idRol = $_SESSION['id_rol'] ?? null;

// Define qué módulos puede ver cada rol
//
//
// TANTO GESTOR COMO ADMINISTRADOR VAN A TENER ACCESO A NUEVO APARTADO EMPRESA
// APARTADO EMPRESA ES UNA PANTALLA QUE DIRECTAMENTE ENTRA EN MODO EDICIÓN
// TENGO QUE HACER QUE CARGUEN TODOS LOS CAMPOS DIRECTAMENTE, Y PUEDO CAMBIARLOS
//
//
// 2: GESTOR
// 3: ADMIN
// 4: COMERCIAL
// 5: TÉCNICO
$permisosPorRol = [
    2 => ['usuarios', 'logs', 'mantenimientos', 'llamadas', 'dashboard', 'presupuestos', 'area_tecnica', 'elementos_consulta', 'documentos_tecnico', 'consultas_tecnico', 'informes_tecnico'], // Gestor (ejemplo)
    3 => ['usuarios', 'logs', 'mantenimientos', 'comerciales', 'llamadas', 'dashboard', 'presupuestos', 'area_tecnica', 'elementos_consulta', 'documentos_tecnico', 'consultas_tecnico', 'informes_tecnico'], // Administrador: todo
    4 => ['llamadas', 'mantenimientos', 'dashboard', 'presupuestos'], // Comercial
    5 => ['area_tecnica', 'elementos_consulta', 'documentos_tecnico', 'consultas_tecnico', 'informes_tecnico'], // Técnico
];

// Validar que el rol tenga permiso para el módulo actual
if (!isset($permisosPorRol[$idRol]) || !in_array($moduloActual, $permisosPorRol[$idRol])) {
    // No tiene permiso: redirige o muestra mensaje
    header("Location: ../../view/Home/accesoDenegado.php"); // O cualquier página que tengas para acceso denegado
    exit();
}

?>
