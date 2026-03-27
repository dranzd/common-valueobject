# GitHub Actions Setup for Private Repositories

## Issue
The GitHub Actions workflow fails when trying to install private Composer dependencies:
- `dranzd/common-domain-assert`
- `dranzd/common-utils`

Error: `Authentication failed for 'https://github.com/dranzd/common-utils.git/'`

## Solution

### 1. Workflow Configuration (Already Applied)

The workflow now includes:

```yaml
permissions:
  contents: read
  packages: read

jobs:
  test:
    steps:
    - name: Setup Composer authentication
      run: |
        composer config --global github-oauth.github.com ${{ secrets.GITHUB_TOKEN }}
```

### 2. Repository Settings Required

You need to configure repository access for the `GITHUB_TOKEN`:

#### Option A: Using GITHUB_TOKEN (Recommended for same organization)

1. Go to your repository settings: `https://github.com/dranzd/common-valueobject/settings`
2. Navigate to **Actions** → **General**
3. Scroll to **Workflow permissions**
4. Ensure "Read repository contents and packages permissions" is selected
5. **Important**: The `GITHUB_TOKEN` can only access repositories in the same organization/account

#### Option B: Using Personal Access Token (PAT) (If repos are in different accounts)

If your private dependencies are in a different GitHub account:

1. Create a Personal Access Token:
   - Go to GitHub Settings → Developer settings → Personal access tokens → Tokens (classic)
   - Click "Generate new token (classic)"
   - Give it a name: `COMPOSER_PRIVATE_REPOS`
   - Select scopes: `repo` (Full control of private repositories)
   - Generate and copy the token

2. Add the token as a repository secret:
   - Go to repository settings: `https://github.com/dranzd/common-valueobject/settings/secrets/actions`
   - Click "New repository secret"
   - Name: `COMPOSER_AUTH_TOKEN`
   - Value: Paste your PAT
   - Click "Add secret"

3. Update the workflow to use the PAT:
   ```yaml
   - name: Setup Composer authentication
     run: |
       composer config --global github-oauth.github.com ${{ secrets.COMPOSER_AUTH_TOKEN }}
   ```

### 3. Alternative: Make Dependencies Public

If these are utility libraries that don't contain sensitive code, consider making them public repositories.

## Verification

After applying the fix, the workflow should:
1. ✅ Authenticate with GitHub
2. ✅ Download private dependencies
3. ✅ Run tests
4. ✅ Run PHPStan
5. ✅ Check code style

## Current Workflow Features

- **PHP Versions**: 8.3 and 8.4
- **Quality Checks**:
  - Composer validation
  - PHPUnit tests
  - PHPStan static analysis (Level 5)
  - PHPCS code style (PSR-12)
- **Permissions**: Read-only access to contents and packages
