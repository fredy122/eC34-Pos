<script type="text/template" id="tmpTeclado">
	<div>
		<!-- Teclado Alfanumérico -->
		<div id="mdlTeclado" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow: hidden;">
			<div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 90%">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white py-2">
						<h6 class="modal-title">Teclado</h6>
					</div>
					<div class="modal-body">
						<div class="row mb-2">
							<div class="col-12">
								<template v-if="_type == 'password'">
									<!-- Input tipo password -->
									<input v-model="texto"
										type="password" class="form-control form-control-lg" readonly>
									<!--  -->
								</template>
								<template v-else>
									<!-- Input tipo texto -->
									<textarea v-model="texto" class="form-control form-control-lg" cols="30" rows="3" readonly></textarea>
									<!--  -->
								</template>
							</div>
						</div>
						<div class="row no-gutters mb-1">
							<div class="col pr-1">
								<button @click="texto += '1'" class="btn btn-lg btn-block btn-default py-3">1</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '2'" class="btn btn-lg btn-block btn-default py-3">2</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '3'" class="btn btn-lg btn-block btn-default py-3">3</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '4'" class="btn btn-lg btn-block btn-default py-3">4</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '5'" class="btn btn-lg btn-block btn-default py-3">5</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '6'" class="btn btn-lg btn-block btn-default py-3">6</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '7'" class="btn btn-lg btn-block btn-default py-3">7</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '8'" class="btn btn-lg btn-block btn-default py-3">8</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '9'" class="btn btn-lg btn-block btn-default py-3">9</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '0'" class="btn btn-lg btn-block btn-default py-3">0</button>
							</div>
						</div>
						<div class="row no-gutters mb-1">
							<div class="col pr-1">
								<button @click="texto += 'Q'" class="btn btn-lg btn-block btn-default py-3">Q</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'W'" class="btn btn-lg btn-block btn-default py-3">W</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'E'" class="btn btn-lg btn-block btn-default py-3">E</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'R'" class="btn btn-lg btn-block btn-default py-3">R</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'T'" class="btn btn-lg btn-block btn-default py-3">T</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'Y'" class="btn btn-lg btn-block btn-default py-3">Y</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'U'" class="btn btn-lg btn-block btn-default py-3">U</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'I'" class="btn btn-lg btn-block btn-default py-3">I</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'O'" class="btn btn-lg btn-block btn-default py-3">O</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'P'" class="btn btn-lg btn-block btn-default py-3">P</button>
							</div>
							<div class="col pr-1">
								<button @click="backspace" class="btn btn-lg btn-block btn-info py-3">
									<span class="icon-backspace-outline"></span>
								</button>
							</div>
						</div>
						<div class="row no-gutters mb-1">
							<div class="col pr-1">
								<button @click="texto += 'A'" class="btn btn-lg btn-block btn-default py-3">A</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'S'" class="btn btn-lg btn-block btn-default py-3">S</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'D'" class="btn btn-lg btn-block btn-default py-3">D</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'F'" class="btn btn-lg btn-block btn-default py-3">F</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'G'" class="btn btn-lg btn-block btn-default py-3">G</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'H'" class="btn btn-lg btn-block btn-default py-3">H</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'J'" class="btn btn-lg btn-block btn-default py-3">J</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'K'" class="btn btn-lg btn-block btn-default py-3">K</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'L'" class="btn btn-lg btn-block btn-default py-3">L</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'Ñ'" class="btn btn-lg btn-block btn-default py-3">Ñ</button>
							</div>
							<div class="col pr-1">
								<button @click="texto = ''" class="btn btn-lg btn-block btn-warning py-3">BORRAR</button>
							</div>
						</div>
						<div class="row no-gutters mb-1">
							<div class="col pr-1">
								<button @click="texto += 'Z'" class="btn btn-lg btn-block btn-default py-3">Z</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'X'" class="btn btn-lg btn-block btn-default py-3">X</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'C'" class="btn btn-lg btn-block btn-default py-3">C</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'V'" class="btn btn-lg btn-block btn-default py-3">V</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'B'" class="btn btn-lg btn-block btn-default py-3">B</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'N'" class="btn btn-lg btn-block btn-default py-3">N</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += 'M'" class="btn btn-lg btn-block btn-default py-3">M</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += ','" class="btn btn-lg btn-block btn-default py-3">,</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '.'" class="btn btn-lg btn-block btn-default py-3">.</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '-'" class="btn btn-lg btn-block btn-default py-3">-</button>
							</div>
						</div>
						<div class="row no-gutters mb-1">
							<div class="col pr-1">
								<button @click="texto += '+'" class="btn btn-lg btn-block btn-default py-3">+</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '-'" class="btn btn-lg btn-block btn-default py-3">-</button>
							</div>
							<div class="col-5 pr-1">
								<button @click="texto += ' '" class="btn btn-lg btn-block btn-default py-3">ESPACIO</button>
							</div>
							<div class="col pr-1">
								<button @click="aceptar" class="btn btn-lg btn-block btn-primary py-3">Aceptar</button>
							</div>
							<div class="col pr-1">
								<button @click="hideMdlTeclado({})" class="btn btn-lg btn-block btn-secondary py-3">Cerrar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--  -->
		<!-- Teclado Numérico -->
		<div id="mdlTecladoNum" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow: hidden;">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header bg-dark text-white py-2">
						<h6 class="modal-title">Teclado</h6>
					</div>
					<div class="modal-body">
						<div class="row mb-2">
							<div class="col-12">
								<template v-if="_type == 'password'">
									<!-- Input tipo password -->
									<input v-model="texto"
										type="password" class="form-control form-control-lg" ref="l_text" readonly>
									<!--  -->
								</template>
								<template v-else>
									<!-- Input tipo texto -->
									<input v-model="texto" type="text" class="form-control form-control-lg" cols="30" rows="3" :readonly="_q_edit != 1" autofocus ref="l_text"></input>
									<!--  -->
								</template>
							</div>
						</div>
						<div class="row no-gutters mb-1">
							<div class="col pr-1">
								<button @click="texto += '7'" class="btn btn-lg btn-block btn-default py-3">7</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '8'" class="btn btn-lg btn-block btn-default py-3">8</button>
							</div>
							<div class="col">
								<button @click="texto += '9'" class="btn btn-lg btn-block btn-default py-3">9</button>
							</div>
						</div>
						<div class="row no-gutters mb-1">
							<div class="col pr-1">
								<button @click="texto += '4'" class="btn btn-lg btn-block btn-default py-3">4</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '5'" class="btn btn-lg btn-block btn-default py-3">5</button>
							</div>
							<div class="col">
								<button @click="texto += '6'" class="btn btn-lg btn-block btn-default py-3">6</button>
							</div>
						</div>
						<div class="row no-gutters mb-1">
							<div class="col pr-1">
								<button @click="texto += '1'" class="btn btn-lg btn-block btn-default py-3">1</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '2'" class="btn btn-lg btn-block btn-default py-3">2</button>
							</div>
							<div class="col">
								<button @click="texto += '3'" class="btn btn-lg btn-block btn-default py-3">3</button>
							</div>
						</div>
						<div class="row no-gutters mb-1">
							<div class="col pr-1">
								<button @click="texto += '.'" class="btn btn-lg btn-block btn-default py-3">.</button>
							</div>
							<div class="col pr-1">
								<button @click="texto += '0'" class="btn btn-lg btn-block btn-default py-3">0</button>
							</div>
							<div class="col">
								<button @click="aceptar" class="btn btn-lg btn-block btn-primary py-3" type="button">Aceptar</button>
							</div>
						</div>
						<div class="row no-gutters mb-1">
							<div class="col pr-1">
								<button @click="texto = ''" class="btn btn-lg btn-block text-danger py-3">Borrar</button>
							</div>
							<div class="col pr-1">
								<button @click="hideMdlTeclado({})" class="btn btn-lg btn-block btn-secondary py-3">Cerrar</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--  -->
	</div>
</script>