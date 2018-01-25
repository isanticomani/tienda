var elixir = require('laravel-elixir');

require('laravel-elixir-vue');

// elixir(function(mix) {
//     mix.webpack('app.js'); // resources/assets/js/main.js
// });

elixir(mix => {
	mix.sass('app.scss')
			.webpack('app.js');
});