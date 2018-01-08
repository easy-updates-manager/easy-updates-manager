import axios from 'axios';

export const FETCH_OPTIONS = 'FETCH_OPTIONS';

export function getOptions() {
	const options = {
		'method': 'GET',
		'url': mpsum.rest_url + 'get/',
		'headers': {
			'X-WP-Nonce': mpsum.rest_nonce
		},
		'json': true
	};
	const request = axios( options );
	return {
		type: FETCH_OPTIONS,
		payload: request
	};
}
