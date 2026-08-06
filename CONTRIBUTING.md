# Contributing to WP Online Active Users

Thank you for your interest in contributing to the Online Active Users plugin.

## How to contribute

1. Fork this repository.
2. Clone your fork:

```bash
git clone https://github.com/Webizito/online-active-users.git
```

3. Create a new branch:
```bash
git checkout -b my-feature
```

4. Make your changes.
5. Commit your changes:

```bash
git add .
git commit -m "Add new feature"
```

6. Push your branch:
```bash
git push origin my-feature
```
7. Create a Pull Request.

**Reporting bugs**

Please create a GitHub issue and include:
- WordPress version
- Plugin version
- Steps to reproduce the issue
- Screenshots (if needed)

**Code Guidelines**

- Follow WordPress coding standards.
- Keep code clean and documented.
- Test changes before submitting a pull request.

**Running the checks locally**

```bash
composer install
composer lint       # WordPress Coding Standards (PHPCS)
composer lint:fix   # Auto-fix what PHPCBF can
composer analyse    # Static analysis (PHPStan)
composer compat     # PHP 7.3+ compatibility check
composer test       # PHPUnit
```

These same checks run automatically on every pull request via GitHub Actions.

**License**

This plugin is licensed under GPL-3.0-or-later.
