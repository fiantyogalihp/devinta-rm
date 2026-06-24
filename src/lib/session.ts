import { SESSION_SECRET } from '$env/static/private';
import type { Cookies } from '@sveltejs/kit';

const SESSION_COOKIE_NAME = 'session';

export function createSession(cookies: Cookies, userId: number, username: string) {
	const sessionData = JSON.stringify({ userId, username });
	const encoded = Buffer.from(sessionData).toString('base64');
	
	cookies.set(SESSION_COOKIE_NAME, encoded, {
		path: '/',
		httpOnly: true,
		sameSite: 'strict',
		secure: process.env.NODE_ENV === 'production',
		maxAge: 60 * 60 * 24 * 7 // 7 days
	});
}

export function getSession(cookies: Cookies): { userId: number; username: string } | null {
	const sessionCookie = cookies.get(SESSION_COOKIE_NAME);
	
	if (!sessionCookie) {
		return null;
	}
	
	try {
		const decoded = Buffer.from(sessionCookie, 'base64').toString('utf-8');
		return JSON.parse(decoded);
	} catch {
		return null;
	}
}

export function destroySession(cookies: Cookies) {
	cookies.delete(SESSION_COOKIE_NAME, { path: '/' });
}
