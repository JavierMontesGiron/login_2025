<H1 class="text-center">BIENVENIDO AL SISTEMA <?= $_SESSION['nombre']?></H1>

<?php

                echo $_SESSION['nombre'] ;
                echo "   ";
                var_dump($_SESSION) ;
?>
        <script src="build/js/inicio.js"></script>