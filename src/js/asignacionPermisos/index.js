import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario, Toast } from "../funciones";
import { lenguaje } from "../lenguaje";

const formAsignacion = document.getElementById('formAsignacion');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscar = document.getElementById('BtnBuscar');
const selectApp = document.getElementById('asignacion_app_id');
const selectPermiso = document.getElementById('asignacion_permiso_id');
const textareaMotivo = document.getElementById('asignacion_motivo');

// Validación de caracteres en tiempo real para el motivo
const validarMotivo = () => {
    const texto = textareaMotivo.value;
    const contador = texto.length;
    
    if (contador < 1) {
        textareaMotivo.classList.remove('is-valid', 'is-invalid');
    } else if (contador < 10) {
        textareaMotivo.classList.remove('is-valid');
        textareaMotivo.classList.add('is-invalid');
    } else {
        textareaMotivo.classList.remove('is-invalid');
        textareaMotivo.classList.add('is-valid');
    }
}

// Cargar permisos cuando se selecciona una aplicación
const cargarPermisos = async () => {
    const appId = selectApp.value;
    
    if (!appId) {
        selectPermiso.innerHTML = '<option value="">Primero seleccione una aplicación</option>';
        selectPermiso.disabled = true;
        return;
    }

    try {
        const url = `/login_2025/asignacionPermisos/permisos?app_id=${appId}`;
        const config = {
            method: 'GET'
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo === 1) {
            selectPermiso.innerHTML = '<option value="">Seleccione un permiso</option>';
            
            data.forEach(permiso => {
                const option = document.createElement('option');
                option.value = permiso.permiso_id;
                option.textContent = `${permiso.permiso_nombre} - ${permiso.permiso_desc}`;
                selectPermiso.appendChild(option);
            });
            
            selectPermiso.disabled = false;
        } else {
            selectPermiso.innerHTML = '<option value="">No hay permisos disponibles</option>';
            selectPermiso.disabled = true;
            
            Toast.fire({
                icon: 'warning',
                title: mensaje
            });
        }

    } catch (error) {
        console.log(error);
        selectPermiso.innerHTML = '<option value="">Error al cargar permisos</option>';
        selectPermiso.disabled = true;
    }
}

// Guardar asignación de permiso
const guardarAsignacion = async e => {
    e.preventDefault();
    
    BtnGuardar.disabled = true;

    if (!validarFormulario(formAsignacion, ['asignacion_id'])) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe completar todos los campos",
            showConfirmButton: false,
            timer: 3000,
        });
        BtnGuardar.disabled = false;
        return;
    }

    try {
        const body = new FormData(formAsignacion);
        const url = "/login_2025/asignacionPermisos/guardar";
        const config = {
            method: 'POST',
            body
        };

        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        const { codigo, mensaje, detalle } = data;

        let icon = 'info';
        if (codigo == 1) {
            icon = 'success';
            formAsignacion.reset();
            selectPermiso.innerHTML = '<option value="">Primero seleccione una aplicación</option>';
            selectPermiso.disabled = true;
            buscarAsignaciones();
        } else if (codigo == 0) {
            icon = 'error';
            console.log(detalle);
        }

        Toast.fire({
            icon,
            title: mensaje
        });

    } catch (error) {
        console.log(error);
        Toast.fire({
            icon: 'error',
            title: 'Error de conexión'
        });
    }
    
    BtnGuardar.disabled = false;
}

// Configuración del DataTable
const datatable = new DataTable('#TableAsignaciones', {
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
            data: 'asignacion_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Usuario', 
            data: 'usuario_nombre',
            width: '15%'
        },
        { 
            title: 'Correo', 
            data: 'usuario_correo',
            width: '15%'
        },
        { 
            title: 'Aplicación', 
            data: 'app_nombre_corto',
            width: '10%'
        },
        { 
            title: 'Permiso', 
            data: 'permiso_nombre',
            width: '15%'
        },
        { 
            title: 'Descripción', 
            data: 'permiso_desc',
            width: '15%',
            render: (data, type, row, meta) => {
                if (data.length > 30) {
                    return data.substring(0, 30) + '...';
                }
                return data;
            }
        },
        { 
            title: 'Fecha', 
            data: 'asignacion_fecha',
            width: '10%',
            render: (data, type, row, meta) => {
                if (data && data !== null && data !== '') {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                }
                return 'Sin fecha';
            }
        },
        { 
            title: 'Asignado Por', 
            data: 'usuario_asigno_nombre',
            width: '15%'
        },
        {
            title: 'Motivo',
            data: 'asignacion_motivo',
            width: '10%',
            render: (data, type, row, meta) => {
                if (data.length > 20) {
                    return `<button class="btn btn-sm btn-outline-info ver-motivo" 
                            data-motivo="${data}"
                            data-usuario="${row.usuario_nombre}"
                            data-app="${row.app_nombre_medium}"
                            data-permiso="${row.permiso_nombre}"
                            data-fecha="${row.asignacion_fecha}">
                            Ver motivo
                        </button>`;
                }
                return data;
            }
        },
        {
            title: 'Estado',
            data: 'asignacion_situacion',
            width: '8%',
            render: (data, type, row, meta) => {
                if (data == 1) {
                    return '<span class="badge bg-success">Activo</span>';
                } else {
                    return '<span class="badge bg-danger">Retirado</span>';
                }
            }
        },
        {
            title: 'Acciones',
            data: 'asignacion_id',
            width: '10%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                if (row.asignacion_situacion == 1) {
                    return `
                    <div class='d-flex justify-content-center'>
                         <button class='btn btn-danger btn-sm retirar' 
                             data-id="${data}"
                             data-usuario="${row.usuario_nombre}"
                             data-permiso="${row.permiso_nombre}">
                            <i class="bi bi-shield-x me-1"></i>Retirar
                         </button>
                     </div>`;
                } else {
                    return '<span class="text-muted">Sin acciones</span>';
                }
            }
        }
    ],
});

// Buscar asignaciones
const buscarAsignaciones = async () => {
    const url = '/login_2025/asignacionPermisos/buscar';
    const config = {
        method: 'GET'
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo === 1) {
            document.getElementById('seccionTabla').classList.remove('d-none');
            
            Toast.fire({
                icon: 'success',
                title: mensaje
            });

            datatable.clear().draw();
            datatable.rows.add(data).draw();
            
        } else {
            Toast.fire({
                icon: 'info',
                title: mensaje
            });
        }
    } catch (error) {
        console.log(error);
        Toast.fire({
            icon: 'error',
            title: 'Error al cargar asignaciones'
        });
    }
}

// Mostrar modal con motivo completo
const mostrarMotivo = (event) => {
    const datos = event.currentTarget.dataset;
    
    document.getElementById('textoMotivo').textContent = datos.motivo;
    document.getElementById('modalUsuario').textContent = datos.usuario;
    document.getElementById('modalAplicacion').textContent = datos.app;
    document.getElementById('modalPermiso').textContent = datos.permiso;
    
    if (datos.fecha && datos.fecha !== 'null') {
        const fecha = new Date(datos.fecha);
        document.getElementById('modalFecha').textContent = fecha.toLocaleDateString('es-GT');
    } else {
        document.getElementById('modalFecha').textContent = 'No disponible';
    }
    
    // Usar Bootstrap 5 Modal
    const modalElement = document.getElementById('modalMotivo');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// Retirar permiso
const retirarPermiso = async (e) => {
    const idAsignacion = e.currentTarget.dataset.id;
    const usuario = e.currentTarget.dataset.usuario;
    const permiso = e.currentTarget.dataset.permiso;

    const confirmar = await Swal.fire({
        position: "center",
        icon: "warning",
        title: "¿Confirmar acción?",
        text: `¿Desea retirar el permiso "${permiso}" al usuario "${usuario}"?`,
        showConfirmButton: true,
        confirmButtonText: "Sí, retirar",
        confirmButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!confirmar.isConfirmed) return;

    try {
        const body = new URLSearchParams();
        body.append('asignacion_id', idAsignacion);

        const respuesta = await fetch('/login_2025/asignacionPermisos/retirar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body
        });

        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            Toast.fire({
                icon: 'success',
                title: mensaje
            });
            buscarAsignaciones();
        } else {
            Toast.fire({
                icon: 'error',
                title: mensaje
            });
        }

    } catch (error) {
        console.log(error);
        Toast.fire({
            icon: 'error',
            title: 'Error al retirar permiso'
        });
    }
};

// Limpiar formulario
const limpiarTodo = () => {
    formAsignacion.reset();
    selectPermiso.innerHTML = '<option value="">Primero seleccione una aplicación</option>';
    selectPermiso.disabled = true;
    textareaMotivo.classList.remove('is-valid', 'is-invalid');
}

// Event Listeners
selectApp.addEventListener('change', cargarPermisos);
textareaMotivo.addEventListener('input', validarMotivo);
formAsignacion.addEventListener('submit', guardarAsignacion);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnBuscar.addEventListener('click', buscarAsignaciones);

// DataTable events
datatable.on('click', '.ver-motivo', mostrarMotivo);
datatable.on('click', '.retirar', retirarPermiso);