# Starter

A starter PHP project with many services and features pre-configured.

[![Travis CI](https://travis-ci.org/g1a/starter.svg?branch=master)](https://travis-ci.org/example-org/example-project)
[![Windows CI](https://ci.appveyor.com/api/projects/status/ey7eubrwjss0gca6?svg=true)](https://ci.appveyor.com/project/greg-1-anderson/starter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/g-1-a/starter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/g-1-a/starter/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/g-1-a/starter/badge.svg?branch=master)](https://coveralls.io/github/g-1-a/starter?branch=master) 
[![License](https://img.shields.io/badge/license-MIT-408677.svg)](LICENSE)

## Features

The things this project provides or does includes:

<table>
  <tr>
    <td>Creates GitHub project</td>
    <td>README with customization instructions</td>
  </tr>

  <tr>
    <td>Enables Travis testing</td>
    <td>GitHub contributing & issue templates</td>
  </tr>
  
  <tr>
    <td>Enables Appveyor Windows testing</td>
    <td>Optimized composer.json</td>
  </tr>
  
  <tr>
    <td>Enables Scrutinizer static analysis</td>
    <td>Data-driven unit test examples</td>
  </tr>
  
  <tr>
    <td>Links to Coveralls code coverage setup</td>
    <td>Test matrix for PHP 5.6 - 7.2</td>
  </tr>
  
  <tr>
    <td>Links to Packagist setup</td>
    <td>PSR-2 checks and PHP linting</td>
  </tr>
  
  <tr>
    <td>Links to Dependencies.io setup</td>
    <td>List dependency license information</td>
  </tr>
  
  <tr>
    <td>Commandline tool with phar builder</td>
    <td>Automatically update copyright year</td>
  </tr>
  
  <tr>
    <td>Phar self:update command</td>
    <td>Release script (after manual VERSION bump)</td>
  </tr>
  
  <tr>
    <td>Auto-deploy phar on GitHub instructions</td>
    <td> </td>
  </tr>

</table>

For more details, see the section [Explanation of Features](#explanation-of-features), below.

## Usage

To get started, export your [GitHub personal access token](https://help.github.com/articles/creating-an-access-token-for-command-line-use/) and then create a new project.
```
$ export GITHUB_TOKEN='...'
$ export APPVEYOR_TOKEN='...'      # Optional
$ export SCRUTINIZER_TOKEN='...'   # Optional
$ composer create-project g1a/starter my-new-project
```
The new project will be owned by the currently-authenticated user. If you would like to create a new project in an organization instead, then set the `GITHUB_ORG` environment variable.
```
$ export GITHUB_ORG='my-github-username-or-org'
```

Once the new project is created, automated scripts will customize it and set up a number of serivces. See the section [Description of Features](#description-of-features) below for more information. Once the scripts have finished running, you may customize your README file and start coding!

### Prerequisites

Before you begin, make sure that the [Travis CLI tool](https://github.com/travis-ci/travis.rb#installation) has been installed on your system.

### Access Token Generation

Generating and exporting a personal access token for the services below is recommended, as doing this will allow the post-create-project scripts to configure and enable these services automatically.

| Export                         | Token Generation URL       
| ------------------------------ | -------------------------- 
| exoirt GITHUB_TOKEN='...'      | [Generate GitHub token](https://github.com/settings/tokens)
| export APPVEYOR_TOKEN='...'    | [Generate Appveyor token](https://ci.appveyor.com/api-token)   
| export SCRUTINIZER_TOKEN='...' | [Generate Scrutinizer token](https://scrutinizer-ci.com/profile/applications)

### Manual Service Configuration

If the personal access token for these services is not set up, then the service may be manually configured later. In addition, this project is also configured for use with Packagist, Dependencies.io and Coveralls; these services only need to be manually authorized through their web interface to enable them for projects created with this template.

Follow the links in the table below to configure the services you would like to use.

| Feature                   | Manual Setup URL
| ------------------------- | ----------------
| Collaborative repository  | [Create GitHub project](https://github.com/new)
| Linux permutation testing | [Enable Travis CI](https://travis-ci.org/profile)
| Windows testing           | [Enable Appveyor CI](https://ci.appveyor.com/projects/new)
| Static analysis           | [Enable Scrutinizer CI](https://scrutinizer-ci.com/g/new)
| Code coverage             | [Enable Coveralls](https://coveralls.io/repos/new)
| Package manager           | [Register with Packagist](https://packagist.org/packages/submit)
| Dependency updates        | [Enable Dependencies.io](https://app.dependencies.io/add-project)

## Explanation of Features ###

### GitHub project ###

After the `composer create-project` command runs to create your new project from the templates provided, a GitHub project will automatically be created, and your code will automatically be pushed up.

In order for this to work, you must define a `GITHUB_TOKEN` environment variable as described in the [usage](#usage) section.

### README with customization instructions ###

Your new project will be set up with the outline for a project README, complete with status badges, ready to be customized. Follow the instructions provided in your new project to complete the customization steps.

### Travis testing ###

[Travis CI](https://travis-ci.org/) is a testing service that will automatically run your unit tests every time a commit is pushed up to GitHub. Your new project will be automatically configured to run tests on Travis. Travis can authenticate using the `GITHUB_TOKEN` environment variable you already defined, so no extra credentials need to be declared for this feature to work.

### Appveyor Windows testing ###

[Appveyor](https://www.appveyor.com/) is a testing service that will automatically run your unit tests on a Windows environment. If you define an environment variable `APPVEYOR_TOKEN` as described in [Access Token Generation](access-token-generation) above, then Appveyor will be configured to run your tests on every commit. If you do not provide authentication credentals for Appveyor, then the customization instructions for your project will include a link that you may use to turn on this service. Everything is configured for you; authenticating with Appveyor is all that is required.

### Scrutinizer static analysis ###

[Scrutinizer CI](https://scrutinizer-ci.com/) is a service that will perform static analysis of your project on every commit.  If you define an environment variable `SCRUTINIZER_TOKEN` as described in [Access Token Generation](access-token-generation) above, then Scrutinizer will be configured to analyze your code on every commit. If you do not provide authentication credentals for Scrutinizer, then the customization instructions for your project will include a link that you may use to turn on this service. Everything is configured for you; authenticating with Scrutinizer is all that is required.

### Coveralls code coverage ###

[Coveralls](https://coveralls.io/) is a code-coverage tracking tool. Your new project is configured to collect code coverage statistics whenever your phpunit tests are run. Coveralls does not provide an API to enable project tracking, but the customization instructions for your project includes a link that you can click authenitcate with Coverals and enable coverage tracking.

### Packagist code distribution ###

[Packagist](https://packagist.org/) is the main repository for Composer projects. The customization instructions for your project includes a link you may follow to register your project in this repository.

### Dependencies.io automated updates ###

[Dependencies.io](https://www.dependencies.io/) is a service that will automate dependency updates for your project. The customization instructions for your project includes a link to authenticate with dependencies.io; if you enable this service, then a pull request will be created on your project automatically every time any of your Composer dependencies publish a new release.

### Data-driven unit test examples ###

Your new project comes with a trivial `Example` class with tests that demonstrate the phpunit [@dataProvider](https://github.com/g1a/starter/blob/master/tests/ExampleTest.php#L29) feature.

### Test matrix for PHP 5.6 - 7.2 ###

Phpunit 6.x is required to test PHP 7.2, but phpunit 5.x is required to test PHP 5.6 and earlier. Your new project will be set up to use phpunit 6.x when running Travis tests on PHP 7.0 and later, and will fall back to using phpunit 5.x when using PHP version earlier than PHP 7.0.

This functionality is provided using the [Composer test scenarios](https://github.com/g1a/composer-test-scenarios) project.

### PSR-2 checks and PHP linting ###

In addition to providing unit tests with phpunit, your new project will also automatically contain style checks for PSR-2 coding convnetions, and will lint your code for syntax errors and other problems.

These features may be accessed via `composer cs` and `composer lint` commands, respectively. A [.editorconfig](/.editorconfig) file is provided pre-configured to maintain PSR-2 coventions in editors that support this feature.

### GitHub contributing & issue templates ###

GitHub has a feature that allows projects to define [pull request and issue templates](https://help.github.com/articles/about-issue-and-pull-request-templates/) which will be presented to users when a new issue or pull request is opened. Also, a [contributing document](https://help.github.com/articles/setting-guidelines-for-repository-contributors/) can be provided to explain project conventions to new users.

Starter versions for all of these files are automatically added to your new project, and may be customized as needed. By default, the [CONTRIBUTING.md](CONTRIBUTING.md) file is added to the project root for better visiblity, but you may move it to the [.github](.github) directory if you prefer.

### Dependency license information ###

Your project will be set up by default to be offered under the MIT Open-Source license. You may change this by editing your [composer.json](composer.json) and [LICENSE](LICENSE) files.

Every time you run `composer update`, the license information for all of your dependencies will be automatically appended to your LICENSE file. This will allow users interested in your project to quickly ascertain whether the licenses for your dependencies are compatible. This function is provided by the [Composer test scenarios](https://github.com/g1a/composer-test-scenarios) project.

### Automatically update copyright year ###

Whenver your dependency license information is updated, the Copyright year for your project will also be adjusted if necessary, so that your Copyright notice will always include the year of the most recent commit.

### Release script ###

Your project includes a script `composer release` that will automatically tag your project and create a release on GitHub. You must manually commit the desired version number for your project in the [VERSION](VERSION) file before you run this script.

Typically, your VERSION file should be set to the next patch release of your project, followed by `-dev`. For example, if the most recent release of your project is `0.1.0`, then your VERSION file should contain `0.1.1-dev`. Remove the `-dev` to make a release, and then add one to the patch version and replace the `-dev` after your release has rolled out successfully.

### Commandline tool ###

Your project will be set up to build a commandline tool, and also includes commands to package it as a phar. If your project is a library, you might still wish to include a commandline tool to provide ad-hoc usage to your library functions, either for testing purposes, or perhaps to directly edit any data stores managed by your library. The commandline tool dependencies are declared in the `require-dev` section, so they will not be pulled in when your project is included as the dependency of some other project.

If you do not want the commandline tool, simply delete the directories `src/Cli` and `tests/ExampleCommandsTest.php`, and also remove the `phar:*` commands in your composer.json file, and the files `example` and `box.json.dist`.

### Auto-deploy phar on GitHub ###

The customization instructions for your project include a single command you may run to automatically set up auto-deployment of your project's phar with every GitHub release.

### Phar self:update command ###

Users who download your phar file from GitHub may obtain the most recent version available by running the `self:update` command, which is automatically provided in your new project.

### Optimized composer.json ###

The `composer.json` file included in the project pre-configures a few settings for convenience:

- `optimize-autoloader`: Creates larger autoload files that find classes more quickly
- `sort-packages`: Keeps the list of packages in alphabetic order
- `platform:php`: Ensures that Composer will only select packages that are compatible with the stated minimum PHP version.

### Optimized Composer dist releases ###

Your project's [.gitattributes](/.gitattributes) file comes pre-configured to exclude unnecessary files in Composer `dist` releases.

## Old Feature Description

This project comes with a number of configuration files already set up for a number of services. A Composer post-install hook makes further modifications, and, where possible, makes API calls to complete the setup for some services.


This list is being rewritten above, and will be deleted once that effort is complete.

- Project information
  - [composer.json](/composer.json): Automatically customized with project-specific information.
    - Project name (taken from `create-project` project name argument)
    - Author name and email address (from git configuration)
  - [README.md](/customize/templates/README.md): Example template with badges to get you started.
  - [CHANGELOG.md](/CHANGELOG.md): Blank slate provided in the hopes that releases may be recorded here.
  - [LICENSE](/LICENSE): Defaults to MIT. Will automatically be updated with dependency licenses and copyright year every time 'composer update' is run.
- Project metadata
  - [.editorconfig](/.editorconfig): Set up for PSR-2 conventions for compliant editors.
  - [.gitattributes](/.gitattributes): Ensures that tests, build results and so on are not exported to Packagist.
  - [.gitignore](/.gitignore): Ensures that vendor directory and so on is not committed to the repository.
- Repository
  - **GitHub:** Automatically creates a new repository on GitHub and pushes up your new project. Starter GitHub contribution templates are provided:
    - [CONTRIBUTING.md](/CONTRIBUTING.md)
    - Issue templates for [bug reports](/.github/ISSUE_TEMPLATE/bug_report_or_support_request.md), [documentation](/.github/ISSUE_TEMPLATE/documentation_request.md) and [feature requests](/.github/ISSUE_TEMPLATE/feature_request.md)
    - [pull_request_template](/.github/pull_request_template.md)
  - Tag and push a release, as identified in the VERSION file.
    - $ `composer release`
- Commandline Tool
  - Comes with an [example command line tool](/src/cli/ExampleCommands.php) based on the [Robo PHP task runner](https://robo.li/getting-started/), to make it easy to add commands for ad-hoc execution of your project classes.
  - Commandline dependencies are declared in the `require-dev` section of the composer.json file, other parties that wish to use your project as a library will not unnecessarily inherit them.
  - Build a phar version of your commandline tool locally:
    - $ `composer phar:install-tools`
    - $ `composer phar:build`
- Testing
  - **Travis:** Automatically enables testing for the new project in Travis.
    - [phpunit.xml.dist](/phpunit.xml.dist): Test configuration with code coverage (html coverage report configuration is present, but commented out).
    - [Example.php](/src/Example.php): A simple class that multiplies. Replace with your own code.
    - [ExampleTest.php](/tests/ExampleTest.php): A simple data-driven test that pulls fixture data from a data provider. Replace with your own tests.
    - [ExampleCommandsTest.php](/tests/ExampleCommandsTest.php): Another simple data-driven test that pulls commandline arguments from fixture data and runs it through the commandline tool. Replace with your own tests.
  - **Coveralls:** Project must be manually configured on [coveralls.io](https://coveralls.io). PHPUnit and Travis are already configured to export coverage data to Coveralls automatically; all that needs to be done is to enable the Coveralls service for the new project.
  - **Appveyor:** An appveyor configuration file is provided. If an APPVEYOR_TOKEN environment variable is defined when the project is created, Appveyor testing will be automatically configured. Otherwise, it will need to be manually enabled on [appveyor](https://www.appveyor.com/) if Windows testing is desired.
  - **Scrutinizer:** If a SCRUTINIZER_TOKEN environment variable is defined when the project is created, then Scrutinizer static code analysis is automatically enabled. Otherwise, the project must be manually enabled on [scrutinizer-ci.com](https://scrutinizer-ci.com).
  - Provides handy composer scripts for testing:
    - `composer test`: Run all tests.
    - `composer unit`: Run just the phpunit tests.
    - `composer lint`: Run the php linter.
    - `composer cs`: Run the code sniffer to check for PSR-2 compliance.
    - `composer cbf`: Fix code style violations where possible.
- Composer
  - **Packagist:** Project must be manually submitted to [packagist.org](https://packagist.org) in order to be able to `require` it as a dependency of some other project.
  - **Dependencies:** A [dependencies.yml](/dependencies.yml) configuration file is provided; if the project is enabled on [dependencies.io](https://www.dependencies.io/), then pull requests will automatically be created any time newer versions of the project's dependencies become available.
  - [Composer test scenarios](https://github.com/g1a/composer-test-scenarios) are configured to allow tests to be written for PHPUnit 6, and still use PHPUnit 5 for testing on PHP 5.6. Highest/lowest testing is also configured by default. This project also contains the scripts used to keep the LICENSES file up-to-date for this project's dependencies.

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on the process for submitting pull requests to us.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- This project makes heavy use of configuration techniques and code from [Drush](https://drush.org), [Robo PHP](https://robo.li) and other [Consolidation projects](https://github.com/consolidation).
- The [KnpLabs github-api](https://github.com/KnpLabs/php-github-api) and [guzzle](http://docs.guzzlephp.org/en/stable/) made the API calls done by this project very easy.
