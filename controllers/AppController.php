<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use MVC\Router;

class AppController
{
    public static function index(Router $router)
    {
        $router->render('login/index', [], 'layouts/login');
    }

    public static function login()
{
    getHeadersApi(); // Esta línea debe estar AL INICIO

    try {
        $usario = filter_var($_POST['usu_codigo'], FILTER_SANITIZE_NUMBER_INT);
        $constrasena = htmlspecialchars($_POST['usu_password']);

        $queyExisteUser = "SELECT usuario_id, usuario_nom1, usuario_contra FROM usuario WHERE usuario_dpi = $usario AND usuario_situacion = 1";

        $resultado = ActiveRecord::fetchArray($queyExisteUser);
        $ExisteUsuario = !empty($resultado) ? $resultado[0] : null;

        if ($ExisteUsuario) {
            $passDB = $ExisteUsuario['usuario_contra'];

            if (password_verify($constrasena, $passDB)) {
                $nombreUser = $ExisteUsuario['usuario_nom1'];
                $idUsuario = $ExisteUsuario['usuario_id'];

                $_SESSION['login'] = true;
                $_SESSION['auth_user'] = true;
                $_SESSION['nombre'] = $nombreUser;
                $_SESSION['dpi'] = $usario;
                $_SESSION['usuario_id'] = $idUsuario;

                $sqlpermisos = "SELECT permiso_clave as permiso from asig_permisos inner join permiso on asignacion_permiso_id = permiso_id where asignacion_usuario_id = $idUsuario AND asignacion_situacion = 1";

                $permisos = ActiveRecord::fetchArray($sqlpermisos);

                foreach ($permisos as $key => $value) {
                   $_SESSION[$value['permiso']] = 1; 
                }

                // ✅ IMPORTANTE: Solo este echo, nada más
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario logueado exitosamente',
                ]);
                exit; // ✅ AGREGAR EXIT para evitar output adicional
            } else {
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La contraseña que ingresó es incorrecta',
                ]);
                exit;
            }
        } else {
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El usuario que intenta loguearse NO EXISTE',
            ]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode([
            'codigo' => 0,
            'mensaje' => 'Error al intentar loguearse',
            'detalle' => $e->getMessage()
        ]);
        exit;
    }
}

    public static function logout()
    {
        isAuth();
        $_SESSION = [];
        $login = $_ENV['APP_NAME'];
        header("Location: /$login");
    }

    public static function renderInicio(Router $router){
        isAuth(); // Solo verificar que esté logueado
        
        $router->render('pages/index', [], 'layouts/menu');
    }
}