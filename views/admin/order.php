<?php  $view->style('codemirror'); $view->script('admin-order', 'bixie/cart:app/bundle/admin-order.js', ['vue', 'editor']); ?>

<form id="order-edit" class="uk-form" name="form" v-on="submit: save | valid" v-cloak>

	<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
		<div data-uk-margin>

			<h2 class="uk-margin-remove" v-if="order.id">{{ 'Edit order' | trans }} <em>{{
					order.title | trans }}</em></h2>


		</div>
		<div data-uk-margin>

			<a class="uk-button uk-margin-small-right" v-attr="href: $url.route('admin/cart/orders')">{{ order.id ?
				'Close' :
				'Cancel' | trans }}</a>
			<button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

		</div>
	</div>

	<ul class="uk-tab" v-el="tab">
		<li><a>{{ 'General' | trans }}</a></li>
	</ul>

	<div class="uk-switcher uk-margin" v-el="content">
		<div>
			<div class="uk-margin">
				<div class="uk-grid pk-grid-large" data-uk-grid-margin>
					<div class="uk-flex-item-1">
						<div class="uk-form-horizontal uk-margin">
							<div class="uk-form-row">
								<label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>

								<div class="uk-form-controls">
									<input id="form-title" class="uk-width-1-1 uk-form-large" type="text" name="title"
										   v-model="order.title" v-valid="required">
								</div>
								<p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">
									{{ 'Please enter a title' | trans }}</p>
							</div>

							<div class="uk-form-row">
								<label for="form-subtitle" class="uk-form-label">{{ 'Subitle' | trans }}</label>

								<div class="uk-form-controls">
									<input id="form-subtitle" class="uk-width-1-1" type="text" name="subtitle"
										   v-model="order.subtitle">
								</div>
							</div>

						</div>


						<div class="uk-form-stacked uk-margin">
							<div class="uk-form-row">
								<span class="uk-form-label">{{ 'Content' | trans }}</span>

								<div class="uk-form-controls">
									<v-editor id="form-content" value="{{@ order.content }}"
											  options="{{ {markdown : order.data.markdown} }}"></v-editor>
								</div>
							</div>
						</div>

					</div>
					<div class="pk-width-sidebar pk-width-sidebar-large uk-form-stacked">

						<div class="uk-form-row">
							<label class="uk-form-label">{{ 'Order' | trans }}</label>

							<div class="uk-form-controls">
								<input-order order="{{@ order.path }}" ext="{{ ['zip', 'rar', 'tar.gz'] }}"></input-order>
								<input type="hidden" name="path" v-model="order.path" v-valid="required">
							</div>
							<p class="uk-form-help-block uk-text-danger" v-show="form.path.invalid">
								{{ 'Please select a order' | trans }}</p>
						</div>

						<div class="uk-form-row">
							<label class="uk-form-label">{{ 'Image' | trans }}</label>
							<div class="uk-form-controls">
								<input-image-meta image="{{@ order.image.main }}" class="pk-image-max-height"></input-image-meta>
							</div>
						</div>

						<div class="uk-form-row">
							<label class="uk-form-label">{{ 'Icon' | trans }}</label>
							<div class="uk-form-controls">
								<input-image-meta image="{{@ order.image.icon }}" class="pk-image-max-height"></input-image-meta>
							</div>
						</div>

						<div class="uk-form-row">
							<label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>

							<div class="uk-form-controls">
								<input id="form-slug" class="uk-width-1-1" type="text" v-model="order.slug">
							</div>
						</div>

						<div class="uk-form-row">
							<label for="form-status" class="uk-form-label">{{ 'Status' | trans }}</label>
							<div class="uk-form-controls">
								<select id="form-status" class="uk-width-1-1" v-model="order.status" options="statusOptions"></select>
							</div>
						</div>

						<div class="uk-form-row">
							<span class="uk-form-label">{{ 'Date' | trans }}</span>
							<div class="uk-form-controls">
								<input-date datetime="{{@ order.date}}"></input-date>
							</div>
						</div>

						<div class="uk-form-row">
							<span class="uk-form-label">{{ 'Tags' | trans }}</span>
							<div class="uk-form-controls">
								<input-tags tags="{{@ order.tags}}" existing="{{ tags }}"></input-tags>
							</div>
						</div>

						<div class="uk-form-row">
							<span class="uk-form-label">{{ 'Restrict Access' | trans }}</span>
							<div class="uk-form-controls uk-form-controls-text">
								<p v-repeat="role: roles" class="uk-form-controls-condensed">
									<label><input type="checkbox" value="{{ role.id }}" v-checkbox="order.roles" number> {{ role.name }}</label>
								</p>
							</div>
						</div>

						<div class="uk-form-row">
							<span class="uk-form-label">{{ 'Options' | trans }}</span>
							<div class="uk-form-controls uk-form-controls-text">
								<label>
									<input type="checkbox" value="markdown" v-model="order.data.markdown"> {{ 'Enable Markdown' |
									trans }}</label>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

</form>

