								<div class="sticky-top" style="margin-bottom: .5rem">
									<div class="card text-white bg-dark">
										<div class="card-body" style="padding: .5rem">
											<div class="row justify-content-between align-items-center position-relative">												
												<!-- Tab nav Lineas-Productos-Combos -->
												<div class="col-auto">
													<ul class="nav nav-pills" id="pills-tabLineProd" role="tablist">
														<!-- Tab listado lineas -->
														<li class="nav-item">
															<a @click="mostLineas()"
																class="nav-link active bg-info text-light" id="pills-lineas-tab" data-toggle="pill" href="#pills-lineas" role="tab" aria-controls="pills-lineas" aria-selected="true"><span class="icon-home align-middle mr-2"></span>LÍneas</a>
														</li>
														<!--  -->
														<!-- Tab listado de productos para linea seleccionada -->
														<li class="nav-item">
															<a class="nav-link font-weight-bold text-light pl-2 pr-0" id="pills-prods-tab" data-toggle="pill" href="#pills-prods" role="tab" aria-controls="pills-prods" aria-selected="false"  style="background: transparent !important;">
																	<small v-text="filterProductos.l_line"></small>
															</a>
														</li>
														<!--  -->
														<!-- Tab listado combos -->
														<li class="nav-item">
															<a class="nav-link font-weight-bold text-light pl-0" id="pills-combos-tab" data-toggle="pill" href="#pills-combos" role="tab" aria-controls="pills-combos" aria-selected="false"  style="background: transparent !important;">
																<small v-text="l_comb"></small>
															</a>
														</li>
														<!--  -->
														<!-- Tab busqueda productos -->
														<li class="nav-item">
															<a class="nav-link font-weight-bold text-light pl-0" id="pills-busc-tab" data-toggle="pill" href="#pills-busc" role="tab" aria-controls="pills-busc" aria-selected="false"  style="background: transparent !important;"></a>
														</li>
														<!--  -->
													</ul>
												</div>
												<!--  -->

												<div class="col-auto text-right position-absolute" style="right: 0">
													<div class="input-group">
														<input v-model="txtBuscProd"
																type="search" class="form-control text-uppercase" placeholder="BUSCAR" style="width: 150px">
														<div class="input-group-append">
															<button @click="showMdlTeclado('txtBuscProd',txtBuscProd)" 
																	class="btn btn-primary" type="button">
																<span class="icon-keyboard"></span>
															</button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>						
								</div>
								<div class="row">
									<!-- Contenedor Tabs Lineas-Productos-Combos -->
									<div class="col-12 tab-content" id="pills-tabLineProdContent">
										<!-- Tab lineas -->
										<div class="tab-pane show active" id="pills-lineas" role="tabpanel" aria-labelledby="pills-lineas-tab">
											<div class="row">
												<div v-show="Combos.length > 0" class="col-12 col-lg-3" style="margin-bottom: .5rem">
													<button @click="showMdlCombos()" type="button" class="btn btn-info btn-lg btn-block font-weight-bold" style="white-space: normal;height: 80px; font-size: 12px;line-height: 13px">COMBOS</button>
												</div>
												<div v-for="LineProd in LineProds" class="col-6 col-lg-3" style="margin-bottom: .5rem">
													<button  v-text="LineProd.l_line" @click="mdlProductosXSubLinea(LineProd.c_line,LineProd.l_line)" type="button" class="btn btn-dark btn-lg btn-block px-0 py-0 text-light" style="white-space: normal;height: 80px; font-size: 12px;line-height: 13px"></button>
												</div>
											</div>
										</div>
										<!--  -->
										<!-- Tab productos -->
										<div class="tab-pane" id="pills-prods" role="tabpanel" aria-labelledby="pills-prods-tab">
											<div class="row">
												<div v-for="producto in filteredProdsXSubLine" class="col-6 col-lg-3" style="margin-bottom: .5rem;">
													<button @click="elegirProducto(producto)" type="button" class="btn btn-success btn-lg btn-block px-0 py-0 text-light" style="white-space: normal;height: 80px; font-size: 12px; line-height: 13px">
														@{{ producto.l_prod }}
														<p class="card-text font-weight-bold">@{{ producto.l_abre }} - @{{ producto.k_mone == '0' ? 'S/.' : '$/.' }}
														<span class="font-weight-bold" v-if="p_pudefecto == '0' || p_pudefecto == '1'">@{{ parseFloat(producto.s_pre1).toFixed(2) }}</span>
														<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '2'">@{{ parseFloat(producto.s_pre2).toFixed(2) }}</span>
														<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '3'">@{{ parseFloat(producto.s_pre3).toFixed(2) }}</span>
														<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '4'">@{{ parseFloat(producto.s_pre4).toFixed(2) }}</span>
														<span class="font-weight-bold" v-else-if="p_pudefecto == '0' || p_pudefecto == '5'">@{{ parseFloat(producto.s_pre5).toFixed(2) }}</span>
														</p>
													</button>
												</div>
											</div>
										</div>
										<!--  -->
										<!-- Tab combos -->
										<div class="tab-pane" id="pills-combos" role="tabpanel" aria-labelledby="pills-combos-tab">
											<div class="row">
												<div v-for="combo in Combos" class="col-6 col-lg-3" style="margin-bottom: .5rem">
													<button @click="elegirProducto(combo)" type="button" class="btn btn-success btn-lg btn-block px-0 py-0 text-light" style="white-space: normal;height: 80px; font-size: 12px; line-height: 13px">
														@{{ combo.l_comb }}
														<p class="card-text font-weight-bold">S/. @{{ combo.s_impo }}</p>
													</button>
												</div>
											</div>
										</div>
										<!--  -->
										<!-- Tab busqueda productos -->
										<div class="tab-pane" id="pills-busc" role="tabpanel" aria-labelledby="pills-busc-tab">
											<div class="row">
												<div v-for="producto in filteredProds" class="col-6 col-lg-3" style="margin-bottom: .5rem">
													<button @click="elegirProducto(producto)" type="button" class="btn btn-success btn-lg btn-block px-0 py-0 text-light" style="white-space: normal;height: 80px; font-size: 12px; line-height: 13px">
														@{{ producto.l_prod }}
														<p class="card-text">@{{ producto.l_abre }} - @{{ producto.k_mone == '0' ? 'S/.' : '$/.' }}
														<span v-if="p_pudefecto == '0' || p_pudefecto == '1'">@{{ parseFloat(producto.s_pre1).toFixed(2) }}</span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '2'">@{{ parseFloat(producto.s_pre2).toFixed(2) }}</span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '3'">@{{ parseFloat(producto.s_pre3).toFixed(2) }}</span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '4'">@{{ parseFloat(producto.s_pre4).toFixed(2) }}</span>
														<span v-else-if="p_pudefecto == '0' || p_pudefecto == '5'">@{{ parseFloat(producto.s_pre5).toFixed(2) }}</span>
														</p>
													</button>
												</div>
											</div>
										</div>
										<!--  -->
									</div>
									<!--  -->
								</div>