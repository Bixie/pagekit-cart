
<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
	<div data-uk-margin>

		<h2 class="uk-margin-remove">{{ 'Email confirmation' | trans }}</h2>

	</div>
	<div data-uk-margin>

		<button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>

	</div>
</div>

<div class="uk-form-horizontal">

	<div class="uk-form-row">
		<label for="form-email_admin" class="uk-form-label">{{ 'Email address admin' | trans }}</label>

		<div class="uk-form-controls">
			<input id="form-email_admin" class="uk-width-1-1" type="email" name="email_admin_email"
				   v-model="config.email.admin_email">
		</div>
	</div>

	<div class="uk-form-row">
		<label for="form-email_subject" class="uk-form-label">{{ 'Email confirmation subject' | trans }}</label>

		<div class="uk-form-controls">
			<input id="form-email_subject" class="uk-width-1-1" type="text" name="email_subject"
				   v-model="config.email.subject">
		</div>
	</div>

</div>


<div class="uk-form-stacked uk-margin">
	<div class="uk-form-row">
		<span class="uk-form-label">{{ 'Email confirmation text' | trans }}</span>

		<div class="uk-form-controls">
			<v-editor id="form-email_body" value="{{@ config.email.body }}"
					  options="{{ {markdown : config.email.markdown_enabled, height: 250} }}"></v-editor>
		</div>
		<p class="uk-form-controls-condensed">
			<label><input type="checkbox" v-model="config.email.markdown_enabled"> {{ 'Enable
				Markdown' | trans }}</label>
		</p>
	</div>
</div>

<p>{{ 'You can use the following placeholders to display data from the order:' | trans }}</p>

<p>
	<kbd>$$transaction_id$$</kbd>, <kbd>$$comment$$</kbd>, <kbd>$$payment.method_name$$</kbd>,
	<kbd>$$firstName$$</kbd>, <kbd>$$lastName$$</kbd>, <kbd>$$email$$</kbd>, <kbd>$$address1$$</kbd>, <kbd>$$address2$$</kbd>,
	<kbd>$$postcode$$</kbd>, <kbd>$$state$$</kbd>, <kbd>$$country$$</kbd>, <kbd>$$phone$$</kbd>
</p>
