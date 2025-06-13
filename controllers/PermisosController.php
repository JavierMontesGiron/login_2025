<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Permisos;

class PermisosController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('permisos/index', []);
    }


    public static function guardarAPI()
    {
        getHeadersApi();
    
        // Validación de aplicación
        $permiso_app_id = filter_var($_POST['permiso_app_id'], FILTER_SANITIZE_NUMBER_INT);
        if (empty($permiso_app_id)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una aplicación'
            ]);
            exit;
        }
        
        //saniticacion de nombre y validaccion con capital
        $_POST['permiso_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['permiso_nombre']))));
        
        $cantidad_nombre = strlen($_POST['permiso_nombre']);
        
        if ($cantidad_nombre < 3) {
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre del permiso debe tener mas de 2 caracteres'
            ]);
            exit;
        }
        
        //saniticacion de clave
        $_POST['permiso_clave'] = strtoupper(trim(htmlspecialchars($_POST['permiso_clave'])));
        
        $cantidad_clave = strlen($_POST['permiso_clave']);
        
        if ($cantidad_clave < 3) {
          
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Clave del permiso debe tener mas de 2 caracteres'
            ]);
            exit;
        }
        
        //saniticacion de descripcion
        $_POST['permiso_desc'] = ucfirst(strtolower(trim(htmlspecialchars($_POST['permiso_desc']))));
        
        $cantidad_desc = strlen($_POST['permiso_desc']);
        
        if ($cantidad_desc < 5) {
         
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Descripción debe tener mas de 4 caracteres'
            ]);
            exit;
        }
        
        // Verificar si la clave ya existe en la misma aplicación
        $claveExistente = self::fetchFirst("SELECT permiso_id FROM permiso WHERE permiso_clave = '{$_POST['permiso_clave']}' AND permiso_app_id = $permiso_app_id");
        if ($claveExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La clave del permiso ya existe en esta aplicación'
            ]);
            exit;
        }
        
        // Verificar si el nombre ya existe en la misma aplicación
        $nombreExistente = self::fetchFirst("SELECT permiso_id FROM permiso WHERE permiso_nombre = '{$_POST['permiso_nombre']}' AND permiso_app_id = $permiso_app_id");
        if ($nombreExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del permiso ya existe en esta aplicación'
            ]);
            exit;
        }
        
        // Validación y formato de fecha para Informix
        if (empty($_POST['permiso_fecha'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La fecha es requerida'
            ]);
            exit;
        }
        
        // Convertir fecha al formato que acepta Informix (MDY)
        $fecha = $_POST['permiso_fecha'];
        $fechaFormateada = date('m/d/Y', strtotime($fecha));
        $_POST['permiso_fecha'] = $fechaFormateada;
        
        try {
            $permiso = new Permisos($_POST);
            $resultado = $permiso->crear();

            if($resultado['resultado'] == 1){
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permiso registrado correctamente',
                ]);
                
                exit;
            }else{
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al registrar el permiso',
                    'datos' => $_POST,
                    'permiso' => $permiso,
                ]);
                exit;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error en el servidor: ' . $e->getMessage()
            ]);
        }
    }

    public static function buscarPermisos()
    {
        getHeadersApi();
        
        try {
            $sql = "SELECT p.*, a.app_nombre_corto 
                    FROM permiso p 
                    INNER JOIN aplicacion a ON p.permiso_app_id = a.app_id 
                    WHERE p.permiso_situacion = 1";
            $data = self::fetchArray($sql);

            if (count($data) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permisos obtenidos correctamente',
                    'data' => $data
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay permisos registrados',
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

    public static function buscarAplicaciones()
    {
        getHeadersApi();
        
        try {
            $sql = "SELECT app_id, app_nombre_corto, app_nombre_largo FROM aplicacion WHERE app_situacion = 1 ORDER BY app_nombre_corto";
            $data = self::fetchArray($sql);

            if (count($data) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Aplicaciones obtenidas correctamente',
                    'data' => $data
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay aplicaciones registradas',
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

    public static function modificarPermiso()
    {
        getHeadersApi();

        $id = $_POST['permiso_id'];

        // Validación de aplicación
        $permiso_app_id = filter_var($_POST['permiso_app_id'], FILTER_SANITIZE_NUMBER_INT);
        if (empty($permiso_app_id)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una aplicación'
            ]);
            return;
        }

        // Sanitización de nombre
        $_POST['permiso_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['permiso_nombre']))));
        if (strlen($_POST['permiso_nombre']) < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre del permiso debe tener más de 2 caracteres'
            ]);
            return;
        }

        // Sanitización de clave
        $_POST['permiso_clave'] = strtoupper(trim(htmlspecialchars($_POST['permiso_clave'])));
        if (strlen($_POST['permiso_clave']) < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Clave del permiso debe tener más de 2 caracteres'
            ]);
            return;
        }

        // Sanitización de descripción
        $_POST['permiso_desc'] = ucfirst(strtolower(trim(htmlspecialchars($_POST['permiso_desc']))));
        if (strlen($_POST['permiso_desc']) < 5) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Descripción debe tener más de 4 caracteres'
            ]);
            return;
        }

        // Verificar duplicados (excluyendo el permiso actual)
        $claveExistente = self::fetchFirst("SELECT permiso_id FROM permiso WHERE permiso_clave = '{$_POST['permiso_clave']}' AND permiso_app_id = $permiso_app_id AND permiso_id != $id");
        if ($claveExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La clave del permiso ya existe en esta aplicación'
            ]);
            return;
        }

        $nombreExistente = self::fetchFirst("SELECT permiso_id FROM permiso WHERE permiso_nombre = '{$_POST['permiso_nombre']}' AND permiso_app_id = $permiso_app_id AND permiso_id != $id");
        if ($nombreExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del permiso ya existe en esta aplicación'
            ]);
            return;
        }

        // Validación y formato de fecha para Informix
        if (empty($_POST['permiso_fecha'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La fecha es requerida'
            ]);
            return;
        }
        
        // Convertir fecha al formato que acepta Informix (MDY)
        $fecha = $_POST['permiso_fecha'];
        $fechaFormateada = date('m/d/Y', strtotime($fecha));
        $_POST['permiso_fecha'] = $fechaFormateada;

        try {
            $data = Permisos::find($id);
            $data->sincronizar([
                'permiso_app_id' => $_POST['permiso_app_id'],
                'permiso_nombre' => $_POST['permiso_nombre'],
                'permiso_clave' => $_POST['permiso_clave'],
                'permiso_desc' => $_POST['permiso_desc'],
                'permiso_fecha' => $_POST['permiso_fecha'],
                'permiso_situacion' => 1
            ]);
            
            $data->actualizar();
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del permiso ha sido modificada exitosamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar permiso',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarPermiso()
    {
        getHeadersApi();
        
        try {
            $id = filter_var($_POST['permiso_id'], FILTER_SANITIZE_NUMBER_INT);
            $consulta = "UPDATE permiso SET permiso_situacion = 0 WHERE permiso_id = $id";
            self::SQL($consulta);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Permiso eliminado exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar permiso',
                'detalle' => $e->getMessage()
            ]);
        }
    }
    
}