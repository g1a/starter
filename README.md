# Starter

A starter PHP project with many services and features pre-configured. Simply clone and then customize to suit.

## Usage

To get started, export your [GitHub personal access token](https://help.github.com/articles/creating-an-access-token-for-command-line-use/) and the GitHub account or organization where you would like the project to be created.
```
$ export GITHUB_TOKEN='[REDACTED]'
$ export GITHUB_ORG='my-github-username-or-org'
$ composer create-project g-1-a/starter my-new-project
```

## Features

This project comes with a number of configuration files already set up for a number of services. A Composer post-install hook makes further modifications, and, where possible, makes API calls to complete the setup for some services.

The following things are provided:

- Project information
  - [composer.json](/composer.json): Fills out initial project information
    - Project name (taken from `create-project` project name argument)
    - Author name and email address (from git configuration)
    - Copyright notice (current year)
  - [README.md](/customize/templates/README.md): Example template with badges to get you started.
  - [CHANGELOG.md](/CHANGELOG.md): Blank slate provided in the hopes that releases may be recorded here.
  - [LICENSE](/LICENSE): Defaults to MIT.
- Project metadata
  - [.editorconfig](/.editorconfig): Set up for PSR-2 conventions for compliant editors.
  - [.gitattributes](/.gitattributes): Ensures that tests, build results and so on are not exported to Packagist.
  - [.gitignore](/.gitignore): Ensures that vendor directory and so on is not committed to the repository.
- Repository
  - **GitHub:** Automatically creates a new repository on GitHub and pushes up your new project. Starter GitHub contribution templates are provided:
    - [CONTRIBUTING.md](/CONTRIBUTING.md)
    - [issue_template.md](/.github/issue_template.md)
    - [pull_request_template](/.github/pull_request_template.md)    
- Testing
  - **Travis:** Automatically enables testing for the new project in Travis.
    - [phpunit.xml.dist](/phpunit.xml.dist): Test configuration with code coverage (html coverage report configuration is present, but commented out).
    - [Example.php](/src/Example.php): A simple class that multiplies.
    - [ExampleTest.php](/tests/ExampleTest.php): A simple data-driven test that pulls fixture data from a data provider.
  - **Coveralls:** Project must be manually configured on [coveralls.io](https://coveralls.io). PHPUnit and Travis are already configured to export coverage data to Coveralls automatically.
  - **Appveyor:** An appveyor configuration file is provided, but project must be manually enabled on [appveyor](https://www.appveyor.com/) if Windows testing is desired.
  - [Composer test scenarios](https://github.com/g-1-a/composer-test-scenarios) are configured to allow tests to be written for PHPUnit 6, and still use PHPUnit 5 for testing on PHP 5.6. Highest/lowest testing is also configured by default.
  - **Scrutinizer:** Project must be manually enabled on [scrutinizer-ci.com](https://scrutinizer-ci.com).
- Code Sharing
  - **Packagist:** Project must be manually submitted to [packagist.org](https://packagist.org)
  - **Dependencies:** A [dependencies.yml](/dependencies.yml) is provided; project must be enabled on [dependencies.io](https://www.dependencies.io/)

After the `composer create-project` completes running, your new project will exist on GitHub, and the first test (with placeholder code) should already be queued to run on Travis. Run through the list above, customize as needed, enable services that were not automatically configured, and start writing your own code.
