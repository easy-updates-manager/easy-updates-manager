import axios from 'axios';

let options = {
	'method': 'GET',
	'url': mpsum.rest_url + 'get/',
	'headers': {
		'X-WP-Nonce': mpsum.rest_nonce
	},
	'json': true
};
let result = axios( options ).then( function ( response ) {
	console.log( response );
} );



console.log( mpsum.rest_nonce );
console.log( mpsum.rest_url );
