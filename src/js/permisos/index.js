import { Dropdown } from "bootstrap"; //si utilizo dropdown en mi layaut  (Dropdown es un funcion interna del MVC)
import Swal from "sweetalert2"; //para utilizar las alertas
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones"; //(validarFormulario es un funcion interna del MVC)
import { lenguaje } from "../lenguaje"; //(lenguaje es un funcion interna del MVC)
import { error } from "jquery";

const FormPermisos = document.getElementById('formPermiso');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscar = document.getElementById('BtnBuscar');
const BtnEliminar = document.getElementById('BtnEliminar');
const validarNombre = document.getElementById('permiso_nombre');
const validarClave = document.getElementById('permiso_clave');
const selectAplicaciones = document.getElementById('permiso_app_id');

const validacionNombre = () => {

    const cantidadCaracteres = validarNombre.value

    if (cantidadCaracteres.length < 1) {

        validarNombre.classList.remove('is-valid', 'is-invalid');

    } else {
        if (cantidadCaracteres.length < 3) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice el nombre del permiso",
                text: "Debe tener más de 2 caracteres",
                showConfirmButton: false,
                timer: 3000
            })

            validarNombre.classList.remove('is-valid');
            validarNombre.classList.add('is-invalid');

        } else {
            validarNombre.classList.remove('is-invalid');
            validarNombre.classList.add('is-valid');
        }
    }

}

const validacionClave = () => {

    const cantidadCaracteres = validarClave.value

    if (cantidadCaracteres.length < 1) {

        validarClave.classList.remove('is-valid', 'is-invalid');

    } else {
        if (cantidadCaracteres.length < 3) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice la clave del permiso",
                text: "Debe tener más de 2 caracteres",
                showConfirmButton: false,
                timer: 3000
            })

            validarClave.classList.remove('is-valid');
            validarClave.classList.add('is-invalid');

        } else {
            validarClave.classList.remove('is-invalid');
            validarClave.classList.add('is-valid');
        }
    }

}

const CargarAplicaciones = async () => {
    const url = '/login_2025/permisos/aplicaciones';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config)
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo === 1) {
            selectAplicaciones.innerHTML = '<option value="">Seleccione una aplicación...</option>';
            
            data.forEach(aplicacion => {
                const option = document.createElement('option');
                option.value = aplicacion.app_id;
                option.textContent = `${aplicacion.app_nombre_corto} - ${aplicacion.app_nombre_largo}`;
                selectAplicaciones.appendChild(option);
            });
            
        } else {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Advertencia",
                text: "No hay aplicaciones disponibles",
                showConfirmButton: false,
                timer: 3000,
            });
        }
    } catch (error) {
        console.log(error);
    }
}

const GuardarPermiso = async (event) => {
    event.preventDefault(); //evita el envio del formulario
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormPermisos, ['permiso_id'])) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: false,
            timer: 3000
        });
        BtnGuardar.disabled = false;
        return;
    }

    //crea una instancia de la clase FormData
    const body = new FormData(FormPermisos);

    const url = '/login_2025/permisos/guardar';
    const config = {
        method: 'POST',
        body
    }

    //tratar de guardar un permiso
    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        const { codigo, mensaje } = datos
        console.log("Respuesta del servidor:", datos);
        if (codigo == 1) {

            Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });

            limpiarTodo();
            // BuscarPermiso(); // Comentado para mantener tabla oculta

        } else {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
            return;
        }
    } catch (error) {
        console.log(error)
    }
    BtnGuardar.disabled = false;

}


const datatable = new DataTable('#TablePermisos', {
    dom: `
        <"row mt-3 justify-content-between"
            <"col" l>
            <"col" B>
            <"col-3" f>
        >
        t
        <"row mt-3 justify-content-between"
            <"col-md-3 d-flex align-items-center" i> 
            <"col-md-8 d-flex justify-content-end" p>
        >
    `,
    language: lenguaje,
    data: [],
    columns: [
        {
            title: 'No.',
            data: 'permiso_id',
            width: '%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { title: 'Aplicación', data: 'app_nombre_corto' },
        { title: 'Nombre', data: 'permiso_nombre' },
        { title: 'Clave', data: 'permiso_clave' },
        { title: 'Descripción', data: 'permiso_desc' },
        { 
            title: 'Fecha', 
            data: 'permiso_fecha',
            render: (data, type, row, meta) => {
                if (data && data !== null) {
                    return new Date(data).toLocaleDateString('es-GT');
                }
                return 'N/A';
            }
        },
        { 
            title: 'Estado', 
            data: 'permiso_situacion',
            render: (data, type, row, meta) => {
                if (data == 1) {
                    return '<span class="badge bg-success">Activo</span>';
                } else {
                    return '<span class="badge bg-danger">Inactivo</span>';
                }
            }
        },
        {
            title: 'Acciones',
            data: 'permiso_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-app-id="${row.permiso_app_id}"  
                         data-nombre="${row.permiso_nombre}"  
                         data-clave="${row.permiso_clave}"  
                         data-desc="${row.permiso_desc}"  
                         data-fecha="${row.permiso_fecha}"  
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger eliminar mx-1' 
                         data-id="${data}">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ],
})

const BuscarPermiso = async () =>{
    const url = '/login_2025/permisos/buscar';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config)
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo ===1) {
            // Mostrar la sección de la tabla
            document.getElementById('seccionTabla').classList.remove('d-none');
            
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });

            datatable.clear().draw();
            datatable.rows.add(data).draw();
            
        } else {
            Swal.fire({
                position: "center",
                icon: "Info",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
            return;
        }
    } catch (error) {
        console.log(error);
        
    }

}

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('permiso_id').value = datos.id
    document.getElementById('permiso_app_id').value = datos.appId
    document.getElementById('permiso_nombre').value = datos.nombre
    document.getElementById('permiso_clave').value = datos.clave
    document.getElementById('permiso_desc').value = datos.desc
    document.getElementById('permiso_fecha').value = datos.fecha

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    })

}

const limpiarTodo = () => {
    FormPermisos.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');

}

const ModificarPermiso = async (event) => {
    event.preventDefault(),
    BtnModificar.disabled = true;

    if (!validarFormulario(FormPermisos, [''])) {
        Swal.fire({
                position: "center",
                icon: "warning",
                title: "FORMULARIO INCOMPLETO",
                text: "Debe de validar todos los campos",
                showConfirmButton: false,
                timer: 3000,
            });
            BtnModificar.disabled = false;
            return;        
    }

    const body = new FormData(FormPermisos);

    const url = '/login_2025/permisos/modificar';
    const config = {
        method: 'POST',
        body
    }

    try {
        
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

        if (codigo === 1) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });

            limpiarTodo();
            BuscarPermiso();

        } else {
            Swal.fire({
                position: "center",
                icon: "Info",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
            return;
        }

    } catch (error) {
        console.log(error);
    }
    BtnModificar.disabled = false;
}

const EliminarPermiso = async (e) => {
    const idPermiso = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: "Usted eliminara un permiso",
        showConfirmButton: true,
        confirmButtonText: "Si",
        confirmButtonColor: "red",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!AlertaConfirmarEliminar.isConfirmed) return;

    // Preparamos el body para POST
    const body = new URLSearchParams();
    body.append('permiso_id', idPermiso);

    try {
        const respuesta = await fetch('/login_2025/permisos/eliminar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body
        });

        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje
            });
            BuscarPermiso();
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 1000
            });
        }

    } catch (error) {
        console.log(error);
    }
};


//Eventos
CargarAplicaciones(); // Cargar aplicaciones al iniciar
// BuscarPermiso(); // Comentado para que la tabla no aparezca automáticamente
validarNombre.addEventListener('change', validacionNombre);
validarClave.addEventListener('change', validacionClave);

//guardar
FormPermisos.addEventListener('submit', GuardarPermiso)

//btn limpiar
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarPermiso);
BtnBuscar.addEventListener('click', BuscarPermiso);

//datatable
datatable.on('click', '.eliminar', EliminarPermiso);
datatable.on('click', '.modificar', llenarFormulario);