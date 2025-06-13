<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Aplicaciones;

class AplicacionesController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('aplicaciones/index', []);
    }


    public static function guardarAPI()
    {
        getHeadersApi();
    
        
        //saniticacion de nombre largo y validaccion con capital
        $_POST['app_nombre_largo'] = ucwords(strtolower(trim(htmlspecialchars($_POST['app_nombre_largo']))));
        
        $cantidad_nombre_largo = strlen($_POST['app_nombre_largo']);
        
        if ($cantidad_nombre_largo < 3) {
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre largo debe tener mas de 2 caracteres'
            ]);
            exit;
        }
        
        //saniticacion de nombre medium y validaccion con capital
        $_POST['app_nombre_medium'] = ucwords(strtolower(trim(htmlspecialchars($_POST['app_nombre_medium']))));
        
        $cantidad_nombre_medium = strlen($_POST['app_nombre_medium']);
        
        if ($cantidad_nombre_medium < 3) {
          
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre mediano debe tener mas de 2 caracteres'
            ]);
            exit;
        }
        
        //saniticacion de nombre corto y validaccion con capital
        $_POST['app_nombre_corto'] = ucwords(strtolower(trim(htmlspecialchars($_POST['app_nombre_corto']))));
        
        $cantidad_nombre_corto = strlen($_POST['app_nombre_corto']);
        
        if ($cantidad_nombre_corto < 2) {
         
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre corto debe tener mas de 1 caracter'
            ]);
            exit;
        }
        
        // Verificar si el nombre largo ya existe
        $nombreExistente = self::fetchFirst("SELECT app_id FROM aplicacion WHERE app_nombre_largo = '{$_POST['app_nombre_largo']}'");
        if ($nombreExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre de aplicación ya está registrado'
            ]);
            exit;
        }
        
        // Verificar si el nombre corto ya existe
        $nombreCortoExistente = self::fetchFirst("SELECT app_id FROM aplicacion WHERE app_nombre_corto = '{$_POST['app_nombre_corto']}'");
        if ($nombreCortoExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre corto ya está registrado'
            ]);
            exit;
        }
        
        // Validación y formato de fecha para Informix
        if (empty($_POST['app_fecha_creacion'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La fecha de creación es requerida'
            ]);
            exit;
        }
        
        // Convertir fecha al formato que acepta Informix (MDY)
        $fecha = $_POST['app_fecha_creacion'];
        $fechaFormateada = date('m/d/Y', strtotime($fecha));
        $_POST['app_fecha_creacion'] = $fechaFormateada;
        
        try {
            $aplicacion = new Aplicaciones($_POST);
            $resultado = $aplicacion->crear();

            if($resultado['resultado'] == 1){
                
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Aplicación registrada correctamente',
                ]);
                
                exit;
            }else{
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al registrar la aplicación',
                    'datos' => $_POST,
                    'aplicacion' => $aplicacion,
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

    public static function buscarAplicaciones()
    {
        getHeadersApi();
        
        try {
            $sql = "SELECT * FROM aplicacion WHERE app_situacion = 1";
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

    public static function modificarAplicacion()
    {
        getHeadersApi();

        $id = $_POST['app_id'];

        // Sanitización de nombre largo
        $_POST['app_nombre_largo'] = ucwords(strtolower(trim(htmlspecialchars($_POST['app_nombre_largo']))));
        if (strlen($_POST['app_nombre_largo']) < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre largo debe tener más de 2 caracteres'
            ]);
            return;
        }

        // Sanitización de nombre mediano
        $_POST['app_nombre_medium'] = ucwords(strtolower(trim(htmlspecialchars($_POST['app_nombre_medium']))));
        if (strlen($_POST['app_nombre_medium']) < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre mediano debe tener más de 2 caracteres'
            ]);
            return;
        }

        // Sanitización de nombre corto
        $_POST['app_nombre_corto'] = ucwords(strtolower(trim(htmlspecialchars($_POST['app_nombre_corto']))));
        if (strlen($_POST['app_nombre_corto']) < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre corto debe tener más de 1 caracter'
            ]);
            return;
        }

        // Verificar duplicados (excluyendo la aplicación actual)
        $nombreExistente = self::fetchFirst("SELECT app_id FROM aplicacion WHERE app_nombre_largo = '{$_POST['app_nombre_largo']}' AND app_id != $id");
        if ($nombreExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre de aplicación ya está registrado'
            ]);
            return;
        }

        $nombreCortoExistente = self::fetchFirst("SELECT app_id FROM aplicacion WHERE app_nombre_corto = '{$_POST['app_nombre_corto']}' AND app_id != $id");
        if ($nombreCortoExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre corto ya está registrado'
            ]);
            return;
        }

        // Validación y formato de fecha para Informix
        if (empty($_POST['app_fecha_creacion'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La fecha de creación es requerida'
            ]);
            return;
        }
        
        // Convertir fecha al formato que acepta Informix (MDY)
        $fecha = $_POST['app_fecha_creacion'];
        $fechaFormateada = date('m/d/Y', strtotime($fecha));
        $_POST['app_fecha_creacion'] = $fechaFormateada;

        try {
            $data = Aplicaciones::find($id);
            $data->sincronizar([
                'app_nombre_largo' => $_POST['app_nombre_largo'],
                'app_nombre_medium' => $_POST['app_nombre_medium'],
                'app_nombre_corto' => $_POST['app_nombre_corto'],
                'app_fecha_creacion' => $_POST['app_fecha_creacion'],
                'app_situacion' => 1
            ]);
            
            $data->actualizar();
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información de la aplicación ha sido modificada exitosamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar aplicación',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAplicacion()
    {
        getHeadersApi();
        
        try {
            $id = filter_var($_POST['app_id'], FILTER_SANITIZE_NUMBER_INT);
            $consulta = "UPDATE aplicacion SET app_situacion = 0 WHERE app_id = $id";
            self::SQL($consulta);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Aplicación eliminada exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar aplicación',
                'detalle' => $e->getMessage()
            ]);
        }
    }
    
}