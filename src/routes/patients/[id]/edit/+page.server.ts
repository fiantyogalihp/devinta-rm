import { error, fail, redirect } from '@sveltejs/kit';
import type { PageServerLoad, Actions } from './$types';
import { db, type Patient } from '$lib/db';

export const load: PageServerLoad = async ({ params }) => {
	const id = parseInt(params.id);

	if (isNaN(id)) {
		throw error(400, 'Invalid patient ID');
	}

	try {
		const result = await db.execute({
			sql: 'SELECT * FROM patients WHERE id = ?',
			args: [id]
		});

		if (result.rows.length === 0) {
			throw error(404, 'Patient not found');
		}

		const patient = result.rows[0] as unknown as Patient;

		return {
			patient
		};
	} catch (err) {
		console.error('Error loading patient:', err);
		throw error(500, 'Failed to load patient data');
	}
};

export const actions = {
	default: async ({ params, request }) => {
		const id = parseInt(params.id);
		const data = await request.formData();

		const no_rm = data.get('no_rm')?.toString();
		const nama = data.get('nama')?.toString();
		const alamat = data.get('alamat')?.toString();
		const no_telepon = data.get('no_telepon')?.toString();
		const tanggal_lahir = data.get('tanggal_lahir')?.toString() || null;
		const nomor_identitas = data.get('nomor_identitas')?.toString() || null;
		const usia = data.get('usia')?.toString();

		const errors: Record<string, string> = {};

		if (!no_rm) errors.no_rm = 'Nomor rekam medis harus diisi';
		if (!nama) errors.nama = 'Nama harus diisi';
		if (!alamat) errors.alamat = 'Alamat harus diisi';
		if (!no_telepon) errors.no_telepon = 'Nomor telepon harus diisi';

		if (Object.keys(errors).length > 0) {
			return fail(400, {
				errors,
				data: {
					no_rm,
					nama,
					alamat,
					no_telepon,
					tanggal_lahir,
					nomor_identitas,
					usia
				}
			});
		}

		try {
			await db.execute({
				sql: `UPDATE patients 
				      SET no_rm = ?, nama = ?, alamat = ?, no_telepon = ?, 
				          tanggal_lahir = ?, nomor_identitas = ?, usia = ?,
				          updated_at = CURRENT_TIMESTAMP
				      WHERE id = ?`,
				args: [
					no_rm,
					nama,
					alamat,
					no_telepon,
					tanggal_lahir,
					nomor_identitas,
					usia ? parseInt(usia) : null,
					id
				]
			});

			throw redirect(303, '/dashboard?success=Data pasien berhasil diupdate');
		} catch (error: any) {
			console.error('Error updating patient:', error);

			if (error.message?.includes('UNIQUE constraint failed')) {
				return fail(400, {
					error: 'Nomor rekam medis sudah digunakan',
					data: {
						no_rm,
						nama,
						alamat,
						no_telepon,
						tanggal_lahir,
						nomor_identitas,
						usia
					}
				});
			}

			return fail(500, {
				error: 'Gagal mengupdate data pasien',
				data: {
					no_rm,
					nama,
					alamat,
					no_telepon,
					tanggal_lahir,
					nomor_identitas,
					usia
				}
			});
		}
	}
} satisfies Actions;
