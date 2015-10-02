<?php $view->style('codemirror'); $view->script('bixie/cart-settings', 'bixie/cart:app/bundle/cart-settings.js', ['vue', 'editor', 'uikit-accordion']) ?>

<div id="cart-settings" class="uk-form">

	<div class="uk-grid pk-grid-large" data-uk-grid-margin>
		<div class="pk-width-sidebar">

			<div class="uk-panel">

				<ul class="uk-nav uk-nav-side pk-nav-large" data-uk-tab="{ connect: '#tab-content' }">
					<li><a><i class="pk-icon-large-settings uk-margin-right"></i> {{ 'Cart settings' | trans }}</a></li>
					<li><a><i class="pk-icon-large-settings uk-margin-right"></i> {{ 'Payment settings' | trans }}</a></li>
					<li><a><i class="pk-icon-large-code uk-margin-right"></i> {{ 'Thank you message' | trans }}</a></li>
					<li><a><i class="pk-icon-large-code uk-margin-right"></i> {{ 'Email confirmation' | trans }}</a></li>
					<li><a><i class="pk-icon-large-code uk-margin-right"></i> {{ 'Terms and Conditions' | trans }}</a></li>
					<li><a><i class="pk-icon-large-settings uk-margin-right"></i> {{ 'General settings' | trans }}</a></li>
				</ul>

			</div>

		</div>
		<div class="uk-flex-item-1">

			<ul id="tab-content" class="uk-switcher uk-margin">
				<li>

					<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
						<div data-uk-margin>

							<h2 class="uk-margin-remove">{{ 'Cart settings' | trans }}</h2>

						</div>
						<div data-uk-margin>

							<button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>

						</div>
					</div>

					<div class="uk-form-horizontal">

						<div class="uk-form-row">
							<label for="form-config_currency" class="uk-form-label">{{ 'Default currency' | trans }}</label>

							<div class="uk-form-controls uk-form-controls-text">
								<select id="form-config_currency" name="config_currency" class="uk-form-width-medium"
										v-model="config.currency">
									<option value="EUR">{{ 'Euro' | trans }}</option>
									<option value="USD">{{ 'Dollar' | trans }}</option>
								</select>
							</div>
						</div>

						<div class="uk-form-row">
							<label for="form-config_vat" class="uk-form-label">{{ 'Default VAT' | trans }}</label>

							<div class="uk-form-controls uk-form-controls-text">
								<select id="form-config_vat" name="config_vat" class="uk-form-width-medium"
										v-model="config.vat" options="vatOptions">
								</select>
							</div>
						</div>

						<div class="uk-form-row">
							<span class="uk-form-label">{{ 'VAT rates' | trans }}</span>
							<div class="uk-form-controls uk-form-controls-text">
								<p v-repeat="config.vatclasses" class="uk-form-controls-condensed">
									<span class="uk-display-inline-block" style="width: 110px">{{ name | trans }}</span>
									<input class="uk-form-width-small uk-text-right" type="number"
										   v-model="config.vatclasses[$key].rate" number> %

								</p>
							</div>
						</div>

						<div class="uk-form-row">
							<label for="form-vat_view" class="uk-form-label">{{ 'VAT display' | trans }}</label>

							<div class="uk-form-controls uk-form-controls-text">
								<select id="form-vat_view" name="vat_view" class="uk-form-width-medium"
										v-model="config.vat_view">
									<option value="incl">{{ 'Show prices including VAT' | trans }}</option>
									<option value="excl">{{ 'Show prices excluding VAT' | trans }}</option>
								</select>
							</div>
						</div>

						<h3>{{ 'Add to cart buttons' | trans }}</h3>

						<div class="uk-form-row">
							<span class="uk-form-label">{{ 'VAT' | trans }}</span>
							<div class="uk-form-controls uk-form-controls-text">
								<label><input type="checkbox" value="center-content" v-model="config.addtocart.show_vat"> {{ 'Show VAT amount' | trans }}</label>
							</div>
						</div>

					</div>


				</li>
				<li>
					<div class="uk-margin">

						<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
							<div data-uk-margin>

								<h2 class="uk-margin-remove">{{ 'Payment gateways settings' | trans }}</h2>

							</div>
							<div data-uk-margin>

								<button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>

							</div>
						</div>

						<div class="uk-accordion uk-form-horizontal" data-uk-accordion="{showfirst: false}">

							<?php foreach ($gateways as $gateway) :
								$shortName = $gateway->getShortName()
								?>

								<h3 class="uk-accordion-title">
									<i v-attr="class: config.gateways['<?= $shortName ?>'].active ?
									'uk-icon-circle uk-text-success uk-margin-small-right' :
									'uk-icon-circle uk-text-danger uk-margin-small-right'"></i>
									<?= $gateway->getName() ?></h3>

								<div class="uk-accordion-content">
									<div class="uk-form-row">
										<span class="uk-form-label">{{ 'Active' | trans }}</span>

										<div class="uk-form-controls uk-form-controls-text">
											<label><input type="checkbox" value="hide-title"
														  v-model="config.gateways['<?= $shortName ?>'].active"> {{ 'Payment method active' |
												trans }}</label>
										</div>
									</div>

									<?php foreach ($gateway->getParameters() as $key => $default) : ?>

										<div class="uk-form-row">
											<label for="form-gateways-<?= $shortName ?>-<?= $key ?>"
												   class="uk-form-label">{{ '<?= $key ?>' | trans }}</label>

											<?php if (is_bool($default)): ?>
												<div class="uk-form-controls uk-form-controls-text">
													<input type="checkbox" value="hide-title"
														   v-model="config.gateways['<?= $shortName ?>']['<?= $key ?>']">
												</div>
											<?php else: ?>
												<div class="uk-form-controls">
													<input class="uk-form-width-large" type="text"
														   name="form-gateways-<?= $shortName ?>-<?= $key ?>"
														   id="form-gateways-<?= $shortName ?>-<?= $key ?>"
														   v-model="config.gateways['<?= $shortName ?>']['<?= $key ?>']">
												</div>
											<?php endif; ?>
										</div>

									<?php endforeach; ?>
								</div>

							<?php endforeach; ?>

						</div>

					</div>
				</li>
				<li>
					<?= $view->render('bixie/cart/admin/settings_thankyou.php') ?>
				</li>
				<li>
					<?= $view->render('bixie/cart/admin/settings_email.php') ?>
				</li>
				<li>
					<?= $view->render('bixie/cart/admin/settings_terms.php') ?>
				</li>
				<li>

					<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
						<div data-uk-margin>

							<h2 class="uk-margin-remove">{{ 'General settings' | trans }}</h2>

						</div>
						<div data-uk-margin>

							<button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>

						</div>
					</div>

					<div class="uk-form-horizontal">
						<div class="uk-form-row">
							<label class="uk-form-label">{{ 'Orders per page' | trans }}</label>

							<div class="uk-form-controls uk-form-controls-text">
								<p class="uk-form-controls-condensed">
									<input type="number" v-model="config.orders_per_page" class="uk-form-width-small">
								</p>
							</div>
						</div>

						<div class="uk-form-row">
							<label for="form-config_vat" class="uk-form-label">{{ 'Server timezone' | trans }}</label>

							<div class="uk-form-controls uk-form-controls-text">
								<select id="form-config_server_tz" name="config_server_tz" class="uk-form-width-medium"
										v-model="config.server_tz" options="timezoneOptions">
								</select>
							</div>
						</div>

						<div class="uk-form-row">
							<label for="form-USDtoEUR" class="uk-form-label">{{ 'USD to EUR conversion rate' | trans }}</label>

							<div class="uk-form-controls">
								<input id="form-USDtoEUR" class="uk-form-width-medium uk-text-right" type="number" name="USDtoEUR"
									   v-model="config.USDtoEUR" number>
							</div>
						</div>

						<div class="uk-form-row">
							<label for="form-EURtoUSD" class="uk-form-label">{{ 'EUR to USD conversion rate' | trans }}</label>

							<div class="uk-form-controls">
								<input id="form-EURtoUSD" class="uk-form-width-medium uk-text-right" type="number" name="EURtoUSD"
									   v-model="config.EURtoUSD" number>
							</div>
						</div>


					</div>
				</li>
			</ul>

		</div>
	</div>

</div>
