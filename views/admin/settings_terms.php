
<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
	<div data-uk-margin>

		<h2 class="uk-margin-remove">{{ 'Terms and Conditions' | trans }}</h2>

	</div>
	<div data-uk-margin>

		<button class="uk-button uk-button-primary" @click="save">{{ 'Save' | trans }}</button>

	</div>
</div>

<div class="uk-form-horizontal">

	<div class="uk-form-row">
		<label for="form-termstitle" class="uk-form-label">{{ 'Terms and Conditions title' | trans }}</label>

		<div class="uk-form-controls">
			<input id="form-termstitle" class="uk-width-1-1" type="text" name="terms_title"
				   v-model="config.terms.title">
		</div>
	</div>
</div>


<div class="uk-form-stacked uk-margin">
	<div class="uk-form-row">
		<span class="uk-form-label">{{ 'Terms and Conditions text' | trans }}</span>

		<div class="uk-form-controls">
			<v-editor id="form-terms_content" :value.sync="config.terms.content"
					  :options="{markdown : config.email.markdown_enabled, height: 350}"></v-editor>
		</div>
		<p class="uk-form-controls-condensed">
			<label><input type="checkbox" v-model="config.terms.markdown_enabled"> {{ 'Enable
				Markdown' | trans }}</label>
		</p>
	</div>
</div>