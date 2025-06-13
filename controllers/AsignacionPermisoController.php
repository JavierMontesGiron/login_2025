<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\AsignacionPermisos;

class AsignacionPermisoController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        // Obtener datos para los selects
        $usuarios = self::obtenerUsuarios();
        $aplicaciones = self::obtenerAplicaciones();
        $usuariosAsignan = $usuarios; // Mismos usuarios pueden asignar
        
        $router->render('asignacionPermisos/index', [
            'usuarios' => $usuarios,
            'aplicaciones' => $aplicaciones,
            'usuariosAsignan' => $usuariosAsignan
        ]);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        // Validación de usuario_id
        $_POST['asignacion_usuario_id'] = filter_var($_POST['asignacion_usuario_id'], FILTER_VALIDATE_INT);
        if (!$_POST['asignacion_usuario_id']) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un usuario válido'
            ]);
            exit;
        }

        // Validación de app_id
        $_POST['asignacion_app_id'] = filter_var($_POST['asignacion_app_id'], FILTER_VALIDATE_INT);
        if (!$_POST['asignacion_app_id']) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una aplicación válida'
            ]);
            exit;
        }

        // Validación de permiso_id
        $_POST['asignacion_permiso_id'] = filter_var($_POST['asignacion_permiso_id'], FILTER_VALIDATE_INT);
        if (!$_POST['asignacion_permiso_id']) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un permiso válido'
            ]);
            exit;
        }

        // Validación de usuario que asigna
        $_POST['asignacion_usuario_asigno'] = filter_var($_POST['asignacion_usuario_asigno'], FILTER_VALIDATE_INT);
        if (!$_POST['asignacion_usuario_asigno']) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error: Usuario que asigna no válido'
            ]);
            exit;
        }

        // Validación de motivo
        $_POST['asignacion_motivo'] = trim(htmlspecialchars($_POST['asignacion_motivo']));
        if (strlen($_POST['asignacion_motivo']) < 10) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El motivo debe tener al menos 10 caracteres'
            ]);
            exit;
        }

        // Verificar que el permiso pertenezca a la aplicación seleccionada
        $permisoValido = self::fetchFirst("SELECT permiso_id FROM permiso WHERE permiso_id = {$_POST['asignacion_permiso_id']} AND permiso_app_id = {$_POST['asignacion_app_id']}");
        if (!$permisoValido) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El permiso seleccionado no pertenece a la aplicación'
            ]);
            exit;
        }

        // Verificar si ya existe una asignación activa igual
        $asignacionExistente = self::fetchFirst("
            SELECT asignacion_id 
            FROM asig_permisos 
            WHERE asignacion_usuario_id = {$_POST['asignacion_usuario_id']} 
            AND asignacion_app_id = {$_POST['asignacion_app_id']} 
            AND asignacion_permiso_id = {$_POST['asignacion_permiso_id']} 
            AND asignacion_situacion = 1
        ");
        
        if ($asignacionExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El usuario ya tiene este permiso asignado'
            ]);
            exit;
        }

        // Limpiar fechas
        $_POST['asignacion_fecha'] = '';
        $_POST['asignacion_fecha_retiro'] = null;

        try {
            $asignacion = new AsignacionPermisos($_POST);
            $resultado = $asignacion->crear();

            if ($resultado['resultado'] == 1) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permiso asignado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al asignar el permiso'
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error en el servidor',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarAsignaciones()
    {
        getHeadersApi();
        
        try {
            $sql = "SELECT 
                        ap.asignacion_id,
                        ap.asignacion_usuario_id,
                        ap.asignacion_app_id,
                        ap.asignacion_permiso_id,
                        ap.asignacion_fecha,
                        ap.asignacion_usuario_asigno,
                        ap.asignacion_motivo,
                        ap.asignacion_situacion,
                        (u.usuario_nom1 || ' ' || u.usuario_nom2 || ' ' || u.usuario_ape1 || ' ' || u.usuario_ape2) as usuario_nombre,
                        u.usuario_correo,
                        a.app_nombre_corto,
                        a.app_nombre_medium,
                        p.permiso_nombre,
                        p.permiso_desc,
                        (ua.usuario_nom1 || ' ' || ua.usuario_nom2 || ' ' || ua.usuario_ape1 || ' ' || ua.usuario_ape2) as usuario_asigno_nombre
                    FROM asig_permisos ap
                    INNER JOIN usuario u ON ap.asignacion_usuario_id = u.usuario_id
                    INNER JOIN aplicacion a ON ap.asignacion_app_id = a.app_id
                    INNER JOIN permiso p ON ap.asignacion_permiso_id = p.permiso_id
                    INNER JOIN usuario ua ON ap.asignacion_usuario_asigno = ua.usuario_id
                    WHERE ap.asignacion_situacion = 1
                    ORDER BY ap.asignacion_fecha DESC";
                    
            $data = self::fetchArray($sql);

            if (count($data) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Asignaciones obtenidas correctamente',
                    'data' => $data
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay asignaciones registradas',
                    'data' => []
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error en el servidor',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarUsuarios()
    {
        getHeadersApi();
        
        try {
            $sql = "SELECT 
                        usuario_id, 
                        CONCAT(usuario_nom1, ' ', usuario_nom2, ' ', usuario_ape1, ' ', usuario_ape2) as nombre_completo,
                        usuario_correo
                    FROM usuario 
                    WHERE usuario_situacion = 1 
                    ORDER BY usuario_nom1, usuario_ape1";
                    
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuarios obtenidos correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error en el servidor',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarAplicaciones()
    {
        getHeadersApi();
        
        try {
            $sql = "SELECT 
                        app_id, 
                        app_nombre_largo, 
                        app_nombre_medium, 
                        app_nombre_corto 
                    FROM aplicacion 
                    WHERE app_situacion = 1 
                    ORDER BY app_nombre_corto";
                    
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Aplicaciones obtenidas correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error en el servidor',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarPermisosPorApp()
    {
        getHeadersApi();
        
        $app_id = filter_var($_GET['app_id'], FILTER_VALIDATE_INT);
        
        if (!$app_id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de aplicación no válido'
            ]);
            exit;
        }

        try {
            $sql = "SELECT 
                        permiso_id, 
                        permiso_nombre, 
                        permiso_desc, 
                        permiso_clave 
                    FROM permiso 
                    WHERE permiso_app_id = $app_id 
                    AND permiso_situacion = 1 
                    ORDER BY permiso_nombre";
                    
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Permisos obtenidos correctamente',
                'data' => $data
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error en el servidor',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function retirarPermiso()
    {
        getHeadersApi();

        $id = filter_var($_POST['asignacion_id'], FILTER_VALIDATE_INT);
        
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de asignación no válido'
            ]);
            exit;
        }

        try {
            $consulta = "UPDATE asig_permisos 
                        SET asignacion_situacion = 0 
                        WHERE asignacion_id = $id";
            
            self::SQL($consulta);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Permiso retirado exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al retirar permiso',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // Método privado para obtener usuarios (usado internamente)
    private static function obtenerUsuarios()
    {
        try {
            $sql = "SELECT 
                        usuario_id, 
                        usuario_nom1,
                        usuario_nom2,
                        usuario_ape1,
                        usuario_ape2,
                        usuario_correo
                    FROM usuario 
                    WHERE usuario_situacion = 1 
                    ORDER BY usuario_nom1, usuario_ape1";
                    
            return self::fetchArray($sql);

        } catch (Exception $e) {
            return [];
        }
    }

    // Método privado para obtener aplicaciones (usado internamente)
    private static function obtenerAplicaciones()
    {
        try {
            $sql = "SELECT 
                        app_id, 
                        app_nombre_largo, 
                        app_nombre_medium, 
                        app_nombre_corto 
                    FROM aplicacion 
                    WHERE app_situacion = 1 
                    ORDER BY app_nombre_corto";
                    
            return self::fetchArray($sql);

        } catch (Exception $e) {
            return [];
        }
    }
}