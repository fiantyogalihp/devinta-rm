import { redirect, fail } from '@sveltejs/kit';
import type { PageServerLoad, Actions } from './$types';
import { db, type Patient } from '$lib/db';
import { destroySession } from '$lib/session';

export const load: PageServerLoad = async ({ locals, url }) => {
	if (!locals.user) {
		throw redirect(303, '/login');
	}

	const searchQuery = url.searchParams.get('q');
	const searchType = url.searchParams.get('type') || 'nama';

	let patients: Patient[] = [];
	let searched = false;

	if (searchQuery) {
		searched = true;
		let sql = '';
		let args: any[] = [];

		switch (searchType) {
			case 'no_rm':
				sql = 'SELECT * FROM patients WHERE no_rm LIKE ? ORDER BY created_at DESC';
				args = [`%${searchQuery}%`];
				break;
			case 'alamat':
				sql = 'SELECT * FROM patients WHERE alamat LIKE ? ORDER BY created_at DESC';
				args = [`%${searchQuery}%`];
				break;
			case 'no_telepon':
				sql = 'SELECT * FROM patients WHERE no_telepon LIKE ? ORDER BY created_at DESC';
				args = [`%${searchQuery}%`];
				break;
			case 'tanggal_lahir':
				sql = 'SELECT * FROM patients WHERE tanggal_lahir LIKE ? ORDER BY created_at DESC';
				args = [`%${searchQuery}%`];
				break;
			case 'nomor_identitas':
				sql = 'SELECT * FROM patients WHERE nomor_identitas LIKE ? ORDER BY created_at DESC';
				args = [`%${searchQuery}%`];
				break;
			case 'usia':
				sql = 'SELECT * FROM patients WHERE usia = ? ORDER BY created_at DESC';
				args = [parseInt(searchQuery) || 0];
				break;
			default: // nama
				sql = 'SELECT * FROM patients WHERE nama LIKE ? ORDER BY created_at DESC';
				args = [`%${searchQuery}%`];
		}

		try {
			const result = await db.execute({ sql, args });
			patients = result.rows as unknown as Patient[];
		} catch (error) {
			console.error('Error searching patients:', error);
		}
	}

	return {
		username: locals.user.username,
		patients,
		searched
	};
};

export const actions = {
	logout: async ({ cookies }) => {
		destroySession(cookies);
		throw redirect(303, '/login');
	},

	delete: async ({ request }) => {
		const data = await request.formData();
		const id = data.get('id')?.toString();

		if (!id) {
			return fail(400, { error: 'ID pasien tidak valid' });
		}

		try {
			await db.execute({
				sql: 'DELETE FROM patients WHERE id = ?',
				args: [parseInt(id)]
			});

			return { success: 'Data pasien berhasil dihapus' };
		} catch (error) {
			console.error('Error deleting patient:', error);
			return fail(500, { error: 'Gagal menghapus data pasien' });
		}
	}
} satisfies Actions;
