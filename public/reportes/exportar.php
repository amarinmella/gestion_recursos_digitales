}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportar Datos - Sistema de Gestión de Recursos</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/reportes.css">
</head>

<body>
    <div class="dashboard">
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo-icon"></div>
                <div>Sistema de Gestión</div>
            </div>
            <div class="sidebar-nav">
                <a href="../admin/dashboard.php" class="nav-item">Dashboard</a>
                <?php if (has_role([ROL_ADMIN, ROL_ACADEMICO])): ?>
                    <a href="../usuarios/listar.php" class="nav-item">Usuarios</a>
                <?php endif; ?>
                <a href="../recursos/listar.php" class="nav-item">Recursos</a>
                <a href="../reservas/listar.php" class="nav-item">Reservas</a>
                <a href="../reservas/calendario.php" class="nav-item">Calendario</a>
                <?php if (has_role([ROL_ADMIN, ROL_ACADEMICO])): ?>
                    <a href="../mantenimiento/listar.php" class="nav-item">Mantenimiento</a>
                    <a href="../reportes/reportes_dashboard.php" class="nav-item active">Reportes</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="content">
            <div class="top-bar">
                <h1>Exportar Datos</h1>
                <div class="user-info">
                    <span class="user-name"><?php echo $_SESSION['usuario_nombre']; ?></span>
                    <a href="../logout.php" class="logout-btn">Cerrar sesión</a>
                </div>
            </div>

            <?php echo $mensaje; ?>

            <div class="card">
                <h2 class="card-title">Opciones de Exportación</h2>
                <p>Seleccione el tipo de datos que desea exportar y el formato de salida.</p>

                <form action="" method="POST" class="report-filters">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label class="filter-label" for="tipo_reporte">Tipo de Reporte:</label>
                            <select id="tipo_reporte" name="tipo_reporte" class="filter-select" required>
                                <option value="">Seleccione un tipo de reporte</option>
                                <option value="uso_recursos">Uso de Recursos</option>
                                <option value="estadisticas_reservas">Estadísticas de Reservas</option>
                                <option value="usuarios">Listado de Usuarios</option>
                                <option value="reservas">Listado de Reservas</option>
                                <option value="recursos">Listado de Recursos</option>
                                <option value="mantenimientos">Listado de Mantenimientos</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label" for="formato">Formato de Exportación:</label>
                            <select id="formato" name="formato" class="filter-select" required>
                                <option value="csv">CSV (Valores separados por comas)</option>
                                <option value="excel">Excel (CSV compatible)</option>
                                <option value="pdf">PDF (Documento)</option>
                            </select>
                        </div>
                    </div>

                    <div class="filter-row">
                        <div class="filter-group">
                            <label class="filter-label" for="fecha_inicio">Fecha Inicio:</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="filter-input" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label" for="fecha_fin">Fecha Fin:</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="filter-input" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="filtro-adicionales" id="filtros-adicionales" style="display: none;">
                        <!-- Estos filtros se mostrarán/ocultarán según el tipo de reporte -->
                    </div>

                    <div class="filter-actions">
                        <button type="submit" name="exportar" class="btn btn-primary">Exportar Datos</button>
                        <button type="reset" class="btn btn-secondary">Limpiar Filtros</button>
                    </div>
                </form>
            </div>

            <div class="export-options">
                <div class="export-option">
                    <h3 class="export-title">Reservas del Mes Actual</h3>
                    <div class="export-description">
                        Exporta todas las reservas del mes actual en un formato fácil de analizar.
                    </div>
                    <a href="exportar.php?reporte=reservas&fecha_inicio=<?php echo date('Y-m-01'); ?>&fecha_fin=<?php echo date('Y-m-t'); ?>&formato=csv" class="btn btn-primary">Exportar CSV</a>
                </div>

                <div class="export-option">
                    <h3 class="export-title">Recursos Más Utilizados</h3>
                    <div class="export-description">
                        Informe de los recursos más utilizados en los últimos 30 días.
                    </div>
                    <a href="exportar.php?reporte=uso_recursos&fecha_inicio=<?php echo date('Y-m-d', strtotime('-30 days')); ?>&fecha_fin=<?php echo date('Y-m-d'); ?>&formato=csv" class="btn btn-primary">Exportar CSV</a>
                </div>

                <div class="export-option">
                    <h3 class="export-title">Listado Completo de Recursos</h3>
                    <div class="export-description">
                        Inventario completo de todos los recursos disponibles en el sistema.
                    </div>
                    <a href="exportar.php?reporte=recursos&formato=csv" class="btn btn-primary">Exportar CSV</a>
                </div>
            </div>

            <div class="card">
                <h2 class="card-title">Información sobre Formatos de Exportación</h2>

                <div style="margin-top: 15px<?php
                                            /**
                                             * Módulo de Reportes - Exportación de Datos
                                             */

                                            // Iniciar sesión
                                            session_start();

                                            // Incluir archivos necesarios
                                            require_once '../../config/config.php';
                                            require_once '../../config/database.php';
                                            require_once '../../includes/functions.php';

                                            // Verificar que el usuario esté logueado y tenga permisos
                                            require_login();

                                            // Solo administradores y académicos pueden acceder a reportes
                                            if (!has_role([ROL_ADMIN, ROL_ACADEMICO])) {
                                                $_SESSION['error'] = "No tienes permisos para acceder al módulo de reportes";
                                                redirect('../admin/dashboard.php');
                                                exit;
                                            }

                                            // Obtener instancia de la base de datos
                                            $db = Database::getInstance();

                                            // Verificar si se solicita exportación directa
                                            if (isset($_GET['reporte']) && !empty($_GET['reporte'])) {
                                                $tipo_reporte = $_GET['reporte'];

                                                // Validar el tipo de reporte solicitado
                                                $reportes_validos = ['uso_recursos', 'estadisticas_reservas', 'usuarios', 'reservas', 'recursos', 'mantenimientos'];

                                                if (in_array($tipo_reporte, $reportes_validos)) {
                                                    // Procesar la exportación directa
                                                    exportarReporte($tipo_reporte, $_GET);
                                                    exit;
                                                }
                                            }

                                            // Verificar si hay mensaje de éxito o error
                                            $mensaje = '';
                                            if (isset($_SESSION['success'])) {
                                                $mensaje = '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                                                unset($_SESSION['success']);
                                            } elseif (isset($_SESSION['error'])) {
                                                $mensaje = '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
                                                unset($_SESSION['error']);
                                            }

                                            // Si se envió el formulario de exportación
                                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exportar'])) {
                                                $tipo_reporte = $_POST['tipo_reporte'];
                                                $formato = $_POST['formato'];
                                                $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
                                                $fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');

                                                // Validar el tipo de reporte
                                                $reportes_validos = ['uso_recursos', 'estadisticas_reservas', 'usuarios', 'reservas', 'recursos', 'mantenimientos'];

                                                if (!in_array($tipo_reporte, $reportes_validos)) {
                                                    $_SESSION['error'] = "Tipo de reporte no válido";
                                                    redirect('exportar.php');
                                                    exit;
                                                }

                                                // Validar el formato
                                                $formatos_validos = ['csv', 'excel', 'pdf'];

                                                if (!in_array($formato, $formatos_validos)) {
                                                    $_SESSION['error'] = "Formato de exportación no válido";
                                                    redirect('exportar.php');
                                                    exit;
                                                }

                                                // Exportar el reporte
                                                exportarReporte($tipo_reporte, $_POST);
                                                exit;
                                            }

                                            // Función para exportar reportes
                                            function exportarReporte($tipo_reporte, $parametros)
                                            {
                                                global $db;

                                                // Obtener parámetros comunes
                                                $fecha_inicio = $parametros['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
                                                $fecha_fin = $parametros['fecha_fin'] ?? date('Y-m-d');
                                                $formato = $parametros['formato'] ?? 'csv';

                                                // Validar fechas
                                                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio)) {
                                                    $fecha_inicio = date('Y-m-d', strtotime('-30 days'));
                                                }

                                                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
                                                    $fecha_fin = date('Y-m-d');
                                                }

                                                // Definir el nombre del archivo
                                                $nombre_archivo = $tipo_reporte . '_' . date('Ymd') . '.' . $formato;

                                                // Configurar cabeceras para la descarga
                                                if ($formato === 'csv') {
                                                    header('Content-Type: text/csv; charset=utf-8');
                                                    header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
                                                } elseif ($formato === 'excel') {
                                                    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
                                                    header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
                                                } elseif ($formato === 'pdf') {
                                                    // Para PDF se requeriría una biblioteca externa como FPDF o TCPDF
                                                    // Por ahora, mostrar un mensaje de error
                                                    $_SESSION['error'] = "La exportación a PDF no está implementada en esta versión";
                                                    redirect('exportar.php');
                                                    exit;
                                                }

                                                // Abrir el flujo de salida
                                                $output = fopen('php://output', 'w');

                                                // Establecer el separador (para Excel es mejor usar punto y coma)
                                                $separador = ($formato === 'excel') ? ';' : ',';

                                                // Escribir BOM para UTF-8
                                                fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

                                                // Generar el contenido según el tipo de reporte
                                                switch ($tipo_reporte) {
                                                    case 'uso_recursos':
                                                        exportarUsoRecursos($output, $separador, $fecha_inicio, $fecha_fin, $parametros);
                                                        break;

                                                    case 'estadisticas_reservas':
                                                        exportarEstadisticasReservas($output, $separador, $fecha_inicio, $fecha_fin, $parametros);
                                                        break;

                                                    case 'usuarios':
                                                        exportarUsuarios($output, $separador, $parametros);
                                                        break;

                                                    case 'reservas':
                                                        exportarReservas($output, $separador, $fecha_inicio, $fecha_fin, $parametros);
                                                        break;

                                                    case 'recursos':
                                                        exportarRecursos($output, $separador, $parametros);
                                                        break;

                                                    case 'mantenimientos':
                                                        exportarMantenimientos($output, $separador, $fecha_inicio, $fecha_fin, $parametros);
                                                        break;
                                                }

                                                // Cerrar el flujo de salida
                                                fclose($output);
                                                exit;
                                            }

                                            // Función para exportar uso de recursos
                                            function exportarUsoRecursos($output, $separador, $fecha_inicio, $fecha_fin, $parametros)
                                            {
                                                global $db;

                                                // Obtener parámetros específicos
                                                $id_tipo = $parametros['tipo'] ?? 0;
                                                $id_usuario = $parametros['usuario'] ?? 0;
                                                $estado = $parametros['estado'] ?? '';

                                                // Preparar filtros para la consulta
                                                $filtros = [];
                                                $params = [];

                                                // Filtros básicos de fecha
                                                $filtros[] = "r.fecha_inicio >= ?";
                                                $params[] = $fecha_inicio . ' 00:00:00';

                                                $filtros[] = "r.fecha_inicio <= ?";
                                                $params[] = $fecha_fin . ' 23:59:59';

                                                // Filtro de tipo de recurso
                                                if ($id_tipo > 0) {
                                                    $filtros[] = "tr.id_tipo = ?";
                                                    $params[] = $id_tipo;
                                                }

                                                // Filtro de usuario
                                                if ($id_usuario > 0) {
                                                    $filtros[] = "r.id_usuario = ?";
                                                    $params[] = $id_usuario;
                                                }

                                                // Filtro de estado
                                                if (!empty($estado)) {
                                                    $filtros[] = "r.estado = ?";
                                                    $params[] = $estado;
                                                }

                                                // Construir cláusula WHERE
                                                $where = !empty($filtros) ? " WHERE " . implode(" AND ", $filtros) : "";

                                                // Consulta para obtener uso de recursos
                                                $sql = "
        SELECT 
            rec.id_recurso,
            rec.nombre as nombre_recurso,
            tr.nombre as tipo_recurso,
            rec.ubicacion,
            COUNT(r.id_reserva) as total_reservas,
            SUM(
                TIMESTAMPDIFF(HOUR, 
                    GREATEST(r.fecha_inicio, ?), 
                    LEAST(r.fecha_fin, ?)
                )
            ) as horas_uso,
            AVG(
                TIMESTAMPDIFF(MINUTE, r.fecha_inicio, r.fecha_fin)
            ) / 60 as duracion_promedio,
            ROUND(
                (COUNT(r.id_reserva) / (
                    SELECT COUNT(*) FROM reservas 
                    WHERE fecha_inicio >= ? AND fecha_inicio <= ?
                )) * 100, 
                2
            ) as porcentaje_ocupacion
        FROM recursos rec
        LEFT JOIN reservas r ON rec.id_recurso = r.id_recurso AND r.fecha_inicio >= ? AND r.fecha_inicio <= ?
        LEFT JOIN tipos_recursos tr ON rec.id_tipo = tr.id_tipo
        $where
        GROUP BY rec.id_recurso
        ORDER BY total_reservas DESC
    ";

                                                // Parámetros adicionales para cálculos en la consulta
                                                $params_adicionales = [
                                                    $fecha_inicio . ' 00:00:00', // GREATEST para horas_uso
                                                    $fecha_fin . ' 23:59:59',    // LEAST para horas_uso
                                                    $fecha_inicio . ' 00:00:00', // Para subquery de porcentaje
                                                    $fecha_fin . ' 23:59:59',    // Para subquery de porcentaje
                                                    $fecha_inicio . ' 00:00:00', // Para JOIN con reservas
                                                    $fecha_fin . ' 23:59:59'     // Para JOIN con reservas
                                                ];

                                                // Combinar los parámetros
                                                $params = array_merge($params_adicionales, $params);

                                                // Ejecutar consulta
                                                $recursos_uso = $db->getRows($sql, $params);

                                                // Escribir encabezados
                                                $encabezados = [
                                                    'ID Recurso',
                                                    'Nombre del Recurso',
                                                    'Tipo de Recurso',
                                                    'Ubicación',
                                                    'Total Reservas',
                                                    'Horas de Uso',
                                                    'Duración Promedio (horas)',
                                                    'Porcentaje de Ocupación'
                                                ];

                                                fputcsv($output, $encabezados, $separador);

                                                // Escribir datos
                                                foreach ($recursos_uso as $recurso) {
                                                    $fila = [
                                                        $recurso['id_recurso'],
                                                        $recurso['nombre_recurso'],
                                                        $recurso['tipo_recurso'],
                                                        $recurso['ubicacion'] ?: 'No especificada',
                                                        $recurso['total_reservas'],
                                                        number_format($recurso['horas_uso'], 1),
                                                        number_format($recurso['duracion_promedio'], 1),
                                                        number_format($recurso['porcentaje_ocupacion'], 2) . '%'
                                                    ];

                                                    fputcsv($output, $fila, $separador);
                                                }
                                            }

                                            // Función para exportar estadísticas de reservas
                                            function exportarEstadisticasReservas($output, $separador, $fecha_inicio, $fecha_fin, $parametros)
                                            {
                                                global $db;

                                                // Consulta para obtener estadísticas por mes
                                                $sql = "
        SELECT 
            DATE_FORMAT(fecha_inicio, '%Y-%m') as mes,
            COUNT(*) as total_reservas,
            SUM(CASE WHEN estado = 'confirmada' THEN 1 ELSE 0 END) as confirmadas,
            SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) as canceladas,
            SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
            SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas,
            AVG(TIMESTAMPDIFF(MINUTE, fecha_inicio, fecha_fin)) / 60 as duracion_promedio
        FROM reservas
        WHERE fecha_inicio BETWEEN ? AND ?
        GROUP BY DATE_FORMAT(fecha_inicio, '%Y-%m')
        ORDER BY mes
    ";

                                                $estadisticas_mes = $db->getRows($sql, [
                                                    $fecha_inicio . ' 00:00:00',
                                                    $fecha_fin . ' 23:59:59'
                                                ]);

                                                // Consulta para obtener estadísticas por día de la semana
                                                $sql = "
        SELECT 
            DAYOFWEEK(fecha_inicio) as dia_semana,
            CASE 
                WHEN DAYOFWEEK(fecha_inicio) = 1 THEN 'Domingo'
                WHEN DAYOFWEEK(fecha_inicio) = 2 THEN 'Lunes'
                WHEN DAYOFWEEK(fecha_inicio) = 3 THEN 'Martes'
                WHEN DAYOFWEEK(fecha_inicio) = 4 THEN 'Miércoles'
                WHEN DAYOFWEEK(fecha_inicio) = 5 THEN 'Jueves'
                WHEN DAYOFWEEK(fecha_inicio) = 6 THEN 'Viernes'
                WHEN DAYOFWEEK(fecha_inicio) = 7 THEN 'Sábado'
            END as nombre_dia,
            COUNT(*) as total_reservas
        FROM reservas
        WHERE fecha_inicio BETWEEN ? AND ?
        GROUP BY DAYOFWEEK(fecha_inicio)
        ORDER BY dia_semana
    ";

                                                $estadisticas_dia = $db->getRows($sql, [
                                                    $fecha_inicio . ' 00:00:00',
                                                    $fecha_fin . ' 23:59:59'
                                                ]);

                                                // Escribir encabezados para estadísticas por mes
                                                fputcsv($output, ['ESTADÍSTICAS POR MES'], $separador);
                                                fputcsv($output, ['Mes', 'Total Reservas', 'Confirmadas', 'Canceladas', 'Pendientes', 'Completadas', 'Duración Promedio (horas)'], $separador);

                                                // Escribir datos para estadísticas por mes
                                                foreach ($estadisticas_mes as $estadistica) {
                                                    $fila = [
                                                        $estadistica['mes'],
                                                        $estadistica['total_reservas'],
                                                        $estadistica['confirmadas'],
                                                        $estadistica['canceladas'],
                                                        $estadistica['pendientes'],
                                                        $estadistica['completadas'],
                                                        number_format($estadistica['duracion_promedio'], 1)
                                                    ];

                                                    fputcsv($output, $fila, $separador);
                                                }

                                                // Espacio en blanco
                                                fputcsv($output, [''], $separador);

                                                // Escribir encabezados para estadísticas por día de la semana
                                                fputcsv($output, ['ESTADÍSTICAS POR DÍA DE LA SEMANA'], $separador);
                                                fputcsv($output, ['Día', 'Total Reservas'], $separador);

                                                // Escribir datos para estadísticas por día de la semana
                                                foreach ($estadisticas_dia as $estadistica) {
                                                    $fila = [
                                                        $estadistica['nombre_dia'],
                                                        $estadistica['total_reservas']
                                                    ];

                                                    fputcsv($output, $fila, $separador);
                                                }
                                            }

                                            // Función para exportar usuarios
                                            function exportarUsuarios($output, $separador, $parametros)
                                            {
                                                global $db;

                                                // Obtener parámetros específicos
                                                $rol = $parametros['rol'] ?? 0;
                                                $activo = isset($parametros['activo']) ? intval($parametros['activo']) : -1;

                                                // Preparar filtros
                                                $filtros = [];
                                                $params = [];

                                                // Filtro de rol
                                                if ($rol > 0) {
                                                    $filtros[] = "u.id_rol = ?";
                                                    $params[] = $rol;
                                                }

                                                // Filtro de estado (activo/inactivo)
                                                if ($activo !== -1) {
                                                    $filtros[] = "u.activo = ?";
                                                    $params[] = $activo;
                                                }

                                                // Construir cláusula WHERE
                                                $where = !empty($filtros) ? " WHERE " . implode(" AND ", $filtros) : "";

                                                // Consulta
                                                $sql = "
        SELECT 
            u.id_usuario,
            u.nombre,
            u.apellido,
            u.email,
            r.nombre as rol,
            u.activo,
            u.fecha_registro,
            u.ultimo_login,
            COUNT(res.id_reserva) as total_reservas
        FROM usuarios u
        JOIN roles r ON u.id_rol = r.id_rol
        LEFT JOIN reservas res ON u.id_usuario = res.id_usuario
        $where
        GROUP BY u.id_usuario
        ORDER BY u.apellido, u.nombre
    ";

                                                // Ejecutar consulta
                                                $usuarios = $db->getRows($sql, $params);

                                                // Escribir encabezados
                                                $encabezados = [
                                                    'ID',
                                                    'Nombre',
                                                    'Apellido',
                                                    'Email',
                                                    'Rol',
                                                    'Estado',
                                                    'Fecha Registro',
                                                    'Último Login',
                                                    'Total Reservas'
                                                ];

                                                fputcsv($output, $encabezados, $separador);

                                                // Escribir datos
                                                foreach ($usuarios as $usuario) {
                                                    $fila = [
                                                        $usuario['id_usuario'],
                                                        $usuario['nombre'],
                                                        $usuario['apellido'],
                                                        $usuario['email'],
                                                        $usuario['rol'],
                                                        $usuario['activo'] ? 'Activo' : 'Inactivo',
                                                        $usuario['fecha_registro'],
                                                        $usuario['ultimo_login'] ?: 'Nunca',
                                                        $usuario['total_reservas']
                                                    ];

                                                    fputcsv($output, $fila, $separador);
                                                }
                                            }

                                            // Función para exportar reservas
                                            function exportarReservas($output, $separador, $fecha_inicio, $fecha_fin, $parametros)
                                            {
                                                global $db;

                                                // Obtener parámetros específicos
                                                $id_recurso = $parametros['recurso'] ?? 0;
                                                $id_usuario = $parametros['usuario'] ?? 0;
                                                $estado = $parametros['estado'] ?? '';

                                                // Preparar filtros
                                                $filtros = [];
                                                $params = [];

                                                // Filtros básicos de fecha
                                                $filtros[] = "r.fecha_inicio >= ?";
                                                $params[] = $fecha_inicio . ' 00:00:00';

                                                $filtros[] = "r.fecha_inicio <= ?";
                                                $params[] = $fecha_fin . ' 23:59:59';

                                                // Filtro de recurso
                                                if ($id_recurso > 0) {
                                                    $filtros[] = "r.id_recurso = ?";
                                                    $params[] = $id_recurso;
                                                }

                                                // Filtro de usuario
                                                if ($id_usuario > 0) {
                                                    $filtros[] = "r.id_usuario = ?";
                                                    $params[] = $id_usuario;
                                                }

                                                // Filtro de estado
                                                if (!empty($estado)) {
                                                    $filtros[] = "r.estado = ?";
                                                    $params[] = $estado;
                                                }

                                                // Construir cláusula WHERE
                                                $where = !empty($filtros) ? " WHERE " . implode(" AND ", $filtros) : "";

                                                // Consulta
                                                $sql = "
        SELECT 
            r.id_reserva,
            r.fecha_inicio,
            r.fecha_fin,
            CONCAT(u.nombre, ' ', u.apellido) as usuario,
            rec.nombre as recurso,
            tr.nombre as tipo_recurso,
            r.estado,
            r.descripcion,
            TIMESTAMPDIFF(HOUR, r.fecha_inicio, r.fecha_fin) as duracion_horas,
            r.fecha_creacion
        FROM reservas r
        JOIN usuarios u ON r.id_usuario = u.id_usuario
        JOIN recursos rec ON r.id_recurso = rec.id_recurso
        JOIN tipos_recursos tr ON rec.id_tipo = tr.id_tipo
        $where
        ORDER BY r.fecha_inicio DESC
    ";

                                                // Ejecutar consulta
                                                $reservas = $db->getRows($sql, $params);

                                                // Escribir encabezados
                                                $encabezados = [
                                                    'ID',
                                                    'Fecha Inicio',
                                                    'Fecha Fin',
                                                    'Usuario',
                                                    'Recurso',
                                                    'Tipo de Recurso',
                                                    'Estado',
                                                    'Descripción',
                                                    'Duración (horas)',
                                                    'Fecha de Creación'
                                                ];

                                                fputcsv($output, $encabezados, $separador);

                                                // Escribir datos
                                                foreach ($reservas as $reserva) {
                                                    $fila = [
                                                        $reserva['id_reserva'],
                                                        $reserva['fecha_inicio'],
                                                        $reserva['fecha_fin'],
                                                        $reserva['usuario'],
                                                        $reserva['recurso'],
                                                        $reserva['tipo_recurso'],
                                                        ucfirst($reserva['estado']),
                                                        $reserva['descripcion'],
                                                        $reserva['duracion_horas'],
                                                        $reserva['fecha_creacion']
                                                    ];

                                                    fputcsv($output, $fila, $separador);
                                                }
                                            }

                                            // Función para exportar recursos
                                            function exportarRecursos($output, $separador, $parametros)
                                            {
                                                global $db;

                                                // Obtener parámetros específicos
                                                $id_tipo = $parametros['tipo'] ?? 0;
                                                $estado = $parametros['estado'] ?? '';
                                                $disponible = isset($parametros['disponible']) ? intval($parametros['disponible']) : -1;

                                                // Preparar filtros
                                                $filtros = [];
                                                $params = [];

                                                // Filtro de tipo
                                                if ($id_tipo > 0) {
                                                    $filtros[] = "r.id_tipo = ?";
                                                    $params[] = $id_tipo;
                                                }

                                                // Filtro de estado
                                                if (!empty($estado)) {
                                                    $filtros[] = "r.estado = ?";
                                                    $params[] = $estado;
                                                }

                                                // Filtro de disponibilidad
                                                if ($disponible !== -1) {
                                                    $filtros[] = "r.disponible = ?";
                                                    $params[] = $disponible;
                                                }

                                                // Construir cláusula WHERE
                                                $where = !empty($filtros) ? " WHERE " . implode(" AND ", $filtros) : "";

                                                // Consulta
                                                $sql = "
        SELECT 
            r.id_recurso,
            r.nombre,
            t.nombre as tipo,
            r.estado,
            r.ubicacion,
            r.descripcion,
            r.disponible,
            r.fecha_alta,
            COUNT(res.id_reserva) as total_reservas,
            COUNT(m.id_mantenimiento) as total_mantenimientos
        FROM recursos r
        JOIN tipos_recursos t ON r.id_tipo = t.id_tipo
        LEFT JOIN reservas res ON r.id_recurso = res.id_recurso
        LEFT JOIN mantenimiento m ON r.id_recurso = m.id_recurso
        $where
        GROUP BY r.id_recurso
        ORDER BY r.nombre
    ";

                                                // Ejecutar consulta
                                                $recursos = $db->getRows($sql, $params);

                                                // Escribir encabezados
                                                $encabezados = [
                                                    'ID',
                                                    'Nombre',
                                                    'Tipo',
                                                    'Estado',
                                                    'Ubicación',
                                                    'Descripción',
                                                    'Disponible',
                                                    'Fecha Alta',
                                                    'Total Reservas',
                                                    'Total Mantenimientos'
                                                ];

                                                fputcsv($output, $encabezados, $separador);

                                                // Escribir datos
                                                foreach ($recursos as $recurso) {
                                                    $fila = [
                                                        $recurso['id_recurso'],
                                                        $recurso['nombre'],
                                                        $recurso['tipo'],
                                                        ucfirst($recurso['estado']),
                                                        $recurso['ubicacion'] ?: 'No especificada',
                                                        $recurso['descripcion'],
                                                        $recurso['disponible'] ? 'Sí' : 'No',
                                                        $recurso['fecha_alta'],
                                                        $recurso['total_reservas'],
                                                        $recurso['total_mantenimientos']
                                                    ];

                                                    fputcsv($output, $fila, $separador);
                                                }
                                            }

                                            // Función para exportar mantenimientos
                                            function exportarMantenimientos($output, $separador, $fecha_inicio, $fecha_fin, $parametros)
                                            {
                                                global $db;

                                                // Obtener parámetros específicos
                                                $id_recurso = $parametros['recurso'] ?? 0;
                                                $id_usuario = $parametros['usuario'] ?? 0;
                                                $estado = $parametros['estado'] ?? '';

                                                // Preparar filtros
                                                $filtros = [];
                                                $params = [];

                                                // Filtros básicos de fecha
                                                $filtros[] = "m.fecha_inicio >= ?";
                                                $params[] = $fecha_inicio . ' 00:00:00';

                                                $filtros[] = "m.fecha_inicio <= ?";
                                                $params[] = $fecha_fin . ' 23:59:59';

                                                // Filtro de recurso
                                                if ($id_recurso > 0) {
                                                    $filtros[] = "m.id_recurso = ?";
                                                    $params[] = $id_recurso;
                                                }

                                                // Filtro de usuario
                                                if ($id_usuario > 0) {
                                                    $filtros[] = "m.id_usuario = ?";
                                                    $params[] = $id_usuario;
                                                }

                                                // Filtro de estado
                                                if (!empty($estado)) {
                                                    $filtros[] = "m.estado = ?";
                                                    $params[] = $estado;
                                                }

                                                // Construir cláusula WHERE
                                                $where = !empty($filtros) ? " WHERE " . implode(" AND ", $filtros) : "";

                                                // Consulta
                                                $sql = "
        SELECT 
            m.id_mantenimiento,
            m.fecha_inicio,
            m.fecha_fin,
            CONCAT(u.nombre, ' ', u.apellido) as usuario,
            r.nombre as recurso,
            t.nombre as tipo_recurso,
            m.estado,
            m.descripcion,
            CASE 
                WHEN m.fecha_fin IS NULL THEN 'En progreso'
                ELSE CONCAT(TIMESTAMPDIFF(HOUR, m.fecha_inicio, m.fecha_fin), ' horas')
            END as duracion,
            m.fecha_registro
        FROM mantenimiento m
        JOIN usuarios u ON m.id_usuario = u.id_usuario
        JOIN recursos r ON m.id_recurso = r.id_recurso
        JOIN tipos_recursos t ON r.id_tipo = t.id_tipo
        $where
        ORDER BY m.fecha_inicio DESC
    ";

                                                // Ejecutar consulta
                                                $mantenimientos = $db->getRows($sql, $params);

                                                // Escribir encabezados
                                                $encabezados = [
                                                    'ID',
                                                    'Fecha Inicio',
                                                    'Fecha Fin',
                                                    'Usuario Responsable',
                                                    'Recurso',
                                                    'Tipo de Recurso',
                                                    'Estado',
                                                    'Descripción',
                                                    'Duración',
                                                    'Fecha de Registro'
                                                ];

                                                fputcsv($output, $encabezados, $separador);

                                                // Escribir datos
                                                foreach ($mantenimientos as $mantenimiento) {
                                                    $fila = [
                                                        $mantenimiento['id_mantenimiento'],
                                                        $mantenimiento['fecha_inicio'],
                                                        $mantenimiento['fecha_fin'] ?: 'No finalizado',
                                                        $mantenimiento['usuario'],
                                                        $mantenimiento['recurso'],
                                                        $mantenimiento['tipo_recurso'],
                                                        ucfirst($mantenimiento['estado']),
                                                        $mantenimiento['descripcion'],
                                                        $mantenimiento['duracion'],
                                                        $mantenimiento['fecha_registro']
                                                    ];

                                                    fputcsv($output, $fila, $separador);
                                                }
                                            }
