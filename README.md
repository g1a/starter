# Starter

A starter PHP project with many services and features pre-configured.

[![License](https://img.shields.io/badge/license-MIT-408677.svg)](LICENSE)

## Features

The things this project provides or does includes:

<table>
  <tr>
    <td><a href="#github-project">Creates GitHub project</a></td>
    <td><a href="#readme-template">README with customization instructions</a></td>
  </tr>

  <tr>
    <td><a href="#optimized-composerjson">Optimized composer.json</a></td>
    <td><a href="#github-contributing-and-issue-templates">GitHub contributing and issue templates</a></td>
  </tr>
    
  <tr>
    <td><a href="#commandline-tool">Commandline tool with phar builder</a></td>
    <td><a href="#data-driven-unit-test-examples">Data-driven unit test examples</a></td>
  </tr>
  
  <tr>
    <td><a href="#coveralls-code-coverage">Links to Coveralls code coverage setup</a></td>
    <td><a href="#test-matrix-for-php-80---81">Test matrix for PHP 8.0 - 8.1</a></td>
  </tr>
  
  <tr>
    <td><a href="#packagist-code-distribution">Links to Packagist setup</a></td>
    <td><a href="#psr-2-checks-and-php-linting">PSR-2 checks and PHP linting</a></td>
  </tr>
      
  <tr>
    <td><a href="#phar-selfupdate-command">Phar self:update command</a></td>
    <td><a href="#configuration-files">Configuration files</a></td>
  </tr>

</table>

For more details, see the section [Explanation of Features](#explanation-of-features), below.

## Usage

To get started, export your [GitHub personal access token](https://help.github.com/articles/creating-an-access-token-for-command-line-use/) and then create a new project.
```
$ export GITHUB_TOKEN='...'
$ composer create-project g1a/starter my-new-project
```
The new project will be owned by the currently-authenticated user. If you would like to create a new project in an organization instead, then set the `GITHUB_ORG` environment variable.
```
$ export GITHUB_ORG='my-github-username-or-org'
```
The new project will work only with php 8.0 and later. If you need to use an earlier version of php (as far back as php 7.1), then instead run:
```
$ composer create-project g1a/starter my-new-project:^2
```

Once the new project is created, automated scripts will customize it and set up a number of serivces. See the section [Description of Features](#description-of-features) below for more information. Once the scripts have finished running, you may customize your README file and start coding!

### Access Token Generation

Generating and exporting a personal access token for the services below is recommended, as doing this will allow the post-create-project scripts to configure and enable these services automatically.

| Export                         | Token Generation URL       
| ------------------------------ | -------------------------- 
| exoirt GITHUB_TOKEN='...'      | [Generate GitHub token](https://github.com/settings/tokens)

### Manual Service Configuration

If the personal access token for these services is not set up, then the service may be manually configured later. In addition, this project is also configured for use with Packagist, this service only needs to be manually authorized through their web interface to enable them for projects created with this template.

Follow the links in the table below to configure the services you would like to use.

| Feature                   | Manual Setup URL
| ------------------------- | ----------------
| Collaborative repository  | [Create GitHub project](https://github.com/new)
| Package manager           | [Register with Packagist](https://packagist.org/packages/submit)

## Explanation of Features ###

### GitHub project ###

After the `composer create-project` command runs to create your new project from the templates provided, a GitHub project will automatically be created, and your code will automatically be pushed up.

In order for this to work, you must define a `GITHUB_TOKEN` environment variable as described in the [usage](#usage) section.

### README template ###

Your new project will be set up with the outline for a project README, complete with status badges, ready to be customized. Follow the instructions provided in your new project to complete the customization steps.

### GitHub Actions Testing ###

GitHub actions will automatically run your unit tests every time a commit is pushed up to GitHub.

### Packagist code distribution ###

[Packagist](https://packagist.org/) is the main repository for Composer projects. The customization instructions for your project includes a link you may follow to register your project in this repository.

### Data-driven unit test examples ###

Your new project comes with a trivial `Example` class with tests that demonstrate the phpunit [@dataProvider](https://github.com/g1a/starter/blob/master/tests/ExampleTest.php#L29) feature.

### Test matrix for PHP 8.0 - 8.1 ###

The included test suite demonstrates testing on multiple platforms and PHP versions.

### PSR-2 checks and PHP linting ###

In addition to providing unit tests with phpunit, your new project will also automatically contain style checks for PSR-2 coding convnetions, and will lint your code for syntax errors and other problems.

These features may be accessed via `composer cs` and `composer lint` commands, respectively. A [.editorconfig](/.editorconfig) file is provided pre-configured to maintain PSR-2 coventions in editors that support this feature.

### GitHub contributing and issue templates ###

GitHub has a feature that allows projects to define [pull request and issue templates](https://help.github.com/articles/about-issue-and-pull-request-templates/) which will be presented to users when a new issue or pull request is opened. Also, a [contributing document](https://help.github.com/articles/setting-guidelines-for-repository-contributors/) can be provided to explain project conventions to new users.

Starter versions for all of these files are automatically added to your new project, and may be customized as needed. By default, the [CONTRIBUTING.md](CONTRIBUTING.md) file is added to the project root for better visiblity, but you may move it to the [.github](.github) directory if you prefer.

### Commandline tool ###

Your project will be set up to build a commandline tool, and also includes commands to package it as a phar using the [humbug/box](https://github.com/humbug/box) project. If your project is a library, you might still wish to include a commandline tool to provide ad-hoc usage to your library functions, either for testing purposes, or perhaps to directly edit any data stores managed by your library. The commandline tool dependencies are declared in the `require-dev` section, so they will not be pulled in when your project is included as the dependency of some other project.

If you do not want the commandline tool, simply delete the directories `src/Cli` and `tests/ExampleCommandsTest.php`, and also remove the `phar:*` commands in your composer.json file, and the files `example` and `box.json.dist`.

### Optimized composer.json ###

The `composer.json` file included in the project pre-configures a few settings for convenience:

- `optimize-autoloader`: Creates larger autoload files that find classes more quickly
- `sort-packages`: Keeps the list of packages in alphabetic order
- `platform:php`: Ensures that Composer will only select packages that are compatible with the stated minimum PHP version.

### Optimized Composer dist releases ###

Your project's [.gitattributes](/.gitattributes) file comes pre-configured to exclude unnecessary files in Composer `dist` releases.

### Configuration files ###

Your project will automatically read in a configuration file in yaml format that you may use for providing command option default values and storing other user-overridable settings. See [consolidation/config](https://github.com/consolidation/config) for more information.

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on the process for submitting pull requests to us.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- This project makes heavy use of configuration techniques and code from [Drush](https://drush.org), [Robo PHP](https://robo.li) and other [Consolidation projects](https://github.com/consolidation).
- The [KnpLabs github-api](https://github.com/KnpLabs/php-github-api) and [guzzle](http://docs.guzzlephp.org/en/stable/) made the API calls done by this project very easy.
