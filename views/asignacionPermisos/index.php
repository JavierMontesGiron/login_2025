<div class="container-fluid">
  <!-- Sección del Formulario -->
  <section class="row justify-content-center mb-5">
    <div class="col-12 col-md-10 col-lg-8 col-xl-7">
      <div class="card" style="border-radius: 15px;">
        <div class="card-body p-5">
          <h2 class="text-uppercase text-center mb-5">Asignación de Permisos</h2>
          <form id="formAsignacion" name="formAsignacion">
            <!-- Campo oculto para ID (necesario para modificaciones) -->
            <input type="hidden" id="asignacion_id" name="asignacion_id">
            
            <div class="row">
              <div class="col-md-12">
                <div data-mdb-input-init class="form-outline mb-4">
                  <select id="asignacion_usuario_id" name="asignacion_usuario_id" class="form-select form-select-lg" required>
                    <option value="">Seleccione un usuario</option>
                    <?php foreach($usuarios as $usuario): ?>
                      <option value="<?= $usuario['usuario_id'] ?>">
                        <?= $usuario['usuario_nom1'] . ' ' . $usuario['usuario_nom2'] . ' ' . $usuario['usuario_ape1'] . ' ' . $usuario['usuario_ape2'] ?> - <?= $usuario['usuario_correo'] ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <label class="form-label" for="asignacion_usuario_id">Usuario</label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div data-mdb-input-init class="form-outline mb-4">
                  <select id="asignacion_app_id" name="asignacion_app_id" class="form-select form-select-lg" required>
                    <option value="">Seleccione una aplicación</option>
                    <?php foreach($aplicaciones as $aplicacion): ?>
                      <option value="<?= $aplicacion['app_id'] ?>">
                        <?= $aplicacion['app_nombre_medium'] ?> (<?= $aplicacion['app_nombre_corto'] ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <label class="form-label" for="asignacion_app_id">Aplicación</label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div data-mdb-input-init class="form-outline mb-4">
                  <select id="asignacion_permiso_id" name="asignacion_permiso_id" class="form-select form-select-lg" required disabled>
                    <option value="">Primero seleccione una aplicación</option>
                  </select>
                  <label class="form-label" for="asignacion_permiso_id">Permiso</label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div data-mdb-input-init class="form-outline mb-4">
                  <select id="asignacion_usuario_asigno" name="asignacion_usuario_asigno" class="form-select form-select-lg" required>
                    <option value="">Seleccione quién asigna</option>
                    <?php foreach($usuariosAsignan as $usuario): ?>
                      <option value="<?= $usuario['usuario_id'] ?>">
                        <?= $usuario['usuario_nom1'] . ' ' . $usuario['usuario_nom2'] . ' ' . $usuario['usuario_ape1'] . ' ' . $usuario['usuario_ape2'] ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <label class="form-label" for="asignacion_usuario_asigno">Usuario que Asigna</label>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div data-mdb-input-init class="form-outline mb-4">
                  <textarea id="asignacion_motivo" name="asignacion_motivo" class="form-control form-control-lg" rows="4" maxlength="250" required placeholder="Explique el motivo de la asignación de este permiso..."></textarea>
                  <label class="form-label" for="asignacion_motivo">Motivo de la Asignación</label>
                  <div class="form-text">Mínimo 10 caracteres, máximo 250</div>
                </div>
              </div>
            </div>



            <!-- Botones de acción -->
            <div class="d-flex justify-content-center gap-2 flex-wrap">
              <button type="submit" id="BtnGuardar" 
                class="btn btn-success btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-shield-plus me-2"></i>Asignar Permiso
              </button>
              
              <button type="button" id="BtnBuscar" 
                class="btn btn-info btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-search me-2"></i>Buscar Asignaciones
              </button>
              
              <button type="button" id="BtnLimpiar" 
                class="btn btn-secondary btn-lg" 
                data-mdb-button-init data-mdb-ripple-init>
                <i class="bi bi-arrow-clockwise me-2"></i>Limpiar
              </button>
            </div>

            <p class="text-center text-muted mt-5 mb-0">
              <i class="bi bi-shield-check me-2"></i>
              Sistema de Gestión de Permisos
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
          <h4 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Asignaciones de Permisos</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="TableAsignaciones" class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Usuario</th>
                  <th>Correo</th>
                  <th>Aplicación</th>
                  <th>Permiso</th>
                  <th>Descripción</th>
                  <th>Fecha Asignación</th>
                  <th>Asignado Por</th>
                  <th>Motivo</th>
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

  <!-- Modal para ver motivo completo -->
  <div class="modal fade" id="modalMotivo" tabindex="-1" aria-labelledby="modalMotivoLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalMotivoLabel">
            <i class="bi bi-info-circle me-2"></i>Motivo de Asignación
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p id="textoMotivo"></p>
          <div class="mt-3">
            <small class="text-muted">
              <strong>Usuario:</strong> <span id="modalUsuario"></span><br>
              <strong>Aplicación:</strong> <span id="modalAplicacion"></span><br>
              <strong>Permiso:</strong> <span id="modalPermiso"></span><br>
              <strong>Fecha:</strong> <span id="modalFecha"></span>
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= asset('./build/js/asignacionPermisos/index.js') ?>"></script>