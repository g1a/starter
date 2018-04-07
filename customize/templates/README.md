# ExampleProject

Put a one-line description of your project here.

[![Travis CI](https://travis-ci.org/example-org/example-project.svg?branch=master)](https://travis-ci.org/example-org/example-project)
[![Windows CI](https://ci.appveyor.com/api/projects/status/{{PUT_APPVEYOR_STATUS_BADGE_ID_HERE}}?svg=true)](https://ci.appveyor.com/project/example-org/example-project)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/example-org/example-project/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/example-org/example-project/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/example-org/example-project/badge.svg?branch=master)](https://coveralls.io/github/example-org/example-project?branch=master) 
[![License](https://img.shields.io/badge/license-MIT-408677.svg)](LICENSE)

<!-- 
There are two choices for LICENSE badges:

1. License using shields.io (above): Can contain any text you want, and has no prerequisites, but must be manually updated if you change the license.
2. License using poser.pugx.org (below): shows the license that Packagist.org read from your composer.json file. Must register with Packagist to use Poser.

[![License](https://poser.pugx.org/example-org/example-project/license)](https://github.com/example-org/example-project//master/LICENSE)
-->

## FINISH CUSTOMIZATION

Follow the steps in this section to complete the customization of your new project.

### Services

Enable those services shown below that have not already been configured:

| Feature                   | Setup
| ------------------------- | ----------------
| Collaborative repository  | [Create GitHub repository](https://github.com/new)
| Linux permutation testing | [Enable Travis CI](https://travis-ci.org/profile)
| Windows testing           | [Enable Appveyor CI](https://ci.appveyor.com/projects/new)
| Static analysis           | [Enable Scrutinizer CI](https://scrutinizer-ci.com/g/new)
| Code coverage             | [Enable Coveralls](https://coveralls.io/repos/new)
| Package manager           | [Register with Packagist](https://packagist.org/packages/submit)
| Dependency updates        | [Enable Dependencies.io](https://app.dependencies.io/add-project)

### Commandline Tool

To customize the name of your commandline tool:

- Rename the file `example` (the executable / front controller)
- Replace any `example` or `example.phar` in [box.json.dist](/box.json.dist) and [.gitignore](/.gitignore) with the desired name for your phar

### Documentation

Once you have finished setting up your project, delete this section and fill out the other sections of this README.

## Getting Started

Explain how to get a copy of this project up and running on a new user's local machine.

See deployment for notes on how to deploy the project on a live system.

### Prerequisites

List the things that are needed to install the software and how to install them. For most PHP projects, it should usually be sufficient to run:

```
composer install
```

If you wish to build the phar for this project, install the `box` phar builder via:

```
composer phar:install-tools
```

### Installing

Provide a step by step series of examples that show how to install this project.

Say what the step will be. If the phar for this project is the primary output, and not a mere development utility, then perhaps the first step will be to build the phar:

```
composer phar:build
```

It may then be sufficient to install via:

```
cp example.phar /usr/local/bin
```

End with an example of getting some data out of the system or using it for a little demo.

## Running the tests

The test suite may be run locally by way of some simple composer scripts:

| Test             | Command
| ---------------- | ---
| Run all tests    | `composer test`
| PHPUnit tests    | `composer unit`
| PHP linter       | `composer lint`
| Code style       | `composer cs`     
| Fix style errors | `composer cbf`


## Deployment

Add additional notes about how to deploy this on a live system.

## Built With

Include all that apply:

* [Composer](https://getcomposer.org/) - Dependency Management
* [Robo](https://robo.li/) - PHP Task Runner
* [Symfony](https://symfony.com/) - PHP Framework

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/example-org/example-project/tags). 

## Authors

* **Greg Anderson** - created project from template.

See also the list of [contributors](https://github.com/example-org/example-project/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Acknowledgments

* Hat tip to anyone who's code was used
* Inspiration
* etc
* Thanks to PurpleBooth for the [example README template](https://gist.github.com/PurpleBooth/109311bb0361f32d87a2)
