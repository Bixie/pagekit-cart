
<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
	<div data-uk-margin>

		<h2 class="uk-margin-remove">{{ 'Thank you content' | trans }}</h2>

	</div>
	<div data-uk-margin>

		<button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>

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
			<v-editor id="form-intro" value="{{@ config.thankyou.content }}"
					  options="{{ {markdown : config.markdown_enabled, height: 250} }}"></v-editor>
		</div>
	</div>
</div>

