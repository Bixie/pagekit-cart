<?php $view->style('codemirror'); $view->script('bixie/cart-settings', 'bixie/cart:app/bundle/cart-settings.js', ['vue', 'editor']) ?>

<div id="cart-settings" class="uk-form">

	<div class="uk-grid pk-grid-large" data-uk-grid-margin>
		<div class="pk-width-sidebar">

			<div class="uk-panel">

				<ul class="uk-nav uk-nav-side pk-nav-large" data-uk-tab="{ connect: '#tab-content' }">
					<li><a><i class="pk-icon-large-brush uk-margin-right"></i> {{ 'Main page content' | trans }}</a></li>
					<li><a><i class="pk-icon-large-settings uk-margin-right"></i> {{ 'Download settings' | trans }}</a></li>
				</ul>

			</div>

		</div>
		<div class="uk-flex-item-1">

			<ul id="tab-content" class="uk-switcher uk-margin">
				<li>

					<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
						<div data-uk-margin>

							<h2 class="uk-margin-remove">{{ 'Main page content' | trans }}</h2>

						</div>
						<div data-uk-margin>

							<button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>

						</div>
					</div>

					<div class="uk-form-horizontal">

						<div class="uk-form-row">
							<label for="form-mainpagetitle" class="uk-form-label">{{ 'Main page title' | trans }}</label>

							<div class="uk-form-controls">
								<input id="form-mainpagetitle" class="uk-width-1-1" type="text" name="mainpage_title"
									   v-model="config.mainpage_title">
							</div>
						</div>
					</div>


					<div class="uk-form-stacked uk-margin">
						<div class="uk-form-row">
							<span class="uk-form-label">{{ 'Main page text' | trans }}</span>

							<div class="uk-form-controls">
								<v-editor id="form-intro" value="{{@ config.mainpage_text }}"
										  options="{{ {markdown : config.markdown_enabled, height: 250} }}"></v-editor>
							</div>
						</div>
					</div>

					<div class="uk-form-horizontal">

						<div class="uk-form-row">
							<label class="uk-form-label">{{ 'Image' | trans }}</label>
							<div class="uk-form-controls">
								<input-image source="{{@ config.mainpage_image }}" class="pk-image-max-height"></input-image>
							</div>
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
							<label for="form-date_format" class="uk-form-label">{{ 'Date format' | trans }}</label>

							<div class="uk-form-controls">
								<select name="date_format" id="form-date_format" class="uk-form-width-small"
										v-model="config.date_format">
									<option value="F Y">{{ 'January 2015' | trans }}</option>
									<option value="F d Y">{{ 'January 15 2015' | trans }}</option>
									<option value="d F Y">{{ '15 January 2015' | trans }}</option>
									<option value="M Y">{{ 'Jan 2015' | trans }}</option>
									<option value="m Y">{{ '1 2015' | trans }}</option>
									<option value="m-d-Y">{{ '1-15-2015' | trans }}</option>
									<option value="d-m-Y">{{ '15-1-2015' | trans }}</option>
								</select>
							</div>
						</div>

				</li>
			</ul>

		</div>
	</div>

</div>
