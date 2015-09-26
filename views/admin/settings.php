<?php $view->style('codemirror'); $view->script('bixie/cart-settings', 'bixie/cart:app/bundle/cart-settings.js', ['vue', 'editor', 'uikit-accordion']) ?>

<div id="cart-settings" class="uk-form">

	<div class="uk-grid pk-grid-large" data-uk-grid-margin>
		<div class="pk-width-sidebar">

			<div class="uk-panel">

				<ul class="uk-nav uk-nav-side pk-nav-large" data-uk-tab="{ connect: '#tab-content' }">
					<li><a><i class="pk-icon-large-settings uk-margin-right"></i> {{ 'Cart settings' | trans }}</a></li>
					<li><a><i class="pk-icon-large-code uk-margin-right"></i> {{ 'Thank you content' | trans }}</a></li>
					<li><a><i class="pk-icon-large-settings uk-margin-right"></i> {{ 'Payment settings' | trans }}</a></li>
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

				</li>
				<li>
					<?= $view->render('bixie/cart/admin/settings_thankyou.php') ?>
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
							<label for="form-ordering" class="uk-form-label">{{ 'Ordering' | trans }}</label>

							<div class="uk-form-controls">
								<select name="ordering" id="form-ordering" class="uk-form-width-small"
										v-model="config.ordering">
									<option value="title">{{ 'Title' | trans }}</option>
									<option value="date">{{ 'Date' | trans }}</option>
								</select>
							</div>
						</div>

						<div class="uk-form-row">
							<label for="form-ordering_dir" class="uk-form-label">{{ 'Ordering direction' | trans }}</label>

							<div class="uk-form-controls">
								<select name="ordering_dir" id="form-ordering_dir" class="uk-form-width-small"
										v-model="config.ordering_dir">
									<option value="asc">{{ 'Ascending' | trans }}</option>
									<option value="desc">{{ 'Descending' | trans }}</option>
								</select>
							</div>
						</div>

						<div class="uk-form-row">
							<span class="uk-form-label">{{ 'Markdown' | trans }}</span>

							<div class="uk-form-controls uk-form-controls-text">
								<p class="uk-form-controls-condensed">
									<label><input type="checkbox" v-model="config.markdown_enabled"> {{ 'Enable
										Markdown' | trans }}</label>
								</p>
							</div>
						</div>

				</li>
			</ul>

		</div>
	</div>

</div>
