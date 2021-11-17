<?php
	session_start();
	$params = parse_ini_file('dist/config.ini');
	if ($params === false) {
		$titulo = '';
	}
	$titulo = $params['title'];
	if (!isset($_SESSION['usuario']) || $_SESSION['usuario']==='') {
		header("Location: /");
	} else {
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php echo $titulo; ?></title>
		<!-- Tell the browser to be responsive to screen width -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- Icon Favicon -->
		<link rel="shortcut icon" href="dist/img/favicon.png">
		
		<!-- Font Awesome -->
		<link rel="stylesheet" href="plugins/fontawesome/css/all.css">
		
		<!-- bootstrap-select -->
		<link rel="stylesheet" href="plugins/bootstrap-select/css/bootstrap-select.css">
		<!-- Datepicker Bootstrap -->
		<link rel="stylesheet" href="plugins/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.min.css" class="rel">
		
		<!-- DataTables -->
		<link rel="stylesheet" href="plugins/datatables/buttons.dataTables.min.css">
		<link rel="stylesheet" href="plugins/datatables/jquery.dataTables.min.css">
		<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap4.css">

		<!-- Theme style -->
		<link rel="stylesheet" href="dist/css/adminlte.css">

		<style>
			.loader {
				background-image:linear-gradient(#06C90F 0%, #D5D800 100%);
				width:50px;
				height:50px;
				border-radius: 50%;
				margin: 0px;
				padding: 0px;
				-webkit-animation: spin 1s linear infinite;
				animation: spin 1s linear infinite;
				opacity: 1;
				filter:alpha(opacity=100);
			}

			.scroller::-webkit-scrollbar {
				width: 8px;
			}

			.scroller::-webkit-scrollbar-track {
				background: white;
			}

			.scroller::-webkit-scrollbar-thumb {
				background: #7f7f7f;
				border-right: 1px solid white;
			}

			.txtcomp {
				letter-spacing: -0.8px;
				line-height: 1em;
			}

			/* Safari */
			@-webkit-keyframes spin {
				0% { -webkit-transform: rotate(0deg); }
				100% { -webkit-transform: rotate(360deg); }
			}

			@keyframes spin {
				0% { transform: rotate(0deg); }
				100% { transform: rotate(360deg); }
			}

			.bgtransparent{
				position:fixed;
				left:0;
				top:0;
				background-color:#000;
				opacity:0.6;
				filter:alpha(opacity=60);
				z-index: 8886;
				display: none;
				width: 1px;
				height: 1px;
			}

			.bgmodal{
				position:fixed;
				font-family:arial;
				font-size:1em;
				border:0.05em solid black;
				overflow:auto;
				background-color:#FFFFFF;
			}

			.nav-treeview {
				border-radius: 0px 0px 10px 10px; 
				background-color: #000 !important; 
				color: white;
			}

			.btn-cal {
				padding: 0.4rem!important;
				background-color: #DDDADA;
				vertical-align: middle;
				text-align: center;
				border-radius: 3px;
				border: 1px solid #C4C4C4;
				cursor: pointer;
			}

			input {
				text-transform: uppercase;
			}

			::-webkit-input-placeholder { /* Chrome/Opera/Safari */
				font-style: italic;
				font-size: 80%;
				text-transform: initial;
			}

			::-moz-placeholder { /* Firefox 19+ */
				font-style: italic;
				font-size: 80%;
				text-transform: initial;
			}

			:-ms-input-placeholder { /* IE 10+ */
				font-style: italic;
				font-size: 80%;
				text-transform: initial;
			}
			
			:-moz-placeholder { /* Firefox 18- */
				font-style: italic;
				font-size: 80%;
				text-transform: initial;
			}

			@media (min-width: 5px) and (max-width: 319px) {
				.mopcion {display: block;}
				.titulo {font-size: 115%; line-height: 1em; margin: 0px; padding: 0px;}
				.imgmain {display: none;}
				.imgmain2 {display: '';}
				.content {margin-top: 55px;}
			}

			@media (min-width: 320px) and (max-width: 424px) {
				.mopcion {display: block;}
				.titulo {font-size: 120%; line-height: 1em; margin: 0px; padding: 0px;}
				.imgmain {display: none;}
				.imgmain2 {display: '';}
				.content {margin-top: 62px;}
			}

			@media (min-width: 425px) and (max-width: 767px) {
				.mopcion {display: block;}
				.titulo {font-size: 125%; line-height: 1em; margin: 0px; padding: 0px;}
				.imgmain {display: none;}
				.imgmain2 {display: '';}
				.content {margin-top: 68px;}
			}

			@media (min-width: 425px) and (max-width: 767px) and (orientation:landscape) {
				.mopcion {display: block;}
				.titulo {font-size: 130%; line-height: 1em; margin: 0px; padding: 0px;}
				.imgmain {display: none;}
				.imgmain2 {display: '';}
				.content {margin-top: 58px;}
			}

			@media (aspect-ratio: 1/1) {
				.content {margin-top: 58px;}
				.titulo {font-size: 140%; line-height: 1em; margin: 0px; padding: 0px;}
			}

			@media (min-width: 768px) and (max-width: 991px) {
				.mopcion {display: block;}
				.titulo {font-size: 140%; line-height: 1em; margin: 0px; padding: 0px;}
				.imgmain {display: none;}
				.imgmain2 {display: '';}
				.content {margin-top: 55px;}
			}

			@media (min-width: 992px) {
				.mopcion {display: none;}
				.titulo {font-size: 140%; line-height: 1em; margin: 0px; padding: 0px;}
				.imgmain {display: block;}
				.imgmain2 {display: none;}
				.content {margin-top: 60px;}
			}

			.modal-backdrop {
				z-index: 8888;
			}

			.custom-control-label {
				margin-bottom: 0;
				cursor: pointer;
			}

			.mbadge {
				text-align: center;
				font-size:80%;
				font-weight:400;
				line-height: 1em;
				border-radius: .25rem;
				letter-spacing: -0.5px;
				margin: 3px;
				padding: 5px;
				cursor: default;
			}

			table.dataTable tbody td, thead th, tfoot th {
				font-weight: lighter !important;
			}
			table.dataTable tbody td {
				height: 36px;
				padding: 2px;
			}
			table.dataTable tfoot th, table.dataTable thead th {
				padding: 0px 2px 0px 2px;
				height: 30px;
				vertical-align: middle;
				text-align: center;
			}
			table.dataTable thead td {
				padding: 0px;
				margin: 0px;
				height: 18px;
				vertical-align: middle;
				text-align: center;
				line-height: 0px;
				font-weight: lighter !important;
			}
			table.table-striped tbody tr {
				background-color: #D5D9E2;
			}
			table.table-hover tbody tr:hover {
				background-color: #BFC7D4;
			}

			.dataTables_filter {
				display: none;
			}
			.current-row {
				background-color: #3A5F91 !important;
			}

			input[type=number]::-webkit-inner-spin-button, 
			input[type=number]::-webkit-outer-spin-button { 
				-webkit-appearance: none; 
				margin: 0; 
			}
			input[type=number] { -moz-appearance:textfield; }
			sub { top: 0px; font-weight: bolder; }
		</style>
	</head>
	<body class="hold-transition sidebar-mini sidebar-collapse">
		<input type="hidden" id="hora_act" value="">
		<input type="hidden" id="uinombre" value="<?php echo $_SESSION['nomusuario'] ?>">
		<input type="hidden" id="uilogin" value="<?php echo $_SESSION['usuario'] ?>">
		<input type="hidden" id="movil">
		<!-- Navbar -->
		<div class="fixed-top main-header navbar navbar-expand border-bottom navbar-dark bg-dark elevation-2">
			<a class="nav-link mopcion" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
			<img src="dist/img/solologo.png" class="m-0 p-0 bg-transparent imgmain" height="45px">
			<img src="dist/img/solologo.png" class="m-0 p-0 bg-transparent imgmain2" height="30px" data-widget="pushmenu">
			<span id="titulo" class="align-items-center m-0 p-0 ml-2 titulo">Información <?php echo substr($titulo, strpos($titulo, '#')); ?></span>
		</div>
		<div class="wrapper">
			<!-- Main Sidebar Container -->
			<aside class="main-sidebar sidebar-dark-primary elevation-4" style="overflow: hidden;">
				<!-- Sidebar user panel (optional) -->
				<div class="user-panel m-0 p-0 d-flex align-items-center">
					<div class="image m-1 p-1">
						<img src="dist/img/favicon.png" class="brand-image" alt="Logo">
					</div>
					<div class="info mt-0 w-100 text-center pt-0">
						<span class="badge badge-success w-100 font-weight-normal">
							<?php echo substr($_SESSION['nomusuario'], 0, 30) ?>
						</span>
						<div class="d-flex pt-2">
							<form action="app/DBProcs.php" method="post" class="w-100 m-0 p-0 text-right">
								<input type="hidden" name="idpara" id="idpara" value="<?php echo $_SESSION['url'] ?>">
								<input type="hidden" name="opcion" id="opcion" value="cerrar_sesion">
								<button id="cerrarSesion" class="btn btn-outline-warning btn-sm p-0 m-0 pl-1 pr-1 w-100">
									Cerrar Sesión
								</button>
							</form>
						</div>
					</div>
				</div>
				
				<!-- Sidebar -->
				<div class="sidebar scroller m-0 p-0" style="overflow-x: hidden;">
					<!-- Sidebar Menu -->
					<nav class="m-1">
						<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
							<li class="nav-item">
								<a id="tasas_usd" href="javascript:void(0);" class="nav-link menu" onclick="cargarcontenido(this.id);">
									<i class="nav-icon fas fa-file-invoice-dollar"></i>
									<p>Tabla de Divisas</p>
								</a>
							</li>
							<li class="nav-item">
								<a id="articulos_usd" href="javascript:void(0);" class="nav-link menu" onclick="cargarcontenido(this.id);">
									<i class="nav-icon fas fa-cubes"></i>
									<p>Artículos Divisas</p>
								</a>
							</li>
							<li class="nav-item">
								<a id="export_prod" href="javascript:void(0);" class="nav-link menu" onclick="cargarcontenido(this.id);">
									<i class="nav-icon fas fa-file-excel"></i>
									<p>Exportar Productos</p>
								</a>
							</li>
						</ul>
					</nav>
					<!-- /.sidebar-menu -->
				</div>
				<!-- /.sidebar -->
			</aside>

			<div class="content-wrapper" id="wp_ppal">
				<!-- Contains page content -->
				<section class="content" id="contenido_ppal">
					<div class="d-flex m-0 p-0 justify-content-center align-items-center" style="height: 90vh;">
						<img src="dist/img/solologo.png" style="width: 90%;">
					</div>
				</section>
				<!-- /.content -->
			</div>
		</div>

		<!-- Modal Cargando-->
		<div class="modal" id="loading" style="z-index: 9999" tabindex="-1" role="dialog" aria-labelledby="ModalLoading" aria-hidden="true">
			<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
				<div class="modal-content align-items-center align-content-center border-0 elevation-0 bg-transparent">
					<div class="loader"></div>
					<button class="btn btn-sm btn-danger m-3 p-1"
						onclick="if(tomar_datos!=='') { tomar_datos.abort(); cargando('hide'); }">
						Cancelar Consulta
					</button>
				</div>
			</div>
		</div>

		<!-- Modal Cargando guardar solicitud de pedidos-->
		<div class="modal" id="loading2" style="z-index: 9999" tabindex="-1" role="dialog" aria-labelledby="ModalLoading" aria-hidden="true">
			<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
				<div class="modal-content align-items-center align-content-center border-0 elevation-0 bg-transparent">
					<div class="loader"></div>
					<div class="bg-warning-gradient p-2 m-2 text-center align-middle rounded border border-dark">
						<b>Procesando, espere...</b>
					</div>
				</div>
			</div>
		</div>

		<!-- jQuery -->
		<script src="plugins/jquery/jquery.min.js"></script>
		<!-- jQuery UI 1.12.1 -->
		<script src="plugins/jQueryUI/jquery-ui.min.js"></script>
		<!-- Bootstrap 4 -->
		<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
		
		<!-- DataTables -->
		<script src="plugins/datatables/jquery.dataTables.min.js"></script>
		<script src="plugins/datatables/dataTables.buttons.min.js"></script>
		<script src="plugins/datatables/jszip.min.js"></script>
		<script src="plugins/datatables/buttons.html5.min.js"></script>

		<!-- Datepicker bootstrap -->
		<script src="plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
		<script src="plugins/bootstrap-datepicker/js/bootstrap-datepicker.es.min.js"></script>

		<!-- SweetAlert2@9 -->
		<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>

		<!-- bootstrap-select -->
		<script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>
		<script src="plugins/bootstrap-select/js/defaults-es_ES.min.js"></script>
		<!-- InputMask -->
		<script src="plugins/input-mask/jquery.inputmask.js"></script>
		<script src="plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
		<!-- moment-with-locals.min.js -->
		<script src="dist/js/moment.min.js"></script>
		<!-- AdminLTE App -->
		<script src="dist/js/adminlte.min.js"></script>
		<!-- JS propias app -->
		<script src="app/js/app.js"></script>
		<!-- Encrypt js -->
		<script src="app/js/encrypt.min.js"></script>
		
		<script>
			moment.locale('es')
			moment.updateLocale('es', {
				week: {
					dow: 0
				}
			});
			var ancho = 600;
			var alto = 250;
			var tomar_datos = '';
			var bgcolor = '';

			$(window).resize(function(){
				// ajustar el ancho de todas las columnas de las datatable
				$('.dataTable').DataTable().columns.adjust().draw();
			});

			// Resolve conflict in jQuery UI tooltip with Bootstrap tooltip
			$.widget.bridge('uibutton', $.ui.button)

			$('.modal').modal({backdrop: 'static', keyboard: false, show: false});

			// Configuracion por defecto de las datatables
			$.extend( true, $.fn.dataTable.defaults, {
				language: {
					emptyTable        : "No hay información para mostrar",
					info              : "Mostrando _START_ de _END_ de _TOTAL_",
					infoEmpty         : "No hay información para mostrar",
					infoFiltered      : "(filtrado de _MAX_ entradas totales)",
					search            : "Buscar",
					infoPostFix       : "",
					lengthMenu        : "Mostrar _MENU_ líneas",
					loadingRecords    : "Cargando...",
					zeroRecords       : "No se encontraron registros",
					paginate: {
						first         : "Primero",
						last          : "Último",
						next          : "Siguiente",
						previous      : "Anterior"
					},
					aria: {
						sortAscending : ": activar orden ascendente de la columna",
						sortDescending: ": activar orden descendente de la columna"
					}
				},
				destroy: true,
				info: false,
				scrollY: "165px",
				bLengthChange: false,
				processing: true,
				paging: false,
				searching: false,
				ordering: true,
				sScrollX: "100%",
				scrollX: true,
			});

			const msg = Swal.mixin({
				customClass: {
					popup: 'p-2 bg-dark border border-warning',
					title: 'text-warning bg-transparent pl-3 pr-3',
					closeButton: 'btn btn-sm btn-danger',
					content: 'bg-white border border-warning rounded p-1',
					confirmButton: 'btn btn-sm btn-success m-1',
					cancelButton: 'btn btn-sm btn-danger m-1',
				},
				buttonsStyling: false,
				cancelButtonText: 'Cancelar',
				confirmButtonText: 'Aceptar',
				showCancelButton: true,
				allowOutsideClick: false,
			})
		</script>
	</body>
</html>
<?php } ?>