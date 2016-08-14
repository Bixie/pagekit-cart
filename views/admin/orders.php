<?php $view->script('admin-orders', 'bixie/cart:app/bundle/admin-orders.js', ['vue']) ?>

<div id="cart-orders" class="uk-form uk-form-horizontal" v-cloak>

	<div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

		<h2 class="uk-margin-remove" v-show="!selected.length">{{ '{0} %count% Orders|{1} %count% Order|]1,Inf[ %count% Orders' | transChoice count {count:count} }}</h2>
		<h2 class="uk-margin-remove" v-else>{{ '{1} %count% Order selected|]1,Inf[ %count% Orders selected' | transChoice selected.length {count:selected.length} }}</h2>

		<div class="uk-margin-left" v-show="selected.length">
			<ul class="uk-subnav pk-subnav-icon">
				<li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}"
					   data-uk-tooltip="{delay: 500}" @click="removeOrders"
					   v-confirm="'Delete order? All data will be deleted from the database.' | trans"></a>
				</li>
			</ul>
		</div>

		<div class="pk-search">
			<div class="uk-search">
				<input class="uk-search-field" type="text" v-model="config.filter.search" debounce="300">
			</div>
		</div>


	</div>

	<div class="uk-overflow-container">
		<table class="uk-table uk-table-hover uk-table-middle">
			<thead>
			<tr>
				<th class="pk-table-width-minimum"><input type="checkbox" v-check-all:selected.literal="input[name=id]" number></th>
				<th class="pk-table-min-width-200" v-order:transaction_id="config.filter.order">{{ 'Transaction ID' | trans }}</th>
				<th class="pk-table-width-100 uk-text-center">
					<input-filter :title="$trans('Status')" :value.sync="config.filter.status" :options="statusOptions"></input-filter>
				</th>
				<th class="pk-table-width-100" v-order:created="config.filter.order">{{ 'Date' | trans }}</th>
				<th class="pk-table-width-200" v-order:email="config.filter.order">{{ 'User / Email' | trans }}</th>
				<th class="pk-table-width-100 uk-text-right" v-order:total_bruto="config.filter.order">{{ 'Total' | trans }}</th>
				<th class="pk-table-min-width-100">{{ 'Items' | trans }}</th>
			</tr>
			</thead>
			<tbody>
			<tr class="check-item" v-for="order in orders" :class="{'uk-active': active(order)}">
				<td><input type="checkbox" name="id" value="{{ order.id }}"></td>
				<td>
					<a :href="$url.route('admin/cart/order/edit', { id: order.id })">{{ order.transaction_id }}</a>
				</td>
				<td class="uk-text-center">
					<a title="{{ getStatusText(order) }}" :class="{
                                'pk-icon-circle-danger': order.status == 0,
                                'pk-icon-circle-success': order.status == 1
                           }" @click="toggleStatus(order)"></a>
				</td>
				<td>
					{{ order.created | date 'mediumDate' }}<br/>
					<small>{{ order.created | date 'mediumTime' }}</small>
				</td>
				<td class="pk-table-text-truncate">
					{{ getName(order) }}<br/>
					{{ order.email }}
				</td>
				<td class="uk-text-right">
					{{{ order.total_bruto | formatprice order.currency }}}
				</td>
				<td class="pk-table-text-break">
					{{ cartItems(order) }}
				</td>
			</tr>
			</tbody>
		</table>
	</div>


	<h3 class="uk-h1 uk-text-muted uk-text-center"
		v-show="orders && !orders.length">{{ 'No orders found.' | trans }}</h3>

    <v-pagination :page.sync="config.page" :pages="pages" v-show="pages > 1"></v-pagination>

</div>
