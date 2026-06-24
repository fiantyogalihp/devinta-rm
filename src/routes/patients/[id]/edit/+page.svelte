<script lang="ts">
	import { enhance } from '$app/forms';
	import type { PageData, ActionData } from './$types';

	export let data: PageData;
	export let form: ActionData;
</script>

<svelte:head>
	<title>Edit Pasien - Hospital RM Search</title>
</svelte:head>

<div class="page-container">
	<header>
		<div class="header-content">
			<h1>🏥 Edit Data Pasien</h1>
			<a href="/dashboard" class="btn-back">← Kembali ke Dashboard</a>
		</div>
	</header>

	<main>
		<div class="form-container">
			<form method="POST" use:enhance>
				<div class="form-row">
					<div class="form-group">
						<label for="no_rm">Nomor Rekam Medis <span class="required">*</span></label>
						<input
							type="text"
							id="no_rm"
							name="no_rm"
							required
							placeholder="Contoh: RM-2024-001"
							value={form?.data?.no_rm || data.patient.no_rm}
						/>
						{#if form?.errors?.no_rm}
							<span class="error">{form.errors.no_rm}</span>
						{/if}
					</div>

					<div class="form-group">
						<label for="nama">Nama Lengkap <span class="required">*</span></label>
						<input
							type="text"
							id="nama"
							name="nama"
							required
							placeholder="Nama lengkap pasien"
							value={form?.data?.nama || data.patient.nama}
						/>
						{#if form?.errors?.nama}
							<span class="error">{form.errors.nama}</span>
						{/if}
					</div>
				</div>

				<div class="form-group">
					<label for="alamat">Alamat <span class="required">*</span></label>
					<textarea
						id="alamat"
						name="alamat"
						required
						rows="3"
						placeholder="Alamat lengkap pasien"
						value={form?.data?.alamat || data.patient.alamat}
					></textarea>
					{#if form?.errors?.alamat}
						<span class="error">{form.errors.alamat}</span>
					{/if}
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="no_telepon">Nomor Telepon <span class="required">*</span></label>
						<input
							type="tel"
							id="no_telepon"
							name="no_telepon"
							required
							placeholder="Contoh: 081234567890"
							value={form?.data?.no_telepon || data.patient.no_telepon}
						/>
						{#if form?.errors?.no_telepon}
							<span class="error">{form.errors.no_telepon}</span>
						{/if}
					</div>

					<div class="form-group">
						<label for="tanggal_lahir">Tanggal Lahir</label>
						<input
							type="date"
							id="tanggal_lahir"
							name="tanggal_lahir"
							value={form?.data?.tanggal_lahir || data.patient.tanggal_lahir || ''}
						/>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group">
						<label for="nomor_identitas">Nomor Identitas (KTP/SIM/Paspor)</label>
						<input
							type="text"
							id="nomor_identitas"
							name="nomor_identitas"
							placeholder="Nomor identitas (opsional)"
							value={form?.data?.nomor_identitas || data.patient.nomor_identitas || ''}
						/>
					</div>

					<div class="form-group">
						<label for="usia">Usia (tahun)</label>
						<input
							type="number"
							id="usia"
							name="usia"
							min="0"
							max="150"
							placeholder="Usia pasien (opsional)"
							value={form?.data?.usia || data.patient.usia || ''}
						/>
					</div>
				</div>

				{#if form?.error}
					<div class="error-message">
						{form.error}
					</div>
				{/if}

				<div class="form-actions">
					<button type="submit" class="btn-primary">💾 Update Data Pasien</button>
					<a href="/dashboard" class="btn-secondary">Batal</a>
				</div>
			</form>
		</div>
	</main>
</div>

<style>
	:global(body) {
		margin: 0;
		padding: 0;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
		background: #f5f5f5;
	}

	.page-container {
		min-height: 100vh;
	}

	header {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		padding: 20px;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
	}

	.header-content {
		max-width: 900px;
		margin: 0 auto;
		display: flex;
		justify-content: space-between;
		align-items: center;
		flex-wrap: wrap;
		gap: 15px;
	}

	h1 {
		margin: 0;
		font-size: 24px;
	}

	.btn-back {
		background: rgba(255, 255, 255, 0.2);
		color: white;
		text-decoration: none;
		padding: 8px 16px;
		border-radius: 6px;
		font-size: 14px;
		transition: background 0.3s;
	}

	.btn-back:hover {
		background: rgba(255, 255, 255, 0.3);
	}

	main {
		max-width: 900px;
		margin: 0 auto;
		padding: 30px 20px;
	}

	.form-container {
		background: white;
		padding: 30px;
		border-radius: 12px;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
	}

	.form-row {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 20px;
		margin-bottom: 20px;
	}

	.form-group {
		margin-bottom: 20px;
	}

	label {
		display: block;
		margin-bottom: 8px;
		color: #333;
		font-weight: 500;
		font-size: 14px;
	}

	.required {
		color: #dc3545;
	}

	input,
	textarea {
		width: 100%;
		padding: 12px;
		border: 1px solid #ddd;
		border-radius: 6px;
		font-size: 14px;
		box-sizing: border-box;
		font-family: inherit;
		transition: border-color 0.3s;
	}

	input:focus,
	textarea:focus {
		outline: none;
		border-color: #667eea;
	}

	.error {
		display: block;
		color: #dc3545;
		font-size: 13px;
		margin-top: 5px;
	}

	.error-message {
		background: #fee;
		color: #c33;
		padding: 12px;
		border-radius: 6px;
		margin-bottom: 20px;
		font-size: 14px;
	}

	.form-actions {
		display: flex;
		gap: 15px;
		margin-top: 30px;
	}

	.btn-primary {
		padding: 12px 24px;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		border: none;
		border-radius: 6px;
		font-size: 14px;
		font-weight: 600;
		cursor: pointer;
		transition: transform 0.2s;
	}

	.btn-primary:hover {
		transform: translateY(-2px);
	}

	.btn-secondary {
		padding: 12px 24px;
		background: #6c757d;
		color: white;
		text-decoration: none;
		border-radius: 6px;
		font-size: 14px;
		font-weight: 600;
		transition: transform 0.2s;
		display: inline-block;
	}

	.btn-secondary:hover {
		transform: translateY(-2px);
	}

	@media (max-width: 768px) {
		.header-content {
			flex-direction: column;
			align-items: flex-start;
		}

		h1 {
			font-size: 20px;
		}

		.form-container {
			padding: 20px;
		}

		.form-row {
			grid-template-columns: 1fr;
		}

		.form-actions {
			flex-direction: column;
		}

		.btn-primary,
		.btn-secondary {
			width: 100%;
			text-align: center;
		}
	}
</style>
