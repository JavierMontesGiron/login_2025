<div class="container-fluid">
  <!-- Sección del Formulario -->
  <section class="row justify-content-center mb-5">
    <div class="col-12 col-md-10 col-lg-8 col-xl-7">
      <div class="card" style="border-radius: 15px;">
        <div class="card-body p-5">
          <h2 class="text-uppercase text-center mb-5">Gestión de Usuarios</h2>
          <form id="formUsuario" name="formUsuario">
            <!-- Campo oculto para ID (necesario para modificaciones) -->
            <input type="hidden" id="usuario_id" name="usuario_id">
            
            <div class="row">
              <div class="col-md-6">
                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="text" id="usuario_nom1" name="usuario_nom1" class="form-control form-control-lg" required />
                  <label class="form-label" for="usuario_nom1">Primer Nombre</label>
                </div>
              </div>
              <div class="col-md-6">
                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="text" id="usuario_nom2" name="usuario_nom2" class="form-control form-control-lg"  />
                  <label class="form-label" for="usuario_nom2">Segundo Nombre</label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="text" id="usuario_ape1" name="usuario_ape1" class="form-control form-control-lg" required />
                  <label class="form-label" for="usuario_ape1">Primer Apellido</label>
                </div>
              </div>
              <div class="col-md-6">
                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="text" id="usuario_ape2" name="usuario_ape2" class="form-control form-control-lg" required />
                  <label class="form-label" for="usuario_ape2">Segundo Apellido</label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="tel" id="usuario_tel" name="usuario_tel" class="form-control form-control-lg" maxlength="8" required />
                  <label class="form-label" for="usuario_tel">Teléfono</label>
                </div>
              </div>
              <div class="col-md-6">
                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="text" id="usuario_dpi" name="usuario_dpi" class="form-control form-control-lg" maxlength="13" required />
                  <label class="form-label" for="usuario_dpi">DPI</label>
                </div>
              </div>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="text" id="usuario_direc" name="usuario_direc" class="form-control form-control-lg" maxlength="150" required />
              <label class="form-label" for="usuario_direc">Dirección</label>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="email" id="usuario_correo" name="usuario_correo" class="form-control form-control-lg" maxlength="100" required />
              <label class="form-label" for="usuario_correo">Correo Electrónico</label>
            </div>

            <!-- Campos de contraseña -->
            <div id="password-fields">
              <div data-mdb-input-init class="form-outline mb-4">
                <input type="password" id="usuario_contra" name="usuario_contra" class="form-control form-control-lg" required />
                <label class="form-label" for="usuario_contra">Contraseña</label>
              </div>

              <div data-mdb-input-init class="form-outline mb-4">
                <input type="password" id="confirmar_contra" name="confirmar_contra" class="form-control form-control-lg" required />
                <label class="form-label" for="confirmar_contra">Confirmar Contraseña</label>
              </div>
            </div>

            <div data-mdb-input-init class="form-outline mb-4">
              <input type="file" id="usuario_fotografia" name="usuario_fotografia" class="form-control form-control-lg" accept="image/*" />
              <label class="form-label" for="usuario_fotografia">Fotografía (Opcional)</label>
            </div>

            <!-- Términos y condiciones -->
            <div class="form-check d-flex justify-content-center mb-5" id="terms-section">
              <input class="form-check-input me-2" type="checkbox" value="" id="terminos_condiciones" required />
              <label class="form-check-label" for="terminos_condiciones">
                Acepto todos los términos y condiciones del <a href="#!" class="text-body"><u>Servicio</u></a>
              </label>
            </div>

            <!-- Botones de acción -->
            <div class="d-flex justify-content-center gap-2 flex-wrap">
              <button type="submit" id="BtnGuardar" 
                class="btn btn-success btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-person-plus me-2"></i>Registrar Usuario
              </button>
              
              <button type="button" id="BtnModificar" 
                class="btn btn-warning btn-lg d-none" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-pencil-square me-2"></i>Modificar Usuario
              </button>
              
              <button type="button" id="BtnBuscar" 
                class="btn btn-info btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-search me-2"></i>Buscar Usuarios
              </button>
              
              <button type="button" id="BtnLimpiar" 
                class="btn btn-secondary btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-arrow-clockwise me-2"></i>Limpiar
              </button>
            </div>

            <p class="text-center text-muted mt-5 mb-0">
              ¿Ya tienes una cuenta? 
              <a href="#!" class="fw-bold text-body"><u>Inicia sesión aquí</u></a>
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
          <h4 class="mb-0"><i class="bi bi-people me-2"></i>Lista de Usuarios</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="TableUsuarios" class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Foto</th>
                  <th>Primer Nombre</th>
                  <th>Segundo Nombre</th>
                  <th>Primer Apellido</th>
                  <th>Segundo Apellido</th>
                  <th>Correo</th>
                  <th>Teléfono</th>
                  <th>DPI</th>
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

<script src="<?= asset('./build/js/registro/index.js') ?>"></script>