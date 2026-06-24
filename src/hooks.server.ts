import { getSession } from '$lib/session';
import { redirect } from '@sveltejs/kit';
import type { Handle } from '@sveltejs/kit';

export const handle: Handle = async ({ event, resolve }) => {
	const session = getSession(event.cookies);
	event.locals.user = session;

	// Protect routes that require authentication
	if (event.url.pathname.startsWith('/dashboard') || event.url.pathname.startsWith('/patients')) {
		if (!session) {
			throw redirect(303, '/login');
		}
	}

	// Redirect to dashboard if already logged in and trying to access login
	if (event.url.pathname === '/login' && session) {
		throw redirect(303, '/dashboard');
	}

	return resolve(event);
};
