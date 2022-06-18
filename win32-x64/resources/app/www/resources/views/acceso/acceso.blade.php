<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<link rel="shorcut icon" href="/img/favicon.ico"/>
	<title>SistemaC34++ - Pedidos</title>
	<link rel="stylesheet" href="{{ mix('/css/app.css') }}">
	<link href="/iconos/styles.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
	<span id="app">
		<acceso inline-template>
			<div class="container h-100">
				<div class="row h-100 justify-content-center">
					<div class="col col-sm-9 col-md-7 col-lg-5" style="display: block;margin-top: 2rem; margin-bottom: 2rem">
						<form v-on:submit.prevent>
							<div class="text-center" style="margin-bottom: 1.3rem">
								<div class="navbar-brand" style="padding: 5px"><img alt="Brand" src="/img/logo-text-white.png" width="100px"></div>
								<h4 @dblclick="mdlShowConf()">Acceso al Sistema</h4>
							</div>
							
							<template v-if="errors != null">
								<p>
									<div class="text-danger" v-for="error in errors" v-text="error[0]"></div>
								</p>
							</template>

							@if (env('APP_TYPE') == 'web')
								<!-- <div class="form-group">
									<input type="tel" class="form-control form-control-round text-uppercase form-control-lg" placeholder="RUC" maxlength="11" v-model="usuario.n_ruc" id="n_ruc">
								</div> -->
								<div class="input-group mb-3">
									<input type="tel" class="form-control form-control-round text-uppercase form-control-lg" placeholder="RUC" maxlength="14" v-model="usuario.n_ruc" id="n_ruc">
									<div class="input-group-append">
										<button @click="showMdlTeclado('usuario.n_ruc',usuario.n_ruc, 'text','numeric')"
												 class="btn btn-lg btn-secondary form-control-round px-4" type="button">
											<span class="icon-keyboard align-middle"></span>
										</button>
									</div>
								</div>
							@endif
							<!-- <div class="form-group">
								<input type="text" class="form-control form-control-round text-uppercase form-control-lg" placeholder="Usuario" v-model="usuario.Usuario" id="Usuario">
							</div> -->
							<div class="input-group mb-3">
								<input type="text" class="form-control form-control-round text-uppercase form-control-lg" placeholder="Usuario" v-model="usuario.Usuario" id="Usuario">
								<div class="input-group-append">
									<button @click="showMdlTeclado('usuario.Usuario',usuario.Usuario, 'text')"
											class="btn btn-lg btn-secondary form-control-round px-4" type="button">
										<span class="icon-keyboard align-middle"></span>
									</button>
								</div>
							</div>
							<!-- <div class="form-group">
								<input type="password" class="form-control form-control-round text-uppercase form-control-lg" placeholder="Contraseña" v-model="usuario.l_pass" id="l_pass">
							</div> -->
							<div class="input-group mb-3">
								<input type="password" class="form-control form-control-round text-uppercase form-control-lg" placeholder="Contraseña" v-model="usuario.l_pass" id="l_pass">
								<div class="input-group-append">
									<button @click="showMdlTeclado('usuario.l_pass','', 'password')" 
											class="btn btn-lg btn-secondary form-control-round px-4" type="button">
										<span class="icon-keyboard align-middle"></span>
									</button>
								</div>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-block btn-round-lg btn-lg" v-on:click="postLogin()" id="btnAceptar">Aceptar</button>
							</div>
							<p class="text-muted text-center">Sistema C34  ©{{  (new \Datetime)->format('Y') }}</p>
						</form>
						<!--Modificar Item -> Cantidad - -->
						<div id="mdlConf" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header bg-dark text-white">
										<h5 class="modal-title">Configuración</h5>
									</div>
									<div class="modal-body text-dark">
										<form>
											<!-- <label class="custom-control custom-checkbox">
											  <input v-model="configuracion.q_tactil" type="checkbox" class="custom-control-input">
											  <span class="custom-control-indicator"></span>
											  <span class="custom-control-description text-dark">Habilitar teclado táctil ?</span>
											</label> -->
											<div class="form-check">
												<input v-model="configuracion.q_tactil"
													   class="form-check-input" type="checkbox" value="" id="q_tactil">
												<label class="form-check-label" for="q_tactil">
													Habilitar teclado táctil ?
												</label>
											</div>
										</form>
									</div>
									<div class="modal-footer">
										<button @click="setConf()" type="button" class="btn btn-primary btn-lg">Aceptar</button>
										<button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cerrar</button>
									</div>
								</div>
							</div>
						</div>
						<!---->
					</div>
				</div>
				<!-- Dynamic Components -->
				<component :is="currentView" v-bind="currentProps" @hide-mdlteclado="hideMdlTeclado">
				</component>
				<!--  -->
			</div>
		</acceso>
	</span>
	@include('Teclado')
	<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>