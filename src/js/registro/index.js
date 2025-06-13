import { Dropdown } from "bootstrap"; //si utilizo dropdown en mi layaut  (Dropdown es un funcion interna del MVC)
import Swal from "sweetalert2"; //para utilizar las alertas
import DataTable from "datatables.net-bs5";
import { validarFormulario, Toast } from "../funciones"; //(validarFormulario y Toast son funciones internas del MVC)
import { lenguaje } from "../lenguaje"; //(lenguaje es un funcion interna del MVC)
import { error } from "jquery";

const formUsuario = document.getElementById('formUsuario');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscar = document.getElementById('BtnBuscar');
const BtnEliminar = document.getElementById('BtnEliminar');
const validarTelefono = document.getElementById('usuario_tel');
const validarDpi = document.getElementById('usuario_dpi');

const validacionTelefono = () => {

    const cantidadDigitos = validarTelefono.value

    if (cantidadDigitos.length < 1) {

        validarTelefono.classList.remove('is-valid', 'is-invalid');

    } else {
        if (cantidadDigitos.length < 8) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice el número de telefono",
                text: "La cantidad de digitos debe de ser igual a 8 digitos",
                showConfirmButton: false,
                timer: 3000
            })

            validarTelefono.classList.remove('is-valid');
            validarTelefono.classList.add('is-invalid');

        } else {
            validarTelefono.classList.remove('is-invalid');
            validarTelefono.classList.add('is-valid');
        }
    }

}

const validacionDpi = () => {

    const cantidadDigitos = validarDpi.value

    if (cantidadDigitos.length < 1) {

        validarDpi.classList.remove('is-valid', 'is-invalid');

    } else {
        if (cantidadDigitos.length < 13 || cantidadDigitos.length > 13) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice el número de DPI",
                text: "La cantidad de digitos debe de ser igual a 13 digitos",
                showConfirmButton: false,
                timer: 3000
            })

            validarDpi.classList.remove('is-valid');
            validarDpi.classList.add('is-invalid');

        } else {
            validarDpi.classList.remove('is-invalid');
            validarDpi.classList.add('is-valid');
        }
    }

}

const guardarUsuario = async e => {
  e.preventDefault();
  
  try {

    const body = new FormData(formUsuario)
    const url = "/login_2025/usuarios/guardar"
    const config = {
      method: 'POST',
      body
    }

    const respuesta = await fetch(url, config);
    const data = await respuesta.json();
    const { codigo, mensaje, detalle } = data;
        console.log(data)
    let icon = 'info'
    if (codigo == 1) {
      icon = 'success'
      formUsuario.reset()
      BuscarUsuario();

    } else if (codigo == 2) {
      icon = 'warning'

      console.log(detalle);
    } else if (codigo == 0) {
      icon = 'error'
      console.log(detalle);

    }

    Toast.fire({
      icon,
      title: mensaje
    })

  } catch (error) {
    console.log(error);
  }

}


const datatable = new DataTable('#TableUsuarios', {
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
            data: 'usuario_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        {
            title: 'Foto',
            data: 'usuario_fotografia',
            width: '10%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                if (data && data !== null && data !== '') {
                    // Cambiar la ruta para que apunte correctamente
                    return `<img src="/login_2025/public/${data}" alt="Foto usuario" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiBmaWxsPSIjNjY2NjY2Ii8+CjxwYXRoIGQ9Ik0yNSAyM0MyNy43NjE0IDIzIDMwIDIwLjc2MTQgMzAgMThDMzAgMTUuMjM4NiAyNy43NjE0IDEzIDI1IDEzQzIyLjIzODYgMTMgMjAgMTUuMjM4NiAyMCAxOEMyMCAyMC43NjE0IDIyLjIzODYgMjMgMjUgMjNaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMjUgMjVDMjAuMDI5NCAyNSAxNiAyOS4wMjk0IDE2IDM0VjM3SDM0VjM0QzM0IDI5LjAyOTQgMjkuOTcwNiAyNSAyNSAyNVoiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPgo=';">`;
                } else {
                    return `<div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-person text-white"></i>
                            </div>`;
                }
            }
        },
        { title: 'Primer Nombre', data: 'usuario_nom1' },
        { title: 'Segundo Nombre', data: 'usuario_nom2' },
        { title: 'Primer Apellido', data: 'usuario_ape1' },
        { title: 'Segundo Apellido', data: 'usuario_ape2' },
        { title: 'Correo', data: 'usuario_correo' },
        { title: 'Telefono', data: 'usuario_tel' },
        { title: 'DPI', data: 'usuario_dpi' },
        {
            title: 'Acciones',
            data: 'usuario_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-nom1="${row.usuario_nom1}"  
                         data-nom2="${row.usuario_nom2}"  
                         data-ape1="${row.usuario_ape1}"  
                         data-ape2="${row.usuario_ape2}"  
                         data-tel="${row.usuario_tel}"  
                         data-direc="${row.usuario_direc}"  
                         data-dpi="${row.usuario_dpi}"  
                         data-correo="${row.usuario_correo}"  
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

const BuscarUsuario = async () =>{
    const url = '/login_2025/usuarios/buscar';
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

    document.getElementById('usuario_id').value = datos.id
    document.getElementById('usuario_nom1').value = datos.nom1
    document.getElementById('usuario_nom2').value = datos.nom2
    document.getElementById('usuario_ape1').value = datos.ape1
    document.getElementById('usuario_ape2').value = datos.ape2
    document.getElementById('usuario_tel').value = datos.tel
    document.getElementById('usuario_direc').value = datos.direc
    document.getElementById('usuario_dpi').value = datos.dpi
    document.getElementById('usuario_correo').value = datos.correo

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    })

}

const limpiarTodo = () => {
    formUsuario.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');

}

const ModificarUsuario = async (event) => {
    event.preventDefault(),
    BtnModificar.disabled = true;

    if (!validarFormulario(formUsuario, [''])) {
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

    const body = new FormData(formUsuario);

    const url = '/login_2025/usuarios/modificar';
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
            BuscarUsuario();

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

const EliminarUsuario = async (e) => {
    const idUsuario = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: "Usted eliminara un usuario",
        showConfirmButton: true,
        confirmButtonText: "Si",
        confirmButtonColor: "red",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!AlertaConfirmarEliminar.isConfirmed) return;

    // Preparamos el body para POST
    const body = new URLSearchParams();
    body.append('usuario_id', idUsuario);

    try {
        const respuesta = await fetch('/login_2025/usuarios/eliminar', {
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
            BuscarUsuario();
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
// BuscarUsuario(); // Comentamos esta línea para que no se ejecute automáticamente
validarTelefono.addEventListener('change', validacionTelefono);
validarDpi.addEventListener('change', validacionDpi);

//guardar
formUsuario.addEventListener('submit', guardarUsuario)

//btn limpiar
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarUsuario);
BtnBuscar.addEventListener('click', BuscarUsuario);

//datatable
datatable.on('click', '.eliminar', EliminarUsuario);
datatable.on('click', '.modificar', llenarFormulario);