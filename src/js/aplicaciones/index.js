import { Dropdown } from "bootstrap"; //si utilizo dropdown en mi layaut  (Dropdown es un funcion interna del MVC)
import Swal from "sweetalert2"; //para utilizar las alertas
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones"; //(validarFormulario es un funcion interna del MVC)
import { lenguaje } from "../lenguaje"; //(lenguaje es un funcion interna del MVC)
import { error } from "jquery";

const FormAplicaciones = document.getElementById('formAplicacion');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscar = document.getElementById('BtnBuscar');
const BtnEliminar = document.getElementById('BtnEliminar');
const validarNombreLargo = document.getElementById('app_nombre_largo');
const validarNombreCorto = document.getElementById('app_nombre_corto');

const validacionNombreLargo = () => {

    const cantidadCaracteres = validarNombreLargo.value

    if (cantidadCaracteres.length < 1) {

        validarNombreLargo.classList.remove('is-valid', 'is-invalid');

    } else {
        if (cantidadCaracteres.length < 3) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice el nombre largo",
                text: "Debe tener más de 2 caracteres",
                showConfirmButton: false,
                timer: 3000
            })

            validarNombreLargo.classList.remove('is-valid');
            validarNombreLargo.classList.add('is-invalid');

        } else {
            validarNombreLargo.classList.remove('is-invalid');
            validarNombreLargo.classList.add('is-valid');
        }
    }

}

const validacionNombreCorto = () => {

    const cantidadCaracteres = validarNombreCorto.value

    if (cantidadCaracteres.length < 1) {

        validarNombreCorto.classList.remove('is-valid', 'is-invalid');

    } else {
        if (cantidadCaracteres.length < 2) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice el nombre corto",
                text: "Debe tener más de 1 caracter",
                showConfirmButton: false,
                timer: 3000
            })

            validarNombreCorto.classList.remove('is-valid');
            validarNombreCorto.classList.add('is-invalid');

        } else {
            validarNombreCorto.classList.remove('is-invalid');
            validarNombreCorto.classList.add('is-valid');
        }
    }

}

const GuardarAplicacion = async (event) => {
    event.preventDefault(); //evita el envio del formulario
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormAplicaciones, ['app_id'])) {
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
    const body = new FormData(FormAplicaciones);

    const url = '/login_2025/aplicaciones/guardar';
    const config = {
        method: 'POST',
        body
    }

    //tratar de guardar una aplicacion
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
            // BuscarAplicacion(); // Comentado para mantener tabla oculta

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


const datatable = new DataTable('#TableAplicaciones', {
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
            data: 'app_id',
            width: '%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { title: 'Nombre Largo', data: 'app_nombre_largo' },
        { title: 'Nombre Mediano', data: 'app_nombre_medium' },
        { title: 'Nombre Corto', data: 'app_nombre_corto' },
        { 
            title: 'Fecha Creación', 
            data: 'app_fecha_creacion',
            render: (data, type, row, meta) => {
                if (data && data !== null) {
                    return new Date(data).toLocaleDateString('es-GT');
                }
                return 'N/A';
            }
        },
        {
            title: 'Acciones',
            data: 'app_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-largo="${row.app_nombre_largo}"  
                         data-medium="${row.app_nombre_medium}"  
                         data-corto="${row.app_nombre_corto}"  
                         data-fecha="${row.app_fecha_creacion}"  
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

const BuscarAplicacion = async () =>{
    const url = '/login_2025/aplicaciones/buscar';
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

    document.getElementById('app_id').value = datos.id
    document.getElementById('app_nombre_largo').value = datos.largo
    document.getElementById('app_nombre_medium').value = datos.medium
    document.getElementById('app_nombre_corto').value = datos.corto
    document.getElementById('app_fecha_creacion').value = datos.fecha

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    })

}

const limpiarTodo = () => {
    FormAplicaciones.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');

}

const ModificarAplicacion = async (event) => {
    event.preventDefault(),
    BtnModificar.disabled = true;

    if (!validarFormulario(FormAplicaciones, [''])) {
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

    const body = new FormData(FormAplicaciones);

    const url = '/login_2025/aplicaciones/modificar';
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
            BuscarAplicacion();

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

const EliminarAplicacion = async (e) => {
    const idAplicacion = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: "Usted eliminara una aplicación",
        showConfirmButton: true,
        confirmButtonText: "Si",
        confirmButtonColor: "red",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!AlertaConfirmarEliminar.isConfirmed) return;

    // Preparamos el body para POST
    const body = new URLSearchParams();
    body.append('app_id', idAplicacion);

    try {
        const respuesta = await fetch('/login_2025/aplicaciones/eliminar', {
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
            BuscarAplicacion();
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
// BuscarAplicacion(); // Comentado para que la tabla no aparezca automáticamente
validarNombreLargo.addEventListener('change', validacionNombreLargo);
validarNombreCorto.addEventListener('change', validacionNombreCorto);

//guardar
FormAplicaciones.addEventListener('submit', GuardarAplicacion)

//btn limpiar
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarAplicacion);
BtnBuscar.addEventListener('click', BuscarAplicacion);

//datatable
datatable.on('click', '.eliminar', EliminarAplicacion);
datatable.on('click', '.modificar', llenarFormulario);