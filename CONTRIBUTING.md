# Contributing to ExampleProject

Thank you for your interest in contributing to ExampleProject! Here are some of the guidelines you should follow to make the most of your efforts:

## Code Style Guidelines

This project adheres to the [PSR-2 Coding Style Guide](http://www.php-fig.org/psr/psr-2/) for PHP code. An `.editorconfig` file is included with the repository to help you get up and running quickly. Most modern editors support this standard, but if yours does not or you would like to configure your editor manually, follow the guidelines in the document linked above.

You can run the PHP Codesniffer on your work using a convenient command provided as a composer script:
```
composer cs
```
The above will run the PHP Codesniffer on the project sources. To automatically clean up code style violations, run:
```
composer cbf
```
Please ensure all contributions are compliant _before_ submitting a pull request.
