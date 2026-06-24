<script lang="ts">
	import { enhance } from '$app/forms';
	import type { PageData, ActionData } from './$types';

	export let data: PageData;
	export let form: ActionData;

	let searchQuery = '';
	let searchType = 'nama';
</script>

<svelte:head>
	<title>Dashboard - Hospital RM Search</title>
</svelte:head>

<div class="dashboard">
	<header>
		<div class="header-content">
			<h1>🏥 Sistem Pencarian Rekam Medis</h1>
			<div class="user-info">
				<span>👤 {data.username}</span>
				<form method="POST" action="?/logout" use:enhance>
					<button type="submit" class="btn-logout">Logout</button>
				</form>
			</div>
		</div>
	</header>

	<main>
		<div class="search-section">
			<h2>Pencarian Pasien</h2>
			<form method="GET" class="search-form">
				<div class="search-row">
					<select name="type" bind:value={searchType} class="search-type">
						<option value="nama">Nama</option>
						<option value="no_rm">No. RM</option>
						<option value="alamat">Alamat</option>
						<option value="no_telepon">No. Telepon</option>
						<option value="tanggal_lahir">Tanggal Lahir</option>
						<option value="nomor_identitas">No. Identitas</option>
						<option value="usia">Usia</option>
					</select>
					<input
						type="text"
						name="q"
						bind:value={searchQuery}
						placeholder="Masukkan kata kunci pencarian..."
						class="search-input"
					/>
					<button type="submit" class="btn-search">🔍 Cari</button>
				</div>
			</form>

			<div class="action-buttons">
				<a href="/patients/new" class="btn-primary">➕ Tambah Pasien Baru</a>
			</div>
		</div>

		{#if form?.success}
			<div class="success-message">
				{form.success}
			</div>
		{/if}

		{#if data.patients && data.patients.length > 0}
			<div class="results-section">
				<h3>Hasil Pencarian ({data.patients.length} pasien)</h3>
				<div class="table-container">
					<table>
						<thead>
							<tr>
								<th>No. RM</th>
								<th>Nama</th>
								<th>Alamat</th>
								<th>No. Telepon</th>
								<th>Tanggal Lahir</th>
								<th>No. Identitas</th>
								<th>Usia</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							{#each data.patients as patient}
								<tr>
									<td class="no-rm">{patient.no_rm}</td>
									<td>{patient.nama}</td>
									<td>{patient.alamat}</td>
									<td>{patient.no_telepon}</td>
									<td>{patient.tanggal_lahir || '-'}</td>
									<td>{patient.nomor_identitas || '-'}</td>
									<td>{patient.usia || '-'}</td>
									<td class="action-cell">
										<a href="/patients/{patient.id}/edit" class="btn-edit">Edit</a>
										<form method="POST" action="?/delete" use:enhance>
											<input type="hidden" name="id" value={patient.id} />
											<button
												type="submit"
												class="btn-delete"
												on:click={(e) => {
													if (!confirm(`Yakin ingin menghapus data pasien ${patient.nama}?`)) {
														e.preventDefault();
													}
												}}
											>
												Hapus
											</button>
										</form>
									</td>
								</tr>
							{/each}
						</tbody>
					</table>
				</div>
			</div>
		{:else if data.searched}
			<div class="no-results">
				<p>Tidak ada data pasien yang ditemukan.</p>
			</div>
		{:else}
			<div class="welcome-message">
				<p>Gunakan form pencarian di atas untuk mencari data pasien.</p>
				<p>Atau klik tombol "Tambah Pasien Baru" untuk menambahkan data pasien.</p>
			</div>
		{/if}
	</main>
</div>

<style>
	:global(body) {
		margin: 0;
		padding: 0;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
		background: #f5f5f5;
	}

	.dashboard {
		min-height: 100vh;
	}

	header {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		padding: 20px;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
	}

	.header-content {
		max-width: 1200px;
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

	.user-info {
		display: flex;
		align-items: center;
		gap: 15px;
	}

	.btn-logout {
		background: rgba(255, 255, 255, 0.2);
		color: white;
		border: 1px solid rgba(255, 255, 255, 0.3);
		padding: 8px 16px;
		border-radius: 6px;
		cursor: pointer;
		font-size: 14px;
		transition: background 0.3s;
	}

	.btn-logout:hover {
		background: rgba(255, 255, 255, 0.3);
	}

	main {
		max-width: 1200px;
		margin: 0 auto;
		padding: 30px 20px;
	}

	.search-section {
		background: white;
		padding: 25px;
		border-radius: 12px;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
		margin-bottom: 25px;
	}

	h2 {
		margin: 0 0 20px 0;
		color: #333;
		font-size: 20px;
	}

	.search-form {
		margin-bottom: 20px;
	}

	.search-row {
		display: flex;
		gap: 10px;
		flex-wrap: wrap;
	}

	.search-type {
		padding: 12px;
		border: 1px solid #ddd;
		border-radius: 6px;
		font-size: 14px;
		min-width: 150px;
	}

	.search-input {
		flex: 1;
		min-width: 250px;
		padding: 12px;
		border: 1px solid #ddd;
		border-radius: 6px;
		font-size: 14px;
	}

	.btn-search {
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

	.btn-search:hover {
		transform: translateY(-2px);
	}

	.action-buttons {
		display: flex;
		gap: 10px;
	}

	.btn-primary {
		display: inline-block;
		padding: 12px 24px;
		background: #28a745;
		color: white;
		text-decoration: none;
		border-radius: 6px;
		font-size: 14px;
		font-weight: 600;
		transition: transform 0.2s;
	}

	.btn-primary:hover {
		transform: translateY(-2px);
	}

	.success-message {
		background: #d4edda;
		color: #155724;
		padding: 15px;
		border-radius: 6px;
		margin-bottom: 20px;
		border: 1px solid #c3e6cb;
	}

	.results-section {
		background: white;
		padding: 25px;
		border-radius: 12px;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
	}

	h3 {
		margin: 0 0 20px 0;
		color: #333;
		font-size: 18px;
	}

	.table-container {
		overflow-x: auto;
	}

	table {
		width: 100%;
		border-collapse: collapse;
		font-size: 14px;
	}

	thead {
		background: #f8f9fa;
	}

	th {
		padding: 12px;
		text-align: left;
		font-weight: 600;
		color: #333;
		border-bottom: 2px solid #dee2e6;
	}

	td {
		padding: 12px;
		border-bottom: 1px solid #dee2e6;
	}

	.no-rm {
		font-weight: 600;
		color: #667eea;
	}

	.action-cell {
		display: flex;
		gap: 8px;
		align-items: center;
	}

	.btn-edit {
		padding: 6px 12px;
		background: #007bff;
		color: white;
		text-decoration: none;
		border-radius: 4px;
		font-size: 13px;
		transition: background 0.3s;
	}

	.btn-edit:hover {
		background: #0056b3;
	}

	.btn-delete {
		padding: 6px 12px;
		background: #dc3545;
		color: white;
		border: none;
		border-radius: 4px;
		font-size: 13px;
		cursor: pointer;
		transition: background 0.3s;
	}

	.btn-delete:hover {
		background: #c82333;
	}

	.no-results,
	.welcome-message {
		background: white;
		padding: 40px;
		border-radius: 12px;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
		text-align: center;
		color: #666;
	}

	@media (max-width: 768px) {
		.header-content {
			flex-direction: column;
			align-items: flex-start;
		}

		h1 {
			font-size: 20px;
		}

		.search-row {
			flex-direction: column;
		}

		.search-type,
		.search-input {
			width: 100%;
		}

		table {
			font-size: 12px;
		}

		th,
		td {
			padding: 8px;
		}

		.action-cell {
			flex-direction: column;
			gap: 5px;
		}
	}
</style>
