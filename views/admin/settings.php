<?php $view->style('codemirror'); $view->script('bixie/cart-settings', 'bixie/cart:app/bundle/cart-settings.js', ['bixie-framework', 'editor', 'uikit-accordion']) ?>

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
		<div class="pk-width-content">

			<ul id="tab-content" class="uk-switcher uk-margin">
				<li>

					<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
						<div data-uk-margin>

							<h2 class="uk-margin-remove">{{ 'Cart settings' | trans }}</h2>

						</div>
						<div data-uk-margin>

							<button class="uk-button uk-button-primary" @click="save">{{ 'Save' | trans }}</button>

						</div>
					</div>

					<div class="uk-form-horizontal">

						<div class="uk-form-row">
							<label for="form-config_vat" class="uk-form-label">{{ 'Default VAT' | trans }}</label>

							<div class="uk-form-controls uk-form-controls-text">
								<select id="form-config_vat" name="config_vat" class="uk-form-width-medium"
										v-model="config.vat">
									<option v-for="vatclass in config.vatclasses" value="$key">{{ vatclass.name }}</option>
								</select>
							</div>
						</div>

						<div class="uk-form-row">
							<span class="uk-form-label">{{ 'VAT rates' | trans }}</span>
							<div class="uk-form-controls uk-form-controls-text">
								<p v-for="vatclass in config.vatclasses" class="uk-form-controls-condensed">
									<span class="uk-display-inline-block" style="width: 110px">{{ vatclass.name | trans }}</span>
									<input class="uk-form-width-small uk-text-right" type="number"
										   v-model="vatclass.rate" number> %

								</p>
							</div>
						</div>

						<fields :config="$options.fields.cart" :model.sync="config" template="formrow"></fields>

					</div>


				</li>
				<li>
					<div class="uk-margin">

						<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
							<div data-uk-margin>

								<h2 class="uk-margin-remove">{{ 'Payment gateways settings' | trans }}</h2>

							</div>
							<div data-uk-margin>

								<button class="uk-button uk-button-primary" @click="save">{{ 'Save' | trans }}</button>

							</div>
						</div>

						<div class="uk-margin uk-accordion uk-form-horizontal" data-uk-accordion="{showfirst: false}">

							<?php foreach ($gateways as $gateway) :
								$shortName = $gateway->getShortName()
								?>

								<h3 class="uk-accordion-title">
									<i class="uk-icon-circle uk-margin-small-right"
									   :class="{'uk-text-success': config.gateways['<?= $shortName ?>'].active,
									'uk-text-danger': !config.gateways['<?= $shortName ?>'].active}"></i>
									<?= $gateway->getName() ?></h3>

								<div class="uk-accordion-content">
									<div class="uk-form-row">
										<span class="uk-form-label">{{ 'Active' | trans }}</span>

										<div class="uk-form-controls uk-form-controls-text">
											<label><input type="checkbox" value="active"
														  v-model="config.gateways['<?= $shortName ?>'].active"> {{ 'Payment method active' |
												trans }}</label>
										</div>
									</div>

									<div class="uk-form-row">
										<label class="uk-form-label">{{ 'Title' | trans }}</label>

										<div class="uk-form-controls uk-form-controls-text">
											<input type="text" v-model="config.gateways['<?= $shortName ?>'].title"
												class="uk-form-width-large">
										</div>
									</div>

									<div class="uk-form-row">
										<label class="uk-form-label">{{ 'Image' | trans }}</label>

										<div class="uk-form-controls uk-form-controls-text">
											<input-image-meta class="pk-image-max-height"
														 :image.sync="config.gateways['<?= $shortName ?>'].data.image"></input-image-meta>
										</div>
									</div>

									<div class="uk-form-row">
										<label class="uk-form-label">{{ 'Price' | trans }}</label>

										<div class="uk-form-controls uk-form-controls-text">
											<input type="number" v-model="config.gateways['<?= $shortName ?>'].price"
												   class="uk-form-width-small uk-text-right" step="0.01" number>
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

							<button class="uk-button uk-button-primary" @click="save">{{ 'Save' | trans }}</button>

						</div>
					</div>

					<div class="uk-form-horizontal">

						<fields :config="$options.fields.general" :model.sync="config" template="formrow"></fields>

					</div>
				</li>
			</ul>

		</div>
	</div>

</div>
