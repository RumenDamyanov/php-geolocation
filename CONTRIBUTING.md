# Contributing to php-geolocation

Thank you for your interest in contributing to php-geolocation! We welcome contributions from the community.

## Code of Conduct

By participating in this project, you agree to abide by our Code of Conduct. Please be respectful and constructive in all interactions.

## How to Contribute

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When creating a bug report, include:

- A clear and descriptive title
- A detailed description of the problem
- Steps to reproduce the behavior
- Expected vs actual behavior
- Environment details (PHP version, framework, etc.)
- Code samples or minimal reproducible examples

### Suggesting Enhancements

Enhancement suggestions are welcome! Please provide:

- A clear and descriptive title
- A detailed description of the proposed enhancement
- Explanation of why this enhancement would be useful
- Possible implementation approach

### Pull Requests

1. Fork the repository
2. Create a new branch from `master` for your feature or fix
3. Make your changes following the coding standards
4. Add or update tests as needed
5. Ensure all tests pass
6. Update documentation if necessary
7. Commit your changes with clear, descriptive messages
8. Push to your fork and submit a pull request

#### Pull Request Guidelines

- Follow PSR-12 coding standards
- Include tests for new functionality
- Maintain or improve code coverage (aim for 100%)
- Update documentation as needed
- Keep commits focused and atomic
- Write clear commit messages

## Development Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/RumenDamyanov/php-geolocation.git
   cd php-geolocation
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Run tests:
   ```bash
   composer test
   ```

4. Run static analysis:
   ```bash
   composer analyze
   ```

5. Check code style:
   ```bash
   composer style
   ```

6. Fix code style issues:
   ```bash
   composer style-fix
   ```

## Coding Standards

- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Write clear and concise comments
- Include docblocks for all public methods
- Maintain consistent indentation (4 spaces)

## Testing

- Write tests for all new functionality
- Ensure tests are readable and well-documented
- Use descriptive test names
- Aim for 100% code coverage
- Test edge cases and error conditions

## Documentation

- Update README.md for significant changes
- Include inline code documentation
- Provide examples for new features
- Keep documentation clear and concise

## Framework Adapters

When adding or modifying framework adapters:

- Ensure they remain framework-agnostic in design
- Provide clear integration examples
- Test compatibility with multiple framework versions
- Follow framework-specific conventions

## Questions?

If you have questions about contributing, feel free to:

- Open an issue for discussion
- Contact the maintainers
- Check existing issues and discussions

Thank you for contributing to php-geolocation!
