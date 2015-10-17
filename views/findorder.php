<?php
/**
 * @var $view
 * @var Bixie\Cart\CartModule $cart
 */
$view->script('bixie-findorder', 'bixie/cart:app/bundle/findorder.js', ['vue', 'uikit-form-password']);

?>

<section id="bixie-findorder">

	<h1><?= __('Find your order') ?></h1>

	<div class="uk-margin">

		<form class="uk-form" name="form" v-on="submit: submitForm | valid">

			<div v-show="step == 1" class="uk-panel">

				<div class="uk-panel-badge uk-badge uk-text-large">{{ 'Step 1' | trans }}</div>

				<p><?= __('Fill in the transaction ID and the email address from your order.') ?></p>

				<p><?= __('When the order is found, you will be asked to create an account.') ?></p>

				<div class="uk-form-row">
					<div class="uk-form-controls">
						<input type="text" id="form-transaction_id" placeholder="{{ 'Transaction ID' | trans}}"
							   class="uk-form-large uk-form-width-large" name="transaction_id" v-model="transaction_id"
							   v-validate="required">
					</div>
					<p class="uk-form-help-block uk-text-danger" v-show="form.transaction_id.invalid">
						{{ 'Please enter a Transaction ID' | trans }}</p>
				</div>

				<div class="uk-form-row">
					<div class="uk-form-controls">
						<input type="email" id="form-email" placeholder="{{ 'Email address' | trans}}"
							   class="uk-form-large uk-form-width-large" name="email" v-model="email"
							   v-validate="required">
					</div>
					<p class="uk-form-help-block uk-text-danger" v-show="form.email.invalid">
						{{ 'Please enter a valid email address' | trans }}</p>
				</div>

				<div class="uk-margin">
					<button type="submit" class="uk-button uk-button-primary uk-button-large">
						<i v-show="loading" class="uk-icon-circle-o-notch uk-icon-spin uk-margin-small-right"></i>
						<i v-show="!loading" class="uk-icon-search uk-margin-small-right"></i>
						{{ 'Find order' | trans }}
					</button>
				</div>

			</div>

			<div v-show="step == 2" class="uk-panel">

				<div class="uk-panel-badge uk-badge uk-text-large">{{ 'Step 2' | trans }}</div>

				<p><?= __('Choose a username and password that will be associated with this order.') ?></p>

				<p>{{ '' | trans }}</p>
				<p><?= __('An activation link will be sent to the email address entered in the previous step.') ?></p>

				<div class="uk-form-row">
					<div class="uk-form-controls">
						<input type="text" id="form-username" placeholder="{{ 'Username' | trans}}"
							   class="uk-form-large uk-form-width-large" name="username" v-model="username"
							   v-validate="required">
					</div>
					<p class="uk-form-help-block uk-text-danger" v-show="form.username.invalid">
						{{ 'Please enter a username' | trans }}</p>
				</div>

				<div class="uk-form-row">
					<div class="uk-form-controls">
						<div class="uk-form-password">
							<input id="form-password" class="uk-form-large uk-form-width-large" type="password" placeholder="{{ 'Password' | trans}}"
								   name="password" v-model="password" v-validate="required">
							<a href="" class="uk-form-password-toggle" tabindex="-1"
							   data-uk-form-password="{ lblShow: '<?= __('Show') ?>', lblHide: '<?= __('Hide') ?>' }"><?= __('Show') ?></a>
						</div>
						<p class="uk-form-help-block uk-text-danger" v-show="form.password.invalid">
							{{ 'Password cannot be blank.' | trans }}</p>
					</div>
				</div>

				<div class="uk-margin">
					<button type="submit" class="uk-button uk-button-primary uk-button-large">
						<i v-show="loading" class="uk-icon-circle-o-notch uk-icon-spin uk-margin-small-right"></i>
						<i v-show="!loading" class="uk-icon-user uk-margin-small-right"></i>
						{{ 'Register' | trans }}
					</button>
				</div>

			</div>

			<div v-show="step == 3" class="uk-panel">

				<div class="uk-panel-badge uk-badge uk-text-large">{{ 'Step 3' | trans }}</div>

				<p><?= __('An email with activiation link has been sent to this email address.') ?></p>

				<p><?= __('Activate the account. Once you are logged in, you can access the orders in your account.') ?></p>

				<div class="uk-margin uk-flex uk-flex-center">
					<div class="uk-panel uk-panel-box uk-text-center">
						<i class="uk-icon-check uk-icon-large uk-text-success"></i>

						<h3 class="uk-margin-remove">{{ 'Email has been sent to ' | trans }}{{ email }}</h3>
					</div>
				</div>


			</div>

			<div v-if="alert" class="uk-alert uk-alert-danger">{{ alert | trans }}</div>

		</form>


	</div>
<pre>{{$data|json}}</pre>
</section>
