<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"> 
	<title> </title>
	<link rel="stylesheet" href="">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script>
		//Entrada del HTML
		var xx;
		var fileName_upload;
		var base64_file;
		var data = {"controller": "1", "cmd": "1"};
		$.ajax({
			url: "./controller/main_controller.php",
			data: data,
			type: "POST",
			success: function (response) {
				dibujarBotonCrearAlbum();
				for (var i = 0; i < response.data.length; i++) {
					dibujarAlbum(response.data[i].nombre, response.data[i].id);
				}
			},
			dataType: "json"
		});	

		/*
		@DESCRIPCION: Devuelve un texto en formato mayuscula(solo la primera).
		@AUTOR: F.Sole
		@FECHA: 2020/08/18 
		@VERSION: 1.0
		*/
		function capitalize(s) {
			if (typeof s !== 'string') {
				return ''
			}
			return s.charAt(0).toUpperCase() + s.slice(1)
		}

		/*
		@DESCRIPCION: Dado un texto, crea una carpeta (album), con una caratula(?), establece los eventos click.
		@AUTOR: F.Sole
		@FECHA: 2020/08/14 
		@VERSION: 1.0
		*/
		function dibujarAlbum(text, id){
			//Creamos un div, que contendra la imgen de la carpeta y la caratula(?).
			var idd = newIdd();
			var html = "<div idAlbum='" + id + "' class='box_album' id='box_album_" + idd +"' texto='" + text + "'>";
			html +=        "<div class='album' id='album_" + idd +"'>";
			html +=            "<img style='user-select:none;' height=40 src='./media/img/folder.png'>";
			html +=            "<div id='info_album_" + idd +"' style='float: right;margin-left:15px'>";
			html +=                "<img style='user-select:none;' height=20 src='./media/img/info-b.png'>";
			html +=            "</div>";
			html +=            "<div class='texto_album' id='album_texto_" + idd +"'>";
			html +=               capitalize(text);
			html +=            "</div>";
			html +=        "</div>";
			html +=    "</div>";

			//agregamos el contenido
			$("#albums_div").append(html);

			//cuando click en el album (ver fotos)
			$("#box_album_" + idd).click(function () {
				var datos = {"id": id, "path": text};
				var data = {"controller": "1", "cmd": "8", "data": datos};
				$.ajax({
					url: "./controller/main_controller.php",
					data: data,
					type: "POST",
					success: function (response) {
						if(response.photos.data.length == 0){
							alert("Sin fotos... TODO ALERT BONITO");
						}else{
							$("#fotos_div").html("");
							$("#albums_div").css("height", "auto");
							$("#albums_div").animate({
								height: '0px'
							},
							500, 
							function(){
								$("#albums_div").hide();
								$("#fotos_div").fadeIn();
								for (var i = 0; i < response.photos.data.length; i++) {
									var idd = response.photos.data[i].id;
									var html = "<img id='box_picture_" + idd + "' order='" + i + "' style='display: none;' class='img' idimagen='" + idd + "' alt='imagen" + i + "' src='./media/source/"+ response.album + "/" + response.photos.data[i].nombre + "'/>";
									$("#fotos_div").append(html);
									$("img[idimagen=" + idd + "]").click(function(event) {
										mostrarVentanaFotos($(this).attr("src"));
									});

									$("#box_picture_" + idd).bind("contextmenu", function(e){
										//llenamos el menu con las opciones
										var html = "";
										var idImagen = $(this).attr("idimagen");
										html += "<ul>";
										html +=     "<li id='propertiesPicture_" + idImagen + "'>";
										html +=         "Propiedades";
										html +=     "</li>";
										html +=     "<li id='downloadPicture_" + idImagen + "'>";
										html +=         "Descargar foto";
										html +=     "</li>";
										html +=     "<li>";
										html +=         "<hr/>";
										html +=     "</li>";
										html +=     "<li id='deletePicture_" + idImagen + "'>";
										html +=         "Eliminar foto";
										html +=     "</li>";
										html += "</ul>";
										$("#menu").html(html);
										$("#menu").css({'display':'block', 'left':e.pageX, 'top':e.pageY, "position": "absolute"});
										return false;
									});

									var delay;

									var longpress = 1300;
									var obj = document.getElementById("box_picture_" + idd);
									obj.addEventListener('touchstart', function(e){
										var idImagen = obj.getAttribute("idimagen");
										var touchlist = e.changedTouches[0];
										delay = setTimeout(function(){
											idImagen = obj.getAttribute("idimagen");
											var a = e.target;
											var aa = a.getAttribute("idimagen");
											var html = "";
											html += "<ul>";
											html +=     "<li id='propertiesPicture_" + aa + "'>";
											html +=         "Propiedades";
											html +=     "</li>";
											html +=     "<li id='downloadPicture_" + aa + "'>";
											html +=         "Descargar foto";
											html +=     "</li>";
											html +=     "<li>";
											html +=         "<hr/>";
											html +=     "</li>";
											html +=     "<li id='deletePicture_" + aa + "'>";
											html +=         "Eliminar foto";
											html +=     "</li>";
											html += "</ul>";
											$("#menu").html(html);
											$("#menu").css({'display':'block', 'left':touchlist.clientX, 'top':touchlist.clientY, "position": "absolute"});
											e.preventDefault();
											return false;
										}, longpress);

									});

									obj.addEventListener("touchend", function(e) {
										clearTimeout(delay);
									});

									obj.addEventListener("touchmove", function(e) {
										clearTimeout(delay);
									});
								}
								//ES CORRECTO 2 VECES!
								$("div[name='selectSize'][selected]").click();
								$("div[name='selectSize'][selected]").click();

								//efecto para que aparezcan como poco a poco
								$("img[order]").each(function(index, el) {
									$(el).fadeIn(parseInt((Math.random())*250+50));
								});


								//activamos botones de opciones
								$("#btnAtras").show();
								$("#btnUpload").show();

							});
						}
						
					},
					dataType: "json"
				});	
			})

			//click derecho
			//Ocultamos el menú al cargar la página
			$("#menu").hide();

			$("#box_album_" + idd).bind("contextmenu", function(e){
				//llenamos el menu con las opciones
				var html = "";
				html += "<ul>";
				html +=     "<li id='change_" + idd + "'>";
				html +=         "Cambiar nombre";
				html +=     "</li>";
				html +=     "<li id='properties_" + idd + "'>";
				html +=         "Propiedades";
				html +=     "</li>";
				html +=     "<li id='download_" + idd + "'>";
				html +=         "Descargar contenido";
				html +=     "</li>";
				html +=     "<li>";
				html +=         "<hr/>";
				html +=     "</li>";
				html +=     "<li id='delete_" + idd + "'>";
				html +=         "Eliminar album";
				html +=     "</li>";
				html += "</ul>";
				$("#menu").html(html);
				$("#menu").css({'display':'block', 'left':e.pageX, 'top':e.pageY, "position": "absolute"});
				return false;
			});

			// Create variable for setTimeout
			var delay;

  			// Set number of milliseconds for longpress
  			var longpress = 1300;
  			var obj = document.getElementById("box_album_" + idd);
  			obj.addEventListener('touchstart', function(e){
  				var touchlist = e.changedTouches[0];
  				//for (var i=0; i<touchlist.length; i++){ 
  					delay = setTimeout(function(){

  						//llenamos el menu con las opciones
  						var html = "";
  						html += "<ul>";
  						html +=     "<li id='change_" + idd + "'>";
  						html +=         "Cambiar nombre";
  						html +=     "</li>";
  						html +=     "<li id='properties_" + idd + "'>";
  						html +=         "Propiedades";
  						html +=     "</li>";
  						html +=     "<li id='download_" + idd + "'>";
  						html +=         "Descargar contenido";
  						html +=     "</li>";
  						html +=     "<li>";
  						html +=         "<hr/>";
  						html +=     "</li>";
  						html +=     "<li id='delete_" + idd + "'>";
  						html +=         "Eliminar album";
  						html +=     "</li>";
  						html += "</ul>";
  						$("#menu").html(html);
  						$("#menu").css({'display':'block', 'left':touchlist.clientX, 'top':touchlist.clientY, "position": "absolute"});
  						e.preventDefault();
  						return false;
  					}, longpress);
  				//}

  			});

  			obj.addEventListener("touchend", function(e) {
  				console.log("2");
  				clearTimeout(delay);
  			});

  			obj.addEventListener("touchmove", function(e) {
  				console.log("3");
  				clearTimeout(delay);
  			});

			//cuando hover en "i" nos da informacion del album
			var delayHover = 1500;
			var setTimeoutConst;
			$("#info_album_" + idd).hover(function() {
				setTimeoutConst = setTimeout(function() {  
					var id = $("#box_album_" + idd).attr("idalbum");
					var path = $("#box_album_" + idd).attr("texto");

					var datos = {"idd": idd, "id": id, "path": path};
					var data = {"controller": "1", "cmd": "3", "data": datos};
					$.ajax({
						url: "./controller/main_controller.php",
						data: data,
						type: "POST",
						success: function (response) {
							var t = response.total;
							var unidad_t = "B"; 
							if(parseFloat(t) > 1000){
								t = t/1024;
								unidad_t = "KB";
							}

							if(parseFloat(t) > 1000){
								t = t/1024;
								unidad_t = "MB";
							}

							if(parseFloat(t) > 1000){
								t = t/1024;
								unidad_t = "GB";
							}

							var h = response.higger;
							var unidad_h = "B"; 
							if(parseFloat(h) > 1000){
								h = h/1024;
								unidad_h = "KB";
							}

							if(parseFloat(h) > 1000){
								h = h/1024;
								unidad_h = "MB";
							}

							if(parseFloat(h) > 1000){
								h = h/1024;
								unidad_h = "GB";
							}

							var html = "<div style='display:none' class='info_hover' id='hover_" + idd + "'>";
							html +=    "<p>Tamaño del album: " + t.toFixed(2) + " " + unidad_t +"</p>";
							html +=    "<p>Nº elementos: " + response.count + "</p>";
							html +=    "<p> Elemento mas grande: " + h.toFixed(2) + " " + unidad_h + "</p>";
							html +=    "<p> Nombre: " + response.name + "</p>";
							html+=     "</div>";
							$("#info_album_" + idd).append(html);
							$("#hover_"+ idd).fadeIn(300);
						},
						dataType: "json"
					});	
				}, delayHover);
			}, function() {
				clearTimeout(setTimeoutConst);
				setTimeout(function(){
					$("#hover_"+ idd).fadeOut(300).remove();
				},500);
			});
			$("#cargando").fadeOut(500);
		}

		/*
		@DESCRIPCION: dibuja un icono de album vacio, asigan el evento click.
		@AUTOR: F.Sole
		@FECHA: 2020/08/14 
		@VERSION: 1.0
		*/
		function dibujarBotonCrearAlbum(){
			var html = "<div class='box_album' id='nuevo_album' texto='crear album'>";
			html +=        "<div class='album'>";
			html +=            "<img style='user-select:none;' height=40 src='./media/img/folder_w.png'>"
			html +=            "<div class='texto_album'>"
			html +=            "Crear album";
			html +=            "</div>";
			html +=        "</div>";
			html +=    "</div>";

			//agregamos el contenido
			$("#albums_div").append(html);

			$("#nuevo_album").click(function(event) {
				//creamos nuevo album
				mostrarVentanaNuevoalbum();
			});
		}

		/*
		@DESCRIPCION: muestra el formulario para crear un nuevo album.
		@AUTOR: F.Sole
		@FECHA: 2020/08/14 
		@VERSION: 1.0
		*/
		function mostrarVentanaNuevoalbum(){
			$("#sombra").show();
			$("#nuevoAlbum").fadeIn({done: function() { 
				$("#nuevoNombreAlbum").focus(); 
				$("#nuevoNombreAlbum").val("");
			}, duration: 400 });
		}

		/*
		@DESCRIPCION: muestra el formulario para modificar el nombre de un album.
		@AUTOR: F.Sole
		@FECHA: 2020/08/18 
		@VERSION: 1.0
		*/
		function mostrarVentanaModificarAlbum(){
			$("#sombra").show();
			$("#modificarAlbum").fadeIn({done: function() { 
				$("#modificarAlbumNuevoNombreAlbum").focus(); 
				$("#modificarAlbumNuevoNombreAlbum").val("");
			}, duration: 400 });
		}

		/*
		@DESCRIPCION: muestra el formulario para ver propiedades de un album.
		@AUTOR: F.Sole
		@FECHA: 2020/08/18 
		@VERSION: 1.0
		*/
		function mostrarVentanaPropiedadesAlbum(){
			$("#sombra").show();
			$("#propiedadesAlbum").fadeIn({done: function() { 
				
			}, duration: 400 });
		}

		/*
		@DESCRIPCION: muestra el formulario para ver propiedades de una foto.
		@AUTOR: F.Sole
		@FECHA: 2020/08/20
		@VERSION: 1.0
		*/
		function mostrarVentanaPropiedadesPicture(){
			$("#sombra").show();
			$("#propiedadesPicture").fadeIn({done: function() { 
				
			}, duration: 400 });
		}

		/*
		@DESCRIPCION: muestra el formulario para eliminar un album.
		@AUTOR: F.Sole
		@FECHA: 2020/08/19
		@VERSION: 1.0
		*/
		function mostrarVentanaEliminarAlbum(){
			$("#sombra").show();
			$("#eliminarAlbum").fadeIn({done: function() { 
				
			}, duration: 400 });
		}

		/*
		@DESCRIPCION: muestra el formulario para eliminar una foto.
		@AUTOR: F.Sole
		@FECHA: 2020/08/21
		@VERSION: 1.0
		*/
		function mostrarVentanaEliminarPicture(){
			$("#sombra").show();
			$("#eliminarPicture").fadeIn({done: function() { 
				
			}, duration: 400 });
		}
		
		/*
		@DESCRIPCION: muestra el formulario para mostrar fotos.
		@AUTOR: F.Sole
		@FECHA: 2020/08/21
		@VERSION: 1.0
		*/
		function mostrarVentanaFotos(src){
			$("#sombra").show();
			$("#verFotoDetalle").attr("src", src);
			var t = src.split("/");
			var n = capitalize(t[t.length-1].toLowerCase());
			$("#verFotoNombre").html(n);
			$("#verFoto").fadeIn({done: function() { 
				// var x = $("#verFoto").width()/2;
				// console.log(x);
				// $("#verFoto").css("margin-left","calc(50% - " + (x+15) + "px)");
			}, duration: 400 });
		}

		/*
		@DESCRIPCION: muestra el formulario para subir fotos.
		@AUTOR: F.Sole
		@FECHA: 2020/09/10
		@VERSION: 1.0
		*/
		function mostrarVentanaSubirFoto (){
			$("#sombra").show();
			$("#subirPicture").fadeIn({done: function() { 
				var data = {"controller": "1", "cmd": "1"};
				$.ajax({
					url: "./controller/main_controller.php",
					data: data,
					type: "POST",
					success: function (response) {
						var html = "";
						for (var i = 0; i < response.data.length; i++) {
							html += "<option value='"+response.data[i].id+"'>";
							html += response.data[i].nombre;
							html += "</option>";
						}
						$("#subirPicture_select").html(html);
					},
					dataType: "json"
				});	
			}, duration: 400 });
		}

		/*
		@DESCRIPCION: Devuelve un id random de 24 digitos.
		@AUTOR: F.Sole
		@FECHA: 2020/08/14 
		@VERSION: 1.0
		*/
		function newIdd(){
			chars = "0123456789ABCDEF";
			lon = 24;
			code = "";
			for (x = 0; x < lon; x++) {
				rand = Math.floor(Math.random() * chars.length);
				if (x % 6 == 0 && x > 0) {
					code += "-";
				}
				code += chars.substr(rand, 1);
			}
			return code;
		}

		/*
		@DESCRIPCION: Cierra la ventana que le paso el ID y quita la sombra.
		@AUTOR: F.Sole
		@FECHA: 2020/08/14 
		@VERSION: 1.0
		*/
		function cerrarVentana(id){
			$("#sombra").fadeOut(400);
			$("#" + id).fadeOut(400);
		}

		/*
		@DESCRIPCION: Comprueba si esta relleno el camp, si es asi crea el album.
		@AUTOR: F.Sole
		@FECHA: 2020/08/14 
		@VERSION: 1.0
		*/
		function crearAlbum(){
			//comprbamos que este lleno el campo
			var texto = $("#nuevoNombreAlbum").val().trim();
			if(texto.length > 0){
				//texto relleno
				var data = {"controller": "1", "cmd": "2", "data": texto};
				$.ajax({
					url: "./controller/main_controller.php",
					data: data,
					type: "POST",
					success: function (response) {
						if(response.data == 0){
							alert("No se pudo crear el album.");
						}else if(parseInt(response.data) == -1){
							alert("El album ya existe.");
						}else if(parseInt(response.data) == -10){
							alert("Imposible capturar datos.");
						}else{
							dibujarAlbum(response.data[0].nombre, response.data[0].id);
							$("#btnCancelarNuevoAlbum").click();
						}
					},
					dataType: "json"
				});	
			}else{
				//no relleno, es obligatorio rellenarlo
				$("#nuevoNombreAlbum").addClass("error");
				setTimeout(function (){
					$("#nuevoNombreAlbum").removeClass("error");	
				},200)
				
			}
		}

		/*
		@DESCRIPCION: Comprueba si esta relleno el camp, si es asi modifica el album.
		@AUTOR: F.Sole
		@FECHA: 2020/08/18 
		@VERSION: 1.0
		*/
		function modificarAlbum(){
			//comprbamos que este lleno el campo
			var texto = $("#modificarAlbumNuevoNombreAlbum").val().trim();
			if(texto.length > 0){
				//texto relleno
				var datos = {"id": $("#modificarAlbumNombreActual").attr("idalbum"), "nombre": texto, "prenombre": $("#modificarAlbumNombreActual").html()};
				var data = {"controller": "1", "cmd": "4", "data": datos};
				$.ajax({
					url: "./controller/main_controller.php",
					data: data,
					type: "POST",
					success: function (response) {
						var id = response.data[0].id;
						var txt = response.data[0].nombre;
						
						$("div[idalbum='" + id + "']").attr("texto", txt);
						var idd = $("div[idalbum='" + id + "']").attr("id").split("box_album")[1];
						
						$("#album_texto"+ idd).html(txt);
						cerrarVentana('modificarAlbum');
					},
					dataType: "json"
				});	
			}else{
				//no relleno, es obligatorio rellenarlo
				$("#modificarAlbumNuevoNombreAlbum").addClass("error");
				setTimeout(function (){
					$("#modificarAlbumNuevoNombreAlbum").removeClass("error");	
				},200)
				
			}
		}

		/*
		@DESCRIPCION: Borra un album de la interfaz segun el id del parametro.
		@AUTOR: F.Sole
		@FECHA: 2020/08/18 
		@VERSION: 1.0
		*/
		function borrarAlbum(idalbum){
			$(".box_album[idalbum=" + idalbum + "]").remove();
		}

		/*
		@DESCRIPCION: Borra una foto de la interfaz segun el id del parametro.
		@AUTOR: F.Sole
		@FECHA: 2020/08/21 
		@VERSION: 1.0
		*/
		function borrarFoto(idimagen){
			$("img[idimagen=" + idimagen + "]").remove();
		}

		

		/*
		@DESCRIPCION: escribe las propiedades de un album en la ventana.
		@AUTOR: F.Sole
		@FECHA: 2020/08/18 
		@VERSION: 1.0
		*/
		function propiedadesAlbum(id, nombre){
			var datos = {"id": id, "path": nombre};
			var data = {"controller": "1", "cmd": "5", "data": datos};
			$.ajax({
				url: "./controller/main_controller.php",
				data: data,
				type: "POST",
				success: function (response) {
					console.log(response);
					$("#propiedadesAlbumPropiertario").html(response.owner);
					$("#propiedadesAlbumGrupo").html(response.group);
					$("#propiedadesAlbumUltimoAcceso").html(response.last_access);
					$("#propiedadesAlbumUltimaModificacion").html(response.last_modified);
					$("#propiedadesAlbumPermisos").html(response.permisions);
				},
				dataType: "json"
			});	
			
		}

		/*
		@DESCRIPCION: Escribe las propiedades de una foto en la ventana correspondiente
		@AUTOR: F.Sole
		@FECHA: 2020/08/20 
		@VERSION: 1.0
		*/
		function propiedadesPicture(path){
			var datos = {"path": path};
			var data = {"controller": "2", "cmd": "1", "data": datos};
			$.ajax({
				url: "./controller/main_controller.php",
				data: data,
				type: "POST",
				success: function (response) {
					console.log(response);
					$("#propiedadesFotoProfundidad").html(response.bits);
					$("#propiedadesFotoAncho").html(response.width);
					$("#propiedadesFotoAlto").html(response.height);
					$("#propiedadesFotoTipo").html(response.mime);
					$("#propiedadesFotoResolucion").html((parseFloat(response.width*response.height)/1000000).toFixed(2));

					// $("#propiedadesAlbumUltimaModificacion").html(response.last_modified);
					// $("#propiedadesAlbumPermisos").html(response.permisions);
				},
				dataType: "json"
			});	
		}

		/*
		@DESCRIPCION: descarga el album en formato zip.
		@AUTOR: F.Sole
		@FECHA: 2020/08/18 
		@VERSION: 1.0
		*/
		function descargarAlbum(id, nombre){
			$("#cargando").fadeIn(400);
			$("#textoCargando").html("Comprimiendo...")
			var datos = {"id": id, "path": nombre};
			var data = {"controller": "1", "cmd": "6", "data": datos};
			$.ajax({
				url: "./controller/main_controller.php",
				data: data,
				type: "POST",
				success: function (response) {
					$("#textoCargando").html("");
					if(response.status > 0){
						$("#downloader").attr("href", response.zip_file)
						var a = document.getElementById("downloader");
						a.click();	
					}else{
						$("#textoCargando").html("Album vacio...");
					}
					setTimeout(function (){
						$("#cargando").fadeOut(400);
						$("#textoCargando").html("");
					},1500)
					
				},
				dataType: "json"
			});	
		}

		/*
		@DESCRIPCION: descarga una foto en formato zip.
		@AUTOR: F.Sole
		@FECHA: 2020/08/21 
		@VERSION: 1.0
		*/
		function descargarPicture(path){
			$("#cargando").fadeIn(400);
			$("#textoCargando").html("Descargando...")
			var ruta = path.split("./media/source/")[1];
			var datos = {"path": ruta};
			var data = {"controller": "2", "cmd": "2", "data": datos};
			$.ajax({
				url: "./controller/main_controller.php",
				data: data,
				type: "POST",
				success: function (response) {
					$("#textoCargando").html("");
					console.log(response.path_file);
					$("#downloader").attr("href", response.path_file)
					var a = document.getElementById("downloader");
					a.click();
					setTimeout(function (){
						$("#cargando").fadeOut(400);
					},1000)
				},
				dataType: "json"
			});	
		}

		/*
		@DESCRIPCION: borrar el album seleccionado.
		@AUTOR: F.Sole
		@FECHA: 2020/08/19 
		@VERSION: 1.0
		*/
		function eliminarAlbum(){
			$("#cargando").fadeIn(400);
			$("#textoCargando").html("Eliminando...")
			var datos = {"id": $("#eliminarAlbum").attr("idalbum"), "path": $("#eliminarNombreAlbum").html() };
			var data = {"controller": "1", "cmd": "7", "data": datos};
			$.ajax({
				url: "./controller/main_controller.php",
				data: data,
				type: "POST",
				success: function (response) {
					$("#cargando").fadeOut(400);
					$("#textoCargando").html("");
					cerrarVentana("eliminarAlbum");
					borrarAlbum(response.idalbum);
				},
				dataType: "json"
			});	
		}

		/*
		@DESCRIPCION: borrar el la foto seleccionada.
		@AUTOR: F.Sole
		@FECHA: 2020/08/21 
		@VERSION: 1.0
		*/
		function eliminarPicture(){
			$("#cargando").fadeIn(400);
			$("#textoCargando").html("Eliminando...")
			var datos = {"id": $("#eliminarPictureImagen").attr("idimagen"), "path": $("#eliminarPictureImagen").attr("src") };
			var data = {"controller": "2", "cmd": "3", "data": datos};
			$.ajax({
				url: "./controller/main_controller.php",
				data: data,
				type: "POST",
				success: function (response) {
					$("#cargando").fadeOut(400);
					$("#textoCargando").html("");
					cerrarVentana("eliminarPicture");
					borrarFoto(response.idimagen);
				},
				dataType: "json"
			});	
		}

		/*
		@DESCRIPCION: Funcion que nos indica si el navegador es de movil o no.
		@AUTOR: F.Sole
		@FECHA: 2020/08/18 
		@VERSION: 1.0
		*/
		window.mobileCheck = function() {
			let check = false;
			(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
			return check;
		};

		/*
		@DESCRIPCION: Funcion sube fotos al servidor.
		@AUTOR: F.Sole
		@FECHA: 2020/09/10 
		@VERSION: 1.0
		*/
		function subirFotos(){
			$("#cargando").fadeIn(400);
			$("#textoCargando").html("Subiendo...");

			var arrayAlbums = $('#subirPicture_select').val();
			var arrayAlbumsName = [];
			$('#subirPicture_select option:selected').each(function(index, el) {
				arrayAlbumsName.push($(this).html());
			});
			var type = fileName_upload.name.split(".")[fileName_upload.name.split(".").length - 1];
			$.ajax({
				url: "./controller/uploader.php",
				data: {"img":base64_file.split("base64,")[1], "tipo": type},
				type: "POST",
				success: function (response) {
					console.log("subido");
					var tipo = response.split(".")[response.split(".").length - 1];
					if(tipo.toUpperCase() == "RAR" || tipo.toUpperCase() == "ZIP"){
						descomprimir(response, tipo, arrayAlbums, arrayAlbumsName);
					}else{
						var a = [];
						a.push(response)
						asignarFotos(a, arrayAlbums, arrayAlbumsName);
					}
				}
			});
			// .done(function(e) {
			// 	console.log("done");
			// 	console.log(e);
			// })
			// .fail(function(e) {
			// 	console.log("fail");
			// 	console.log(e);
			// })
			// .always(function(e) {
			// 	console.log("always");
			// 	console.log(e);
			// });	
		}

		/*
		@DESCRIPCION: Funcion manda al servidor el nombre del archivo a descomprimir.
		@AUTOR: F.Sole
		@FECHA: 2020/09/17 
		@VERSION: 1.0
		*/
		function descomprimir(name, type, arrayAlbums, arrayAlbumsName){
			$("#cargando").fadeIn(400);
			$("#textoCargando").html("Descomprimiendo...");
			var datos = {"name": name, "type": type };
			var data = {"controller": "2", "cmd": "4", "data": datos};
			$.ajax({
				url: "./controller/main_controller.php",
				data: data,
				type: "POST",
				success: function (response) {
					console.log(response);
					if(response.status == 0){
						asignarFotos(response.file, arrayAlbums, arrayAlbumsName, 1);
					}
				}, dataType: "json"
			});
		}

		/*
		@DESCRIPCION: Funcion manda al servidor el nombre de la foto y los albums.
		@AUTOR: F.Sole
		@FECHA: 2020/09/17 
		@VERSION: 1.0
		*/
		function asignarFotos(name, arrayAlbums, arrayAlbumsName, carpeta){
			//si carpeta = 1 se recorrera, sino el fichero;
			if (carpeta == undefined) {
				carpeta = 0;
			}

			$("#cargando").fadeIn(400);
			$("#textoCargando").html("Asignando fotos...");
			var datos = {"name": name, "albums": arrayAlbums, "albums_name" : arrayAlbumsName ,"is_folder": carpeta };
			var data = {"controller": "2", "cmd": "5", "data": datos};
			$.ajax({
				url: "./controller/main_controller.php",
				data: data,
				type: "POST",
				success: function (response) {
					if(response.status == 0){
						$("#textoCargando").html("Completado");
						$("#cargando").fadeOut(
						{
							done: function() { 
								$("#textoCargando").html("");
								cerrarVentana('subirPicture');
								$('#subirPicture_select option:selected').each(function(index, el) {
									$(this).removeAttr("selected");
								});
								$("#subirPicture_input").val("");
								fileName_upload = undefined;
							} 
						}, 1000);
					}
				}, dataType: 'json'
			});
		}

		function getBase64(file) {
			var reader = new FileReader();
			reader.readAsDataURL(file);
			reader.onload = function () {
				base64_file = reader.result;
			};
			reader.onerror = function (error) {
				console.log('Error: ', error);
			};
		}

		$(document).ready(function() {
			//controlamos los botones del menú
			$("#menu").click(function(e){
				// El switch utiliza los IDs de los <li> del menú
				var opcion = e.target.id.split("_")[0];
				var idOpcion =  e.target.id.split("_")[1];
				switch(opcion){
					case "change":
						//
						$("#menu").css("display", "none");
						mostrarVentanaModificarAlbum();
						var texto = $("#box_album_"+idOpcion).attr("texto");
						$("#modificarAlbumNombreActual").html(texto);
						var idalbum = $("#box_album_"+idOpcion).attr("idalbum");
						$("#modificarAlbumNombreActual").attr("idalbum", idalbum);
					//	
					break;	
					
					case "properties":
						//
						$("#menu").css("display", "none");
						mostrarVentanaPropiedadesAlbum();
						var texto = $("#box_album_"+idOpcion).attr("texto");
						var idalbum = $("#box_album_"+idOpcion).attr("idalbum");
						propiedadesAlbum(idalbum, texto);
					//
					break;
					
					case "download":
						//
						$("#menu").css("display", "none");
						var texto = $("#box_album_"+idOpcion).attr("texto");
						var idalbum = $("#box_album_"+idOpcion).attr("idalbum");
						descargarAlbum(idalbum, texto);
					//
					break;
					
					case "delete":
						//
						$("#menu").css("display", "none");
						mostrarVentanaEliminarAlbum();
						var texto = $("#box_album_"+idOpcion).attr("texto");
						var idalbum = $("#box_album_"+idOpcion).attr("idalbum");
						$("#eliminarNombreAlbum").html(texto);
						$("#eliminarAlbum").attr("idalbum", idalbum);
					//
					break;
					//PICTURE/////////////////////////////////////////////////
					
					case "propertiesPicture":
						//
						$("#menu").css("display", "none");
						mostrarVentanaPropiedadesPicture();
						var path = $("#box_picture_"+idOpcion).attr("src");
						propiedadesPicture(path);
					//
					break;
					
					case "downloadPicture":
						//
						$("#menu").css("display", "none");
						var path = $("#box_picture_"+idOpcion).attr("src");
						descargarPicture(path);
					//
					break;
					
					case "deletePicture":
						//
						$("#menu").css("display", "none");
						mostrarVentanaEliminarPicture();
						var path = $("#box_picture_"+idOpcion).attr("src");
						var idImagen = $("#box_picture_"+idOpcion).attr("idimagen");

						$("#eliminarPictureImagen").attr("src", path);
						$("#eliminarPictureImagen").attr("idimagen", idImagen);
					//
					break;
				}

				$("#btnUpload").click(function(event) {
					
				});
			});	

			/* mostramos el menú si hacemos click derecho
			con el ratón */
			$(document).bind("contextmenu", function(e){
				if(window.mobileCheck()){
					//nada es movil
				}else{
					$("#menu").css({'display':'none'});					
				}
				return false;
			});

			//cuando hagamos click, el menú desaparecerá
			$(document).click(function(e){
				if(e.button == 0){
					$("#menu").css("display", "none");
				}
			});

			//si pulsamos escape, el menú desaparecerá
			$(document).keydown(function(e){
				if(e.keyCode == 27){
					$("#menu").css("display", "none");
				}
			});

			$("#btnAtras").click(function(e){
				$("#fotos_div").fadeOut({done: function() { $("#albums_div").fadeIn(); } });
				//$(this).hide();
				//$("#btnUpload").hide();
			})

			$("#selectSize").click(function(event) {
				var status = $("#selectSize").attr("expanded");
				if(status == "expanded"){
					$("div[name='selectSize'][like='option']").hide();
					$("div[name='selectSize'][like='option'][selected]").show();
					$("#selectSize").removeAttr("expanded");
				}else{
					$("div[name='selectSize'][like='option']").show("fadeIn");
					$("#selectSize").attr("expanded", "expanded");
				}

			});

			$("div[name='selectSize'][like='option']").each(function(index, el) {
				$(this).click(function(event) {
					$("div[name='selectSize'][like='option']").removeAttr("selected").css("display", "none");
					$(this).attr("selected", "").show();
					var type = $(this).attr("value");
					switch (type){
						case "s":
						//$(".box_album").css("padding", "14px");
						$(".img").css({
							"width": "150px",
							"height": "150px"
						});
						break;
						case "m":
						//$(".box_album").css("padding", "34px");
						$(".img").css({
							"width": "250px",
							"height": "175px"
						});
						break;
						case "l":
						//$(".box_album").css("padding", "54px");
						$(".img").css({
							"width": "350px",
							"height": "200px"
						});
						break;
					}
				});				
			});

			$("#btnUpload").click(function(event) {
				mostrarVentanaSubirFoto();
			});

			$("#subirPicture_input").change(function(e){
				fileName_upload = e.target.files[0];
				getBase64(fileName_upload);
			});

			$("div[name='selectSize'][like='option']").hide();
			$("div[name='selectSize'][like='option'][selected]").show();
		});

	</script>
	<style>
		@font-face {
			font-family: album_texto;
			src: url("./media/fonts/Autography.otf");
		}

		@font-face {
			font-family: texto;
			src: url("./media/fonts/Roboto-Regular.ttf");
		}

		@media (max-width: 360px) {
			#inputBuscador input {
				width: 150px !important;
			}
		}

		body{
			margin: 0;
			border: 0;
			padding: 0;
			width: 100vw;
			background-color: #ebf5fb;
		}

		#albums_div{
			width: 80vw;
			margin-left: 10vw;
			margin-top: 10vh;
		}

		.box_album{
			background-color: #5dade2;
			padding: 14px;
			border-radius: 24px;
			float:left;
			margin-left: 15px;
			margin-top: 15px;
			cursor:pointer;		
			-webkit-box-shadow: 2px 7px 6px -6px rgba(0,0,0,0.75);
			-moz-box-shadow: 2px 7px 6px -6px rgba(0,0,0,0.75);
			box-shadow: 2px 7px 6px -6px rgba(0,0,0,0.75);
			transition: all .3s;
		}

		.box_album:hover{
			-webkit-box-shadow: 2px 10px 6px -6px rgba(0,0,0,0.75);
			-moz-box-shadow: 2px 10px 6px -6px rgba(0,0,0,0.75);
			box-shadow: 2px 10px 6px -6px rgba(0,0,0,0.75);
			transition: all .3s;
		}

		.texto_album{
			color: white;
			user-select:none;
			font-family: album_texto;
			font-size: 2.5em;
		}

		.album{
			float:left;
			width: 100%;
			text-align: center;
		}

		#cargando{
			position: fixed;
			z-index: 99;
			height: 100vh;
			width: 100vw;
			background-color: rgba(0,0,0,0.5);
			top: 0;
			left: 0;
		}

		#sombra{
			position: fixed;
			height: 100vh;
			width: 100vw;
			background-color: rgba(0,0,0,0.5);
			top: 0;
			left: 0;
			display: none;
		}

		#cargando img{
			margin-left: calc(50vw - 35px);
			margin-top: 60vh;
		}

		##nuevoAlbum{
			width: 25vw;
			margin-left: 35.5vw;
			top: 40vh;
			background-color: #5dade2;
			position: fixed;
			font-family: texto;
			padding: 15px;
			color: white;
			border-radius: 15px;
		}

		.formulario{
			#width: 25vw;
			#margin-left: 35.5vw;
			width: 80vw;
			margin-left: 10vw;
			top: 40vh;
			background-color: #5dade2;
			position: fixed;
			font-family: texto;
			padding: 15px;
			color: white;
			border-radius: 15px;
		}


		.titulo{
			font-size: 1.5em;
			border-bottom: 1px solid;
			user-select: none;
			font-weight: 900;
			letter-spacing: 2px;
		}

		#nuevoNombreAlbum{
			background: transparent;
			border-bottom: 1px solid black;
			border-style: solid;
			border: none;
			border-bottom: 1px solid black;
			color: white;
			margin-left: 10px;
		}

		#nuevoNombreAlbum::placeholder {
			color: white;
		}

		:focus {
			outline: none;
		}

		#btnCancelarNuevoAlbum{
			float: right;
			#width: 50%;
			margin-top: 25px;
			text-align: right;
			text-decoration: underline;
			color: white;
			cursor: pointer;
			user-select: none;
			margin-left: 25px;
			padding: 7px;
			background-color: #cb4335;
			border-radius: 14px;
		}

		#btnAceptarNuevoAlbum{
			float: right;
			#width: 50%;
			margin-top: 25px;
			text-align: right;
			text-decoration: underline;
			color: white;
			cursor: pointer;
			user-select: none;
			padding: 7px;
			background-color: #f1c40f;
			border-radius: 14px;

		}

		.cancelar{
			float: right;
			#width: 50%;
			margin-top: 25px;
			text-align: right;
			text-decoration: underline;
			color: white;
			cursor: pointer;
			user-select: none;
			margin-left: 25px;
			padding: 7px;
			background-color: #cb4335;
			border-radius: 14px;
		}

		.aceptar{
			float: right;
			#width: 50%;
			margin-top: 25px;
			text-align: right;
			text-decoration: underline;
			color: white;
			cursor: pointer;
			user-select: none;
			padding: 7px;
			background-color: #f1c40f;
			border-radius: 14px;

		}

		.error {
			position: relative;
			animation: shake .1s linear;
			animation-iteration-count: 3;
		}

		.info_hover{
			float: left;
			position: fixed;
			background: white;
			padding: 10px;
			border-radius: 15px;
			text-align: left;
			margin-top: -107px;
			margin-left: 25px;
			-webkit-box-shadow: 0px 0px 10px 5px rgba(0,0,0,0.75);
			-moz-box-shadow: 0px 0px 10px 5px rgba(0,0,0,0.75);
			box-shadow: 0px 0px 10px 5px rgba(0,0,0,0.75);
			font-family: texto;
			color: #560a0a;
			font-size: 0.75em;
		}

		.info_hover:before {
			content: "";
			width: 0px;
			height: 0px;
			position: absolute;
			border-left: 10px solid transparent;
			border-right: 10px solid white;
			border-top: 10px solid transparent;
			border-bottom: 10px solid transparent;
			left: -20px;
			top: 83px;
		}

		@keyframes shake {
			0% { left: -5px; }
			100% { right: -5px; }
		}

		#menu ul{
			list-style: none;
			background: white;
			padding: 10px;
			border-radius: 15px;
			text-align: left;
			-webkit-box-shadow: 0px 0px 10px 5px rgba(0,0,0,0.75);
			-moz-box-shadow: 0px 0px 10px 5px rgba(0,0,0,0.75);
			box-shadow: 0px 0px 10px 5px rgba(0,0,0,0.75);
			font-family: texto;
			color: #560a0a;
			font-size: 1em;
			user-select: none;
		}

		#menu li{
			user-select: none;
			cursor: pointer;	
		}

		#menu li:hover{
			text-decoration: underline;
		}

		#menu li hr{
			height: 1px;
			background: black;
			border: none;
		}
		#menu li hr:hover {
			user-select: none;
			cursor: default;	
		}

		.img {
			float: left;
			width:  150px;
			height: 150px;
			object-fit: cover;
			margin-left: 15px;
			margin-top: 15px;
			border: 1px solid #560a0a;
			cursor: pointer;
			transition: all .3s;
			border-radius: 15px;
		}

		.img:hover {
			/*width:  160px;
			height: 160px;
			object-fit: cover;
			transition: all .3s;
			position: relative;
			z-index: 1;*/
			-webkit-box-shadow: 2px 10px 6px -6px rgba(0,0,0,0.75);
			-moz-box-shadow: 2px 10px 6px -6px rgba(0,0,0,0.75);
			box-shadow: 2px 10px 6px -6px rgba(0,0,0,0.75);
			transition: all .3s;
		}

		#opciones{
			width: 80vw;
			margin-left: 10vw;
			margin-top: 25px;
			height: 54px;
		}

		#btnAtras{
			cursor:pointer;
			#display: none;
		}

		#btnUpload{
			cursor:pointer;
			#display: none;
			margin-left: 50px;
		}

		#fotos_div{
			width: 80vw;
			margin-left: 10vw;
			margin-top: 10vh;
		}

		.visor{
			display: block;
			width: auto;
			height: auto;
			max-width: 100%;
			max-height: 90%;
			margin: 20px auto;
		}

		.formulario-visor{
			#width: 25vw;
			#margin-left: 35.5vw;
			top: 10px;
			#background-color: #5dade2;
			#background-color:bisque;
			background-color:white;
			position: fixed;
			font-family: texto;
			padding: 15px;
			color: white;
			border-radius: 15px;
			width: calc(100vw - 34px);
			margin-left: 4px;
			text-align: center;
			max-height: calc(100vh - 50px);
		}

		.img-detalle{
			width: auto;
			height: auto;
			max-width: 100%;
			max-height: 86vh;
		}

	</style>
</head>
<body>
	<div id="cargando">
		<img height="70" src="./media/img/loading.gif" alt="Cargando...">
		<p id="textoCargando" style="text-align: center;color: white;font-family: texto;font-size: 2em;"></p>
	</div>
	<div id="sombra"></div>
	<div id="opciones">
		<div id="btnAtras" style="float: left;"><img height="50" src="./media/img/back.png" alt="atras"></div>
		<div id="inputBuscador" style="float: left;">
			<input type="text" style="width: 235px;border: none;background: transparent;border-bottom: 1px solid;margin-top: 15px;margin-left: 15px;" placeholder="Busca albums, foto, etiquetas, fecha..." />
			<img height="20" src="./media/img/buscar.png" alt="busca" style="margin-left: -10px;cursor: pointer;">
		</div>
		<div id="btnLabel" style="float: left;margin-left: 15px;cursor: pointer;">
			<img height="50" src="./media/img/label.png" alt="label">
		</div>
		<div id="btnSize" style="float: left;margin-left: 15px;">
			<div id="selectSize" like="select" style="position: absolute;">
				<div name="selectSize" selected="" value="s" like="option" style="cursor: pointer;">
					<div >
						<img height="50" src="./media/img/resize_up_s.png" alt="tamaño_s">
					</div>
				</div>
				<div name="selectSize" value="m" like="option" style="cursor: pointer;">
					<div>
						<img height="50" src="./media/img/resize_up_m.png" alt="tamaño_m">
					</div>
				</div>
				<div name="selectSize" value="l" like="option" style="cursor: pointer;">
					<div>
						<img height="50" src="./media/img/resize_up_l.png" alt="tamaño_l">
					</div>
				</div>
			</div>
		</div>
		<div id="btnUpload" style="float: right;"><img height="50" src="./media/img/upload.png" alt="subir"></div>

	</div>

	<div id="nuevoAlbum" class="formulario" style="display: none;">
		<div class="titulo">Crear album</div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Nombre<input type="text" name="nuevoNombreAlbum" id="nuevoNombreAlbum" value="" placeholder="Vacaciones en..."></div>
		<div id="btnCancelarNuevoAlbum" class="cancelar" onclick="cerrarVentana('nuevoAlbum')">Cancelar</div><div id="btnAceptarNuevoAlbum" class="aceptar" onclick="crearAlbum();">Aceptar</div>
	</div>
	
	<div id="modificarAlbum"  class="formulario" style="display: none;">
		<div class="titulo">Cambiar nombre</div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Nombre actual &nbsp;&nbsp;<span id="modificarAlbumNombreActual"></span></div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Nombre nuevo <input type="text" name="modificarAlbumNuevoNombreAlbum" id="modificarAlbumNuevoNombreAlbum" value="" placeholder="Vacaciones en..."></div>
		<div id="btnCancelarNuevoNombreAlbum" class="cancelar" onclick="cerrarVentana('modificarAlbum')">Cancelar</div><div class="aceptar" id="btnAceptarNuevonombreAlbum" onclick="modificarAlbum();">Aceptar</div>
	</div>
	
	<div id="propiedadesAlbum"  class="formulario" style="display: none;">
		<div class="titulo">Propiedades de album</div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Propietario: &nbsp;&nbsp;<span id="propiedadesAlbumPropiertario"></span></div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Grupo: &nbsp;&nbsp;<span id="propiedadesAlbumGrupo"></span></div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Ultimo acceso: &nbsp;&nbsp;<span id="propiedadesAlbumUltimoAcceso"></span></div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Ultima modificación: &nbsp;&nbsp;<span id="propiedadesAlbumUltimaModificacion"></span></div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Permisos: &nbsp;&nbsp;<span id="propiedadesAlbumPermisos"></span></div>
		<div id="btnCerrarPropiedadesAlbum" class="aceptar" onclick="cerrarVentana('propiedadesAlbum')">Cerrar</div>
	</div>
	
	<div id="propiedadesPicture"  class="formulario" style="display: none;">
		<div class="titulo">Propiedades de foto</div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Alto: &nbsp;&nbsp;<span id="propiedadesFotoAlto"></span> px</div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Ancho: &nbsp;&nbsp;<span id="propiedadesFotoAncho"></span> px</div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Profundidad: &nbsp;&nbsp;<span id="propiedadesFotoProfundidad"></span> bits</div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Resolucion: &nbsp;&nbsp;<span id="propiedadesFotoResolucion"></span> MP</div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">Tipo imagen: &nbsp;&nbsp;<span id="propiedadesFotoTipo"></span></div>
		<div id="btnCerrarPropiedadesPicture" class="aceptar" onclick="cerrarVentana('propiedadesPicture')">Cerrar</div>
	</div>
	
	<div id="eliminarAlbum" class="formulario" style="display: none;">
		<div class="titulo">Eliminar album</div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">
			<p style="font-weight: 900;">Atencion!</p>
			<p>Esta accion eliminara el album <span id="eliminarNombreAlbum" style="text-decoration: underline;"></span> y todo su contenido.</p>
			<p>¿Desea continuar?</p>
		</div>
		<div id="btnCancelarEliminarAlbum" class="cancelar" onclick="cerrarVentana('eliminarAlbum')" style="background-color: #f1c40f;">Cancelar</div><div id="btnAceptarEliminarAlbum" class="aceptar" style="background-color: #cb4335;" onclick="eliminarAlbum();" ><img style="margin-left: 3px;margin-right: 5px;" alt="papelera" src="./media/img/bin_w.png" height="15" />Borrar</div>
	</div>
	
	<div id="eliminarPicture" class="formulario" style="display: none;">
		<div class="titulo">Eliminar foto</div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">
			<p style="font-weight: 900;">Atencion!</p>
			<p>Esta accion eliminara la foto <span id="eliminarNombreAlbum" style="text-decoration: underline;"></span></p>
			<div><img style="max-height:150px;max-width:150px" id="eliminarPictureImagen" src="" alt="imgen_borrar"></div>
			<p>¿Desea continuar?</p>
		</div>
		<div id="btnCancelarEliminarPicture" class="cancelar" onclick="cerrarVentana('eliminarPicture')" style="background-color: #f1c40f;">Cancelar</div><div id="btnAceptarEliminarPicture" class="aceptar" style="background-color: #cb4335;" onclick="eliminarPicture();" ><img style="margin-left: 3px;margin-right: 5px;" alt="papelera" src="./media/img/bin_w.png" height="15" />Borrar</div>
	</div>
	
	<div id="subirPicture" class="formulario" style="display: none;">
		<div class="titulo">Subir fotos</div>
		<div style="float: left;margin-top: 10px;width: 100%;user-select: none;">
			<p>
				Destino:  
				<select id="subirPicture_select" multiple>
					
				</select>
			</p>
			<div>Origen de fotos: 
				<input type="file" id="subirPicture_input" accept=".zip,image/*">
			</div>
		</div>
		<div id="btnCancelarSubirPicture" class="cancelar" onclick="cerrarVentana('subirPicture')">Cancelar</div>
		<div id="btnAceptarSubirPicture" class="aceptar" onclick="subirFotos();" >Subir fotos</div>
	</div>
	<div id="verFoto" class="formulario-visor" style="display: none;">
		<div class="titulo" style="color:#560a0a;float: left;width: 100%; border-bottom: 1px solid;margin-bottom: 10px;"><span style="float: left;position: absolute;left: 20px;" id="verFotoNombre"></span><span style="float: right;cursor: pointer;" onclick="cerrarVentana('verFoto');">X</span></div>
		<img id="verFotoDetalle" class="img-detalle" style="#border: 1px solid;" />
	</div>
	<div id="albums_div">
		<div id=""></div>
	</div>
	<div id="fotos_div">

	</div>
	<div id="menu">

	</div>
	<a id="downloader" style="display: none;" href=""></a>
</body>
</html>
<?php
// $handle = opendir(dirname(realpath(__FILE__)).'/test/');
// while($file = readdir($handle)){
//   if($file !== '.' && $file !== '..'){
//     echo '<img src="./test/'.$file.'" border="0" />';
//   }
// }

?>