
<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
	<div data-uk-margin>

		<h2 class="uk-margin-remove">{{ 'Thank you message' | trans }}</h2>

	</div>
	<div data-uk-margin>

		<button class="uk-button uk-button-primary" @click="save">{{ 'Save' | trans }}</button>

	</div>
</div>

<div class="uk-form-horizontal">

	<div class="uk-form-row">
		<label for="form-thankyoutitle" class="uk-form-label">{{ 'Thank you title' | trans }}</label>

		<div class="uk-form-controls">
			<input id="form-thankyoutitle" class="uk-width-1-1" type="text" name="thankyou_title"
				   v-model="config.thankyou.title">
		</div>
	</div>
</div>


<div class="uk-form-stacked uk-margin">
	<div class="uk-form-row">
		<span class="uk-form-label">{{ 'Thank you text' | trans }}</span>

		<div class="uk-form-controls">
			<v-editor id="form-thankyou_content" :value.sync="config.thankyou.content"
					  :options="{markdown : config.email.markdown_enabled, height: 250}"></v-editor>
		</div>
		<p class="uk-form-controls-condensed">
			<label><input type="checkbox" v-model="config.thankyou.markdown_enabled"> {{ 'Enable
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
