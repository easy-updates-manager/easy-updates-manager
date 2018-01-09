import axios from 'axios';

export const SAVE_OPTIONS = 'SAVE_OPTIONS';

export function saveOptions( id, value ) {
	const options = {
		'method': 'POST',
		'url': mpsum.rest_url + 'save/' + id + '/' + value,
		'headers': {
			'X-WP-Nonce': mpsum.rest_nonce,
			'id': id,
			'value': value
		},
		'json': true
	};
	const request = axios( options );
	return {
		type: SAVE_OPTIONS,
		payload: request
	};
}
