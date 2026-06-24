import { fail, redirect } from '@sveltejs/kit';
import type { Actions } from './$types';
import { verifyUser } from '$lib/auth';
import { createSession } from '$lib/session';

export const actions = {
	default: async ({ request, cookies }) => {
		const data = await request.formData();
		const username = data.get('username')?.toString();
		const password = data.get('password')?.toString();

		if (!username || !password) {
			return fail(400, { error: 'Username dan password harus diisi' });
		}

		const user = await verifyUser(username, password);

		if (!user) {
			return fail(401, { error: 'Username atau password salah' });
		}

		createSession(cookies, user.id, user.username);
		throw redirect(303, '/dashboard');
	}
} satisfies Actions;
