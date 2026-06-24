<script lang="ts">
	import { enhance } from '$app/forms';
	import type { ActionData } from './$types';

	export let form: ActionData;
</script>

<svelte:head>
	<title>Login - Hospital RM Search</title>
</svelte:head>

<div class="login-container">
	<div class="login-card">
		<h1>🏥 Hospital RM Search</h1>
		<p class="subtitle">Sistem Pencarian Rekam Medis Pasien</p>

		<form method="POST" use:enhance>
			<div class="form-group">
				<label for="username">Username</label>
				<input
					type="text"
					id="username"
					name="username"
					required
					autocomplete="username"
					placeholder="Masukkan username"
				/>
			</div>

			<div class="form-group">
				<label for="password">Password</label>
				<input
					type="password"
					id="password"
					name="password"
					required
					autocomplete="current-password"
					placeholder="Masukkan password"
				/>
			</div>

			{#if form?.error}
				<div class="error-message">
					{form.error}
				</div>
			{/if}

			<button type="submit" class="btn-primary">Login</button>
		</form>
	</div>
</div>

<style>
	:global(body) {
		margin: 0;
		padding: 0;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		min-height: 100vh;
	}

	.login-container {
		display: flex;
		justify-content: center;
		align-items: center;
		min-height: 100vh;
		padding: 20px;
	}

	.login-card {
		background: white;
		padding: 40px;
		border-radius: 12px;
		box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
		width: 100%;
		max-width: 400px;
	}

	h1 {
		margin: 0 0 10px 0;
		color: #333;
		text-align: center;
		font-size: 28px;
	}

	.subtitle {
		text-align: center;
		color: #666;
		margin: 0 0 30px 0;
		font-size: 14px;
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

	input {
		width: 100%;
		padding: 12px;
		border: 1px solid #ddd;
		border-radius: 6px;
		font-size: 14px;
		box-sizing: border-box;
		transition: border-color 0.3s;
	}

	input:focus {
		outline: none;
		border-color: #667eea;
	}

	.btn-primary {
		width: 100%;
		padding: 12px;
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		border: none;
		border-radius: 6px;
		font-size: 16px;
		font-weight: 600;
		cursor: pointer;
		transition: transform 0.2s;
	}

	.btn-primary:hover {
		transform: translateY(-2px);
	}

	.btn-primary:active {
		transform: translateY(0);
	}

	.error-message {
		background: #fee;
		color: #c33;
		padding: 12px;
		border-radius: 6px;
		margin-bottom: 20px;
		font-size: 14px;
		text-align: center;
	}

	.info-box {
		margin-top: 30px;
		padding: 15px;
		background: #f8f9fa;
		border-radius: 6px;
		font-size: 13px;
		color: #666;
	}

	.info-box p {
		margin: 5px 0;
	}

	@media (max-width: 480px) {
		.login-card {
			padding: 30px 20px;
		}

		h1 {
			font-size: 24px;
		}
	}
</style>
