<script lang="ts">
	import { goto } from '$app/navigation';
	import { onMount } from 'svelte';

	onMount(() => {
		goto('/login');
	});
</script>

<div class="container">
	<p>Redirecting to login...</p>
</div>

<style>
	.container {
		display: flex;
		justify-content: center;
		align-items: center;
		min-height: 100vh;
		font-family: system-ui, -apple-system, sans-serif;
	}
</style>
