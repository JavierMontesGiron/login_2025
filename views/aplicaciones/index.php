<div class="container-fluid">
  <!-- Sección del Formulario -->
  <section class="row justify-content-center mb-5">
    <div class="col-12 col-md-10 col-lg-8 col-xl-7">
      <div class="card" style="border-radius: 15px;">
        <div class="card-body p-5">
          <h2 class="text-uppercase text-center mb-5">Gestión de Aplicaciones</h2>
          <form id="formAplicacion" name="formAplicacion">
            <!-- Campo oculto para ID (necesario para modificaciones) -->
            <input type="hidden" id="app_id" name="app_id">
            
            <div data-mdb-input-init class="form-outline mb-4">
              <input type="text" id="app_nombre_largo" name="app_nombre_largo" class="form-control form-control-lg" maxlength="250" required />
              <label class="form-label" for="app_nombre_largo">Nombre Largo de la Aplicación</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="text" id="app_nombre_medium" name="app_nombre_medium" class="form-control form-control-lg" maxlength="150" required />
              <label class="form-label" for="app_nombre_medium">Nombre Mediano</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="text" id="app_nombre_corto" name="app_nombre_corto" class="form-control form-control-lg" maxlength="50" required />
              <label class="form-label" for="app_nombre_corto">Nombre Corto</label>
            </div>

            <!-- Campo de fecha de creación (editable) -->
            <div data-mdb-input-init class="form-outline mb-4">
              <input type="date" id="app_fecha_creacion" name="app_fecha_creacion" class="form-control form-control-lg" required />
              <label class="form-label" for="app_fecha_creacion">Fecha de Creación</label>
            </div>

            <!-- Botones de acción -->
            <div class="d-flex justify-content-center gap-2 flex-wrap">
              <button type="submit" id="BtnGuardar" 
                class="btn btn-success btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-plus-circle me-2"></i>Registrar Aplicación
              </button>
              
              <button type="button" id="BtnModificar" 
                class="btn btn-warning btn-lg d-none" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-pencil-square me-2"></i>Modificar Aplicación
              </button>
              
              <button type="button" id="BtnBuscar" 
                class="btn btn-info btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-search me-2"></i>Buscar Aplicaciones
              </button>
              
              <button type="button" id="BtnLimpiar" 
                class="btn btn-secondary btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-arrow-clockwise me-2"></i>Limpiar
              </button>
            </div>

            <p class="text-center text-muted mt-5 mb-0">
              <a href="/login_2025/usuarios" class="fw-bold text-body"><u>Ir a Gestión de Usuarios</u></a>
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
          <h4 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Lista de Aplicaciones</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="TableAplicaciones" class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Nombre Largo</th>
                  <th>Nombre Mediano</th>
                  <th>Nombre Corto</th>
                  <th>Fecha Creación</th>
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

<script src="<?= asset('./build/js/aplicaciones/index.js') ?>"></script>