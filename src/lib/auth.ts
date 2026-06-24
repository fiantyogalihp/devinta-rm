import bcrypt from 'bcryptjs';
import { db, type User } from '$lib/db';

export async function verifyUser(username: string, password: string): Promise<User | null> {
	try {
		const result = await db.execute({
			sql: 'SELECT * FROM users WHERE username = ?',
			args: [username]
		});

		if (result.rows.length === 0) {
			return null;
		}

		const user = result.rows[0] as unknown as User;
		const isValid = await bcrypt.compare(password, user.password_hash);

		if (!isValid) {
			return null;
		}

		return user;
	} catch (error) {
		console.error('Error verifying user:', error);
		return null;
	}
}

export async function createUser(username: string, password: string): Promise<boolean> {
	try {
		const passwordHash = await bcrypt.hash(password, 10);
		await db.execute({
			sql: 'INSERT INTO users (username, password_hash) VALUES (?, ?)',
			args: [username, passwordHash]
		});
		return true;
	} catch (error) {
		console.error('Error creating user:', error);
		return false;
	}
}
