<div class="container-fluid">
  <!-- Sección del Formulario -->
  <section class="row justify-content-center mb-5">
    <div class="col-12 col-md-10 col-lg-8 col-xl-7">
      <div class="card" style="border-radius: 15px;">
        <div class="card-body p-5">
          <h2 class="text-uppercase text-center mb-5">Gestión de Permisos</h2>
          <form id="formPermiso" name="formPermiso">
            <!-- Campo oculto para ID (necesario para modificaciones) -->
            <input type="hidden" id="permiso_id" name="permiso_id">
            
            <!-- Select de Aplicaciones -->
            <div data-mdb-input-init class="form-outline mb-4">
              <select id="permiso_app_id" name="permiso_app_id" class="form-select form-control-lg" required>
                <option value="">Seleccione una aplicación...</option>
              </select>
              <label class="form-label" for="permiso_app_id">Aplicación</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="text" id="permiso_nombre" name="permiso_nombre" class="form-control form-control-lg" maxlength="150" required />
              <label class="form-label" for="permiso_nombre">Nombre del Permiso</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="text" id="permiso_clave" name="permiso_clave" class="form-control form-control-lg" maxlength="250" required />
              <label class="form-label" for="permiso_clave">Clave del Permiso</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <textarea id="permiso_desc" name="permiso_desc" class="form-control form-control-lg" maxlength="250" rows="3" required></textarea>
              <label class="form-label" for="permiso_desc">Descripción del Permiso</label>
            </div>

            <!-- Campo de fecha -->
            <div data-mdb-input-init class="form-outline mb-4">
              <input type="date" id="permiso_fecha" name="permiso_fecha" class="form-control form-control-lg" required />
              <label class="form-label" for="permiso_fecha">Fecha de Creación</label>
            </div>

            <!-- Botones de acción -->
            <div class="d-flex justify-content-center gap-2 flex-wrap">
              <button type="submit" id="BtnGuardar" 
                class="btn btn-success btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-shield-plus me-2"></i>Registrar Permiso
              </button>
              
              <button type="button" id="BtnModificar" 
                class="btn btn-warning btn-lg d-none" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-pencil-square me-2"></i>Modificar Permiso
              </button>
              
              <button type="button" id="BtnBuscar" 
                class="btn btn-info btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-search me-2"></i>Buscar Permisos
              </button>
              
              <button type="button" id="BtnLimpiar" 
                class="btn btn-secondary btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-arrow-clockwise me-2"></i>Limpiar
              </button>
            </div>

            <p class="text-center text-muted mt-5 mb-0">
              <a href="/login_2025/aplicaciones" class="fw-bold text-body me-3"><u>Ir a Aplicaciones</u></a>
              <a href="/login_2025/usuarios" class="fw-bold text-body"><u>Ir a Usuarios</u></a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección de la Tabla (Oculta inicialmente) -->
  <section class="row d-none" id="seccionTabla">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0"><i class="bi bi-shield-check me-2"></i>Lista de Permisos</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="TablePermisos" class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Aplicación</th>
                  <th>Nombre Permiso</th>
                  <th>Clave</th>
                  <th>Descripción</th>
                  <th>Fecha</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <!-- DataTable llenará automáticamente -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script src="<?= asset('./build/js/permisos/index.js') ?>"></script>