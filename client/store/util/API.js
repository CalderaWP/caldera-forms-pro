import axios from 'axios';
import CFProConfig from './wpConfig';

export const localAPI = axios.create({
	baseURL: CFProConfig.localApiURL,
	timeout: 3000,
	headers: {'X-WP-Nonce': CFProConfig.localApiNonce}
});

export const appAPI = axios.create({
	baseURL: CFProConfig.appURL,
	timeout: 3000,
});