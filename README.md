# EcoSolve

## Quick start for collaborators

Short, copy-pasteable steps to get started and avoid merge conflicts.

1. Clone the repo

```bash
git clone git@github.com:RamiHsasna/EcoSolve.git
cd EcoSolve
```

2. Create a feature branch from the latest main

```bash
git checkout main
git pull origin main
git checkout -b feature/initials-short-desc
```

3. Work, commit often, push your branch

```bash
git add .
git commit -m "feature short description"
git push -u origin feature/initials-short-desc
```

4. Keep your branch up-to-date (rebase preferred)

```bash
git pull origin/main
```

#Rami

5. Merge branches into main

```bash
git checkout main
git pull origin main

git fetch origin
git branch -a

git merge origin/branch-name
```

6. Clean up after merge

```bash
git checkout main
git pull origin main
git branch -d feature/initials-short-desc
git push origin --delete feature/initials-short-desc
```

Tips

- Branch names: `feature/initials-short-desc`, `fix/initials-short-desc`.
- Use Conventional Commits for messages: `feat:`, `fix:`, `chore:`.
- Keep branches small and rebase frequently to minimize conflicts.

```

```
