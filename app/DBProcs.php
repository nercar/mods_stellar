<?php
	/**
	* Permite obtener los datos de la base de datos y retornarlos
	* en modo json o array
	*/

	try {
		date_default_timezone_set('America/Caracas');
		// Se capturan las opciones por Post
		$opcion = (isset($_POST["opcion"])) ? $_POST["opcion"] : "";
		$fecha  = (isset($_POST["fecha"]) ) ? $_POST["fecha"]  : date("Y-m-d");
		$hora   = (isset($_POST["hora"])  ) ? $_POST["hora"]   : date("H:i:s");

		// id para los filtros en las consultas
		$idpara = (isset($_POST["idpara"])) ? $_POST["idpara"] : '';

		// Se establece la conexion con la BBDD
		$params = parse_ini_file('../dist/config.ini');

		if ($params === false) {
			// exeption leyen archivo config
			throw new \Exception("Error reading database configuration file");
		}

		// connect to the sql server database
		if($params['instance']!='') {
			$conStr = sprintf("Driver={SQL Server};Server=%s\%s;",$params['host_sql'],$params['instance']);
		} else {
			$conStr = sprintf("Driver={SQL Server};Server=%s;",$params['host_sql']);
		}

		$connec   = odbc_connect( $conStr, $params['user_sql'], $params['password_sql'] );

		if(!$connec) {
			die("Connection could not be established. (" . $conStr .')');
		} else {

			$moneda   = $params['moneda'];
			$simbolo  = $params['simbolo'];
			$bdVAD    = $params['bdVAD'];

			switch ($opcion) {
				case 'hora_srv':
					echo json_encode('1¬' . $hora);
					break;

				case 'iniciar_sesion':
					extract($_POST);
					if(empty($tusuario) || empty($tclave)){
						header("Location: " . $idpara);
						break;
					}

					$sql = "SELECT login_name, descripcion, codusuario, password AS clave, BS_ACTIVO AS activo
							FROM VAD10.dbo.MA_USUARIOS WHERE LOWER(login_name)=LOWER('$tusuario')";

					$sql = odbc_exec( $connec, $sql );
					if(!$sql) print("Error en Consulta SQL (".odbc_errormsg($connec).")");

					$row = odbc_fetch_array($sql);

					if( md5($row['clave']) == $_POST['tclave'] ){
						if($row['activo']!=1) {
							session_destroy();
							session_commit();
							session_start();
							session_id($_SESSION['id']);
							session_destroy();
							session_commit();
							session_start();
							$_SESSION['error'] = 2;
							header("Location: " . $idpara);
						} else {
							session_start();
							$_SESSION['id']         = session_id();
							$_SESSION['url']        = $idpara;
							$_SESSION['usuario']    = strtolower($row['login_name']);
							$_SESSION['nomusuario'] = ucwords(strtolower($row['descripcion']));
							$_SESSION['error']      = 0;
							header("Location: " . $idpara . "inicio.php");
						}
					} else {
						session_start();
						session_id($_SESSION['id']);
						session_destroy();
						session_commit();
						session_start();
						$_SESSION['error'] = 1;
						header("Location: " . $idpara);
					}

					break;

				case 'cerrar_sesion':
					session_start();
					session_id($_SESSION['id']);
					session_destroy();
					session_commit();
					header("Location: " . $_SESSION['url']);
					exit();
					break;

				case 'lstTasasUSD':
					$sql = "SELECT id, tasa, modificado_por, ultima_modificacion,
							CONVERT(VARCHAR(10), fecha, 105) AS fecha,
							(CONVERT(VARCHAR(10), ultima_modificacion, 105)+' '+
							CONVERT(VARCHAR(5), ultima_modificacion, 108)) AS ultima_fecha, activo
							FROM VAD10.dbo.DB_TASAS_USD";

					$sql = odbc_exec( $connec, $sql );
					if(!$sql) print("Error en Consulta SQL (".odbc_errormsg($connec).")");
					
					$datos = [];
					while ($row = odbc_fetch_array($sql)) {
						$datos[] = $row;
					}

					echo json_encode(array('data' => $datos));
					break;
				
				case 'guardarTasaDolar':
					$idpara = explode('¬', $idpara);
					$sql = "INSERT INTO VAD10.dbo.DB_TASAS_USD(fecha, tasa, modificado_por, ultima_modificacion, activo)
							VALUES('$fecha', $idpara[0], '$idpara[1]', CURRENT_TIMESTAMP, 1)";
				
					$sql = odbc_exec( $connec, $sql );

					if(!$sql) {
						echo 0;
						print("Error en Consulta SQL (".odbc_errormsg($connec).")");
					} else {
						echo 1;
					}
					
					break;
				
				case 'marcarTasa':
					// se extraen los valores del parametro idpara
					$params = explode('¬', $idpara);
					
					$sql = "UPDATE VAD10.dbo.DB_TASAS_USD SET
							activo = $params[1]
							WHERE id = $params[0]";
				
					$sql = odbc_exec( $connec, $sql );

					if($sql) {
						echo '1';
					} else {
						echo '0';
						print("Error en Consulta SQL (".odbc_errormsg($connec).")");
					}

					break;

				case 'tasaAct':
					extract($_POST);
					$tasa = 1; // monto del dolar para mostrar los precios en dolares
					$sql = "SELECT TOP 1 tasa
							FROM VAD10.dbo.DB_TASAS_USD
							WHERE activo = 1
							ORDER BY ultima_modificacion DESC";
					$sql = odbc_exec( $connec, $sql );
					if(!$sql) print("Error en Consulta SQL (".odbc_errormsg($connec).")");
					else {
						$row = odbc_fetch_array($sql);
						if($row) $tasa = $row['tasa'];
					}

					echo number_format($tasa, 4);
					break;
				
				case 'config_act':
					$tasa = 1; // monto del dolar para mostrar los precios en dolares
					$sql = "SELECT TOP 1 tasa
							FROM VAD10.dbo.DB_TASAS_USD
							WHERE activo = 1
							ORDER BY ultima_modificacion DESC";
					$sql = odbc_exec( $connec, $sql );
					if(!$sql) print("Error en Consulta SQL (".odbc_errormsg($connec).")");
					else {
						$row = odbc_fetch_array($sql);
						if($row) $tasa = $row['tasa'];
					}

					$sql="SELECT a.C_CODIGO AS C_CODIGO, a.C_DESCRI AS descripcion,
							pd.c_COSTO_USD, pd.c_PRECIO_USD, pd.c_ACTIVO
						FROM VAD10.dbo.DB_PRODUCTOS_USD AS pd
						INNER JOIN VAD10.dbo.MA_PRODUCTOS AS a ON a.C_CODIGO = pd.C_CODIGO
						WHERE c_ACTIVO = 1";

					// Se ejecuta la consulta en la BBDD
					$sql = odbc_exec( $connec, $sql );
					if(!$sql) print("Error en Consulta SQL (".$opcion.'-'.odbc_errormsg($connec).")");
					else {
						// Se prepara el array para almacenar los datos obtenidos
						$datos = [];
						while ($row = odbc_fetch_array($sql)) {
							$costo = $row['c_COSTO_USD']*$tasa;
							$precio = $row['c_PRECIO_USD']*$tasa;
							
							$datos[] = [
								'codigo'      => $row['C_CODIGO'],
								'descripcion' => utf8_encode($row['descripcion']),
								'costo_usd'   => $row['c_COSTO_USD'],
								'costo'       => $costo,
								'precio_usd'  => $row['c_PRECIO_USD'],
								'precio'      => $precio,
								'tasa'        => number_format($tasa, 4),
								'activo'      => $row['c_ACTIVO']
							];
						}

						echo json_encode($datos);
					}
					break;
				
				case 'listaDptos':
					$sql="SELECT C_CODIGO AS codigo, C_DESCRIPCIO AS descripcion
						FROM VAD10.dbo.MA_DEPARTAMENTOS ORDER BY C_DESCRIPCIO";

					// Se ejecuta la consulta en la BBDD
					$sql = odbc_exec( $connec, $sql );
					if(!$sql) echo json_encode("Error en Consulta SQL (".$sql.")");
					else {
						// Se prepara el array para almacenar los datos obtenidos
						$datos = [];
						while ($row = odbc_fetch_array($sql)) {
							$datos[] = [
								'codigo'      => $row['codigo'],
								'descripcion' => utf8_encode($row['descripcion']),
							];
						}

						echo json_encode($datos);
					}
					break;

				case 'export_prod':
					extract($_POST);
					$tasa = 1; // monto del dolar para mostrar los precios en dolares
					$sql = "SELECT TOP 1 tasa
							FROM VAD10.dbo.DB_TASAS_USD
							WHERE activo = 1
							ORDER BY ultima_modificacion DESC";
					$sql = odbc_exec( $connec, $sql );
					if(!$sql) print("Error en Consulta SQL (".odbc_errormsg($connec).")");
					else {
						$row = odbc_fetch_array($sql);
						if($row) $tasa = $row['tasa'];
					}


					$dpto = ($dptos=='*')?'c_departamento = c_departamento':'c_departamento = '.$dptos;
					$pvp0 = ($precio==1)?'n_precio1 = n_precio1':'n_precio1 > 0';

					$sql="SELECT C_CODIGO AS C_CODIGO, C_DESCRI AS descripcion,
							n_costoact AS n_costoact, n_precio1 AS n_precio1, n_impuesto1
						FROM VAD10.dbo.MA_PRODUCTOS
						WHERE n_activo = 1
						AND (C_CODIGO LIKE '%$produc%' OR LOWER(C_DESCRI) LIKE '%$produc%')
						AND $dpto
						AND $pvp0";

					// Se ejecuta la consulta en la BBDD
					$sql = odbc_exec( $connec, $sql );
					$datos = [];
					if(!$sql) print("Error en Consulta SQL (".odbc_errormsg($connec).")");
					else {
						// Se prepara el array para almacenar los datos obtenidos
						while ($row = odbc_fetch_array($sql)) {
							$costo  = bcdiv(($row['n_costoact']*1), $tasa, 9);
							$precio = bcdiv(($row['n_precio1'] *1), $tasa, 9);
							
							$datos[] = [
								'codigo'      => $row['C_CODIGO'],
								'descripcion' => utf8_encode($row['descripcion']),
								'costo'       => $row['n_costoact'],
								'costo_usd'   => $costo,
								'precio'      => $row['n_precio1'],
								'precio_usd'  => $precio
							];
						}
					}
					
					echo json_encode(array('tasa'=>number_format($tasa, 4), 'datos'=>$datos));
					break;
				
				case 'subirProductosUSD':
					// error_reporting(E_ALL);
					ini_set('memory_limit', '-1');
					ini_set('max_execution_time', 0);
					// Se verifica si se envio algun archivo
					$target_path = "../tmp/";
					$archivoreal = basename($_FILES['archivo']['name']);
					$extension = explode('.', $archivoreal);
					$extension = end($extension);
					$result = "0¬Hubo un error, Por favor revise el archivo y trate de nuevo!(" . $archivoreal . ")"; 
					if($extension == 'csv') {
						$archivoreal = bin2hex(random_bytes(10)) . '.' . $extension;
						$archivotemp = $_FILES['archivo']['tmp_name'];
						$target_path = $target_path . $archivoreal;
						if(move_uploaded_file($archivotemp, $target_path)) {
							// Se detecta el delimitador de campos del csv
							$delimiter = getFileDelimiter($target_path);

							// Se inicializa la linea para detectar las 2 primeras lineas de datos
							$linea = 0;
							
							// Filas para insertar en la tabla temporal y en el historico de dbbonos
							$registros = [];
							
							//Abrimos nuestro archivo
							//Consultamos la ultima tasa activa y obtenemos el id
							$tasa = 0;
							$sql = "SELECT TOP 1 id
									FROM VAD10.dbo.DB_TASAS_USD
									WHERE activo = 1
									ORDER BY ultima_modificacion DESC";
							
							$sql = odbc_exec( $connec, $sql );
							if(!$sql) print("Error en Consulta SQL (".odbc_errormsg($connec).")");
							else {
								$row = odbc_fetch_array($sql);
								if($row) $tasa = $row['id'];
							}

							$archivo = fopen($target_path, "r");

							// recorremos el archivo
							while(($datos = fgetcsv($archivo, null, $delimiter)) == true) {
								if($linea<3) {
									$linea++;
									continue;
								}
								$registros[] = [
									$tasa, $idpara, $datos[0],
									str_replace(',', '.', $datos[4]),
									str_replace(',', '.', $datos[5])
								];
							}

							// Cerramos el archivo
							fclose($archivo);
							// Eliminamos el archivo
							unlink($target_path);
							
							// Se crea el query para insertar en la tabla temporal de dbbonos
							$sql = "IF OBJECT_ID('#DB_PRODUCTOS_USD') IS NOT NULL DROP TABLE #DB_PRODUCTOS_USD
									CREATE TABLE #DB_PRODUCTOS_USD(
										c_TASA_ID int,
										c_USR_MOD varchar(50),
										c_CODIGO nvarchar(15),
										c_COSTO_USD numeric(18,9),
										c_PRECIO_USD numeric(18,9));";

							$sql = odbc_exec( $connec, $sql );

							$maxrecords = 500;
							$i=1;
							$row=0;
							$filas = '';
							foreach ($registros as $value) {
								$filas.= "(".$value[0].",'".$value[1]."','".$value[2]."',".
									$value[3].",".$value[4]."),";
								if($i=$maxrecords) {
									// Se elimina la ultima coma de la cadena
									$filas = substr($filas, 0, -1);

									// Se crea el query para insertar en la tabla temporal de dbbonos
									$sql = "INSERT INTO #DB_PRODUCTOS_USD(
												c_TASA_ID,
												c_USR_MOD,
												c_CODIGO,
												c_COSTO_USD,
												c_PRECIO_USD)
											VALUES ".$filas;

									$sql2 = $sql;
									$sql = odbc_exec( $connec, $sql );

									if(!$sql) {
										$result = "0¬Error al Actualizar los registros Temporales<br>".$sql;
										$result.= "<br>".odbc_errormsg($connec);
										echo $sql2;
										break;
									} else {
										$filas = '';
										$i=0;
									}
								}
								$i++;
							}
							if($i>1) {
								$i=0;
								// Se elimina la ultima coma de la cadena
								$filas = substr($filas, 0, -1);

								// Se crea el query para insertar en la tabla temporal de dbbonos
								$sql = "INSERT INTO #DB_PRODUCTOS_USD(
											c_TASA_ID,
											c_USR_MOD,
											c_CODIGO,
											c_COSTO_USD,
											c_PRECIO_USD)
										VALUES ".$filas;

								$sql2 = $sql;
								$sql = odbc_exec( $connec, $sql );

								if(!$sql) {
									$result = "0¬Error al Actualizar los registros Temporales<br>".$sql;
									$result.= "<br>".odbc_errormsg($connec);
									echo $sql2;
									break;
								} 
							}

							$sql = "MERGE VAD10.dbo.DB_PRODUCTOS_USD AS destino
									USING (SELECT c_CODIGO COLLATE Modern_Spanish_CI_AS AS c_CODIGO,
											c_TASA_ID, c_USR_MOD AS c_USR_MOD,
											c_COSTO_USD, c_PRECIO_USD
										FROM #DB_PRODUCTOS_USD ) AS origen
									ON destino.c_CODIGO = origen.c_CODIGO
									WHEN MATCHED THEN
										UPDATE SET
											c_TASA_ID = origen.c_TASA_ID,
											c_FECHA_MOD	= GETDATE(),
											c_USR_MOD = origen.c_USR_MOD,
											c_COSTO_USD = origen.c_COSTO_USD,
											c_PRECIO_USD = origen.c_PRECIO_USD,
											c_ACTIVO = 1
									WHEN NOT MATCHED THEN
										INSERT(c_TASA_ID, c_FECHA_MOD, c_USR_MOD, c_CODIGO, c_COSTO_USD,
												c_PRECIO_USD, c_ACTIVO)
										VALUES(origen.c_TASA_ID, GETDATE(), origen.c_USR_MOD, origen.c_CODIGO,
												origen.c_COSTO_USD, origen.c_PRECIO_USD, 1);";

							$sql = odbc_exec( $connec, $sql );

							if(!$sql) {
								$result = "0¬Error al Actualizar los registros<br>".$sql;
								$result.= "<br>".odbc_errormsg($connec);
								break;
							} else {
								$row += odbc_num_rows($sql);
								$result = "1¬Registros afectados ($row)";
							}
						}
					}

					echo $result;
					break;

				case 'sincProdUSD':
					error_reporting(0);
					extract($_POST);
					$resp = '0¬Error Desconocido'; 
					$tasa = 1; // monto del dolar para mostrar los precios en dolares
					$sql = "SELECT TOP 1 tasa
							FROM VAD10.dbo.DB_TASAS_USD
							WHERE activo = 1
							ORDER BY ultima_modificacion DESC";
					$sql = odbc_exec( $connec, $sql );
					if(!$sql) {
						$resp = "0¬Error Consultando la tasa<br>".$sql;
						echo $resp;
						break;
					} else {
						$row = odbc_fetch_array($sql);
						if($row) $tasa = $row['tasa'];
					}

					// Primero se actualizan los precios y el costo en MA_PRODUCTOS en VAD10 y VAD20
					$sql = "UPDATE VAD10.dbo.MA_PRODUCTOS SET
								n_costoact  = (pd.c_COSTO_USD  * $tasa),
								n_precio1   = (pd.c_PRECIO_USD * $tasa),
								n_precio2   = (pd.c_PRECIO_USD * $tasa),
								n_precio3   = (pd.c_PRECIO_USD * $tasa),
								update_date = GETDATE()
							FROM (SELECT c_CODIGO AS c_CODIGO, c_COSTO_USD, c_PRECIO_USD
									FROM VAD10.dbo.DB_PRODUCTOS_USD WHERE c_ACTIVO = 1) AS pd
							WHERE MA_PRODUCTOS.C_CODIGO = pd.c_CODIGO;

							UPDATE VAD20.dbo.MA_PRODUCTOS SET
								n_costoact  = (pd.c_COSTO_USD  * $tasa),
								n_precio1   = (pd.c_PRECIO_USD * $tasa),
								n_precio2   = (pd.c_PRECIO_USD * $tasa),
								n_precio3   = (pd.c_PRECIO_USD * $tasa),
								update_date = GETDATE()
							FROM (SELECT c_CODIGO AS c_CODIGO, c_COSTO_USD, c_PRECIO_USD
									FROM VAD10.dbo.DB_PRODUCTOS_USD WHERE c_ACTIVO = 1) AS pd
							WHERE MA_PRODUCTOS.C_CODIGO = pd.c_CODIGO;";

					$sql = odbc_exec( $connec, $sql );
					if(!$sql) {
						echo "0¬Error Actualizando MA_PRODUCTOS<br>".$sql;
						break;
					} else {
						// Se valida si el tipo mde sincronizacion es 1->cajas o 2->habladores
						if($tiposinc==2) {
							$sql = "
								INSERT INTO ".$bdVAD.".dbo.TR_PENDIENTE_PROD
									(C_CODIGO, C_DESCRI, c_departamento, c_grupo, c_subgrupo, c_marca, c_modelo,
									c_procede, n_costoact, n_costoant, n_costopro, n_costorep, n_precio1,
									n_precio2, n_precio3, C_seriales, C_compuesto, C_presenta, n_peso, n_volumen,
									n_cantibul, n_pesobul, n_volbul, c_fileimagen, n_impuesto1, n_impuesto2,
									n_impuesto3, c_cod_arancel, c_des_arancel, n_por_arancel, n_costo_original,
									c_observacio, n_activo, n_tipopeso, n_precioO, f_inicial, f_final, h_inicial,
									h_final, add_date, update_date, c_codfabricante, HABLADOR, C_CODMONEDA,
									CANT_DECIMALES, MONEDA_ANT, MONEDA_ACT, MONEDA_PRO, C_COD_PLANTILLA,
									N_PRO_EXT, c_usuarioAdd, c_usuarioupd, C_CODIGO_BASE, C_DESCRI_BASE, TEXT1,
									TEXT2, TEXT3, TEXT4, TEXT5, TEXT6, TEXT7, TEXT8, DATE1, NUME1, N_CANTIDAD_TMP,
									N_PROD_EXT, NU_TIPO_PRODUCTO, nu_insumointerno, nu_precioregulado,
									nu_pocentajemerma, nu_nivelClave, CU_DESCRIPCION_CORTA, bs_permiteteclado,
									bs_permitecantidad, nu_stockmin, nu_stockmax)
								SELECT 
									C_CODIGO, C_DESCRI, c_departamento, c_grupo, c_subgrupo, c_marca, c_modelo,
									c_procede, n_costoact, n_costoant, n_costopro, n_costorep, n_precio1,
									n_precio2, n_precio3, C_seriales, C_compuesto, C_presenta, n_peso, n_volumen,
									n_cantibul, n_pesobul, n_volbul, c_fileimagen, n_impuesto1, n_impuesto2,
									n_impuesto3, c_cod_arancel, c_des_arancel, n_por_arancel, n_costo_original,
									c_observacio, n_activo, n_tipopeso, n_precioO, f_inicial, f_final, h_inicial,
									h_final, add_date, update_date, c_codfabricante, HABLADOR, C_CODMONEDA,
									CANT_DECIMALES, MONEDA_ANT, MONEDA_ACT, MONEDA_PRO, C_COD_PLANTILLA,
									N_PRO_EXT, c_usuarioAdd, c_usuarioupd, C_CODIGO_BASE, C_DESCRI_BASE, TEXT1,
									TEXT2, TEXT3, TEXT4, TEXT5, TEXT6, TEXT7, TEXT8, DATE1, NUME1, N_CANTIDAD_TMP,
									N_PROD_EXT, NU_TIPO_PRODUCTO, nu_insumointerno, nu_precioregulado,
									nu_pocentajemerma, nu_nivelClave, CU_DESCRIPCION_CORTA, bs_permiteteclado,
									bs_permitecantidad, nu_stockmin, nu_stockmax
								FROM VAD10.dbo.MA_PRODUCTOS
								WHERE C_CODIGO IN(SELECT c_CODIGO
										FROM VAD10.dbo.DB_PRODUCTOS_USD WHERE c_ACTIVO = 1)";
						} else {
							$sql = "
								INSERT INTO ".$bdVAD.".dbo.TR_PEND_PRODUCTOS
									(C_CODIGO, C_DESCRI, c_departamento, c_grupo, c_subgrupo, c_marca, c_modelo,
									c_procede, n_costoact, n_costoant, n_costopro, n_costorep, n_precio1,
									n_precio2, n_precio3, C_seriales, C_compuesto, C_presenta, n_peso, n_volumen,
									n_cantibul, n_pesobul, n_volbul, c_fileimagen, n_impuesto1, n_impuesto2,
									n_impuesto3, c_cod_arancel, c_des_arancel, n_por_arancel, n_costo_original,
									c_observacio, n_activo, n_tipopeso, n_precioO, f_inicial, f_final, h_inicial,
									h_final, add_date, update_date, c_codfabricante, HABLADOR, C_CODMONEDA,
									CANT_DECIMALES, MONEDA_ANT, MONEDA_ACT, MONEDA_PRO, c_usuarioAdd, c_usuarioupd,
									C_CODIGO_BASE, C_DESCRI_BASE, TEXT1, TEXT2, TEXT3, TEXT4, TEXT5, TEXT6, TEXT7,
									TEXT8, DATE1, NUME1, N_CANTIDAD_TMP, C_COD_PLANTILLA, N_PROD_EXT, N_PRO_EXT,
									NU_TIPO_PRODUCTO, N_CAJA, nu_insumointerno, nu_precioregulado, nu_pocentajemerma,
									nu_nivelClave, CU_DESCRIPCION_CORTA, bs_permiteteclado,
									bs_permitecantidad, nu_stockmin, nu_stockmax)
								SELECT
									p.C_CODIGO, C_DESCRI, c_departamento, c_grupo, c_subgrupo, c_marca, c_modelo,
									c_procede, n_costoact, n_costoant, n_costopro, n_costorep, n_precio1,
									n_precio2, n_precio3, C_seriales, C_compuesto, C_presenta, n_peso, n_volumen,
									n_cantibul, n_pesobul, n_volbul, c_fileimagen, n_impuesto1, n_impuesto2,
									n_impuesto3, c_cod_arancel, c_des_arancel, n_por_arancel, n_costo_original,
									c_observacio, n_activo, n_tipopeso, n_precioO, f_inicial, f_final, h_inicial,
									h_final, add_date, update_date, c_codfabricante, HABLADOR, p.C_CODMONEDA,
									CANT_DECIMALES, MONEDA_ANT, MONEDA_ACT, MONEDA_PRO, c_usuarioAdd, c_usuarioupd,
									C_CODIGO_BASE, C_DESCRI_BASE, TEXT1, TEXT2, TEXT3, TEXT4, TEXT5, TEXT6, TEXT7,
									TEXT8, DATE1, NUME1, N_CANTIDAD_TMP, C_COD_PLANTILLA, N_PROD_EXT, N_PRO_EXT,
									NU_TIPO_PRODUCTO, cj.C_Codigo, nu_insumointerno, nu_precioregulado,
									nu_pocentajemerma, nu_nivelClave, CU_DESCRIPCION_CORTA, bs_permiteteclado,
									bs_permitecantidad, nu_stockmin, nu_stockmax
								FROM VAD20.dbo.MA_PRODUCTOS AS p
								LEFT JOIN VAD20.dbo.MA_CAJA AS cj ON cj.C_Codigo = cj.C_Codigo
								WHERE p.C_CODIGO IN(SELECT c_CODIGO
										FROM VAD10.dbo.DB_PRODUCTOS_USD WHERE c_ACTIVO = 1)";
						}
						$sql = odbc_exec( $connec, $sql );
						if(!$sql) {
							$resp = "0¬Error Insertando Sincronización<br>".odbc_errormsg($connec);
						} else {
							$resp = "1¬Sincronización Realizada correctamente<br>".$sql;
						}
					}

					echo $resp;
					break;
				
				case 'delProdUSD':
					// Se prepara la consulta para inactivar un artículo
					$sql = "UPDATE VAD10.dbo.DB_PRODUCTOS_USD SET c_ACTIVO = 0 WHERE c_CODIGO = '$idpara'";
					
					$resp = 0;
					$sql = odbc_exec( $connec, $sql );
					
					if(!$sql) print("Error en Consulta SQL (".odbc_errormsg($connec).")");
					else $resp = 1;

					echo $resp;
					break;

				default:
					# code...
					break;
			}
		}

		$connec = null;

	} catch (PDOException $e) {
		echo "Error : " . $e->getMessage() . "<br/>";
		die();
	}

	function getFileDelimiter($file, $checkLines = 2){
		$file = new SplFileObject($file);
		$delimiters = array( ',', '\t', ';', '|', ':' );
		$results = array();
		$i = 0;
		while($file->valid() && $i <= $checkLines){
			$line = $file->fgets();
			foreach ($delimiters as $delimiter){
				$regExp = '/['.$delimiter.']/';
				$fields = preg_split($regExp, $line);
				if(count($fields) > 1){
					if(!empty($results[$delimiter])){
						$results[$delimiter]++;
					} else {
						$results[$delimiter] = 1;
					}
				}
			}
			$i++;
		}
		$results = array_keys($results, max($results)); return $results[0];
	}
?>