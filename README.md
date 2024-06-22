### Before
1. Pastikan anda sudah menjalankan perintah ```composer config --global --auth http-basic.kahasolusi.repo.repman.io TOKEN```. Isikan TOKEN dengan token yang telah diberikan
2. Atau anda bisa juga menggunakan perinta ```COMPOSER_AUTH={"http-basic": {"kahasolusi.repo.repman.io": {"username": "token", "password": "TOKEN"}}}```

### Installation

1. Clone this repository to your local computer
2. Copy .env.example to .env
3. Fill .env with your own configuration
4. Run `php artisan key:generate` if needed
5. Run `php artisan migrate --seed` to execute migration and seeder data

### Using Docker

1. Clone this repository
2. Copy .env.example to .env
3. Fill .env with your own configuration
4. Run `docker compose up -d`
5. Wait for all service is ready and running
6. Generate key inside app using `docker compose exec app php artisan key:generate`
7. Run migration inside app service using command `docker compose exec app php artisan module:migrate-fresh --seed`

### Contribution

1. **Creating and Working on Branches:**
   - Each team member creates a new branch for their tasks.
   - Branches should be named descriptively according to the task being worked on.

2. **Committing with Conventional Commit Format:**
   - When making commits, ensure to follow the conventional commit format.
   - Use the format `<type>(<scope>): <subject>` with a clear description of the changes made.

### Submitting Pull Requests

1. **Pushing and Opening Pull Requests:**
   - After completing the work, team members push their branches to the original repository.
   - Open a pull request from the modified branch to the `development` branch.

2. **Reviewing and Approving Pull Requests:**
   - Team members or other reviewers will review the changes in the pull request.
   - Ensure that the branch is up to date and all necessary approvals are obtained before merging into `development`.

### Example Commit Based on Conventional Commit:
```
feat(auth): add user authentication functionality

Added login and signup pages
Implemented JWT authentication
Closes #123
```

In the example above:

- **`feat(auth):`**: Indicates the commit type (`feat`) with the scope of the change on the authentication feature (`auth`).
- **`add user authentication functionality`**: A brief description of the changes made.
- **Body**: Contains further explanation of the changes made, such as adding login and signup pages and implementing JWT authentication.
- **Footer**: Contains a reference to the issue being resolved or closed by the commit, in this case, issue number `#123`.
