import { createClient } from '@libsql/client';
import { TURSO_DATABASE_URL, TURSO_AUTH_TOKEN } from '$env/static/private';

export const db = createClient({
	url: TURSO_DATABASE_URL,
	authToken: TURSO_AUTH_TOKEN
});

export interface Patient {
	id: number;
	no_rm: string;
	nama: string;
	alamat: string;
	no_telepon: string;
	tanggal_lahir: string | null;
	nomor_identitas: string | null;
	usia: number | null;
	created_at: string;
	updated_at: string;
}

export interface User {
	id: number;
	username: string;
	password_hash: string;
	created_at: string;
}
