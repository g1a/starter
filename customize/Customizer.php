<?php

namespace CustomizeProject;

use Symfony\Component\Finder\Finder;

class Customizer
{
    public static function customize()
    {
        $customizer = new self();
        $customizer->run();
    }

    public function run()
    {
        $local_working_dir = dirname(__DIR__);
        $project_name = basename($local_working_dir);

        $variables = [
            'author_name' => exec('git config user.name'),
            'author_email' => exec('git config user.email'),
            'copyright_year' => date('Y'),
            'working_dir' => $local_working_dir,
            'project_name' => $project_name,
            'project_camelcase_name' => $this->camelCase($project_name),
            'project_org' => getenv('GITHUB_ORG'),
        ];

        // Replacements:
        //    1. Project
        //       a. Project name (e.g. example-project)
        //       b. Project camelcase name (e.g. ExampleProject)
        //       c. Project organization (e.g. example-org)
        //    2. Credits
        //       a. Author name
        //       b. Author email address
        //       c. Copyright date
        //
        $replacements = [
            '/example-project/' => $variables['project_name'],
            '/ExampleProject/' => $variables['project_camelcase_name'],
            '/example-org/' => $variables['project_org'],
            '/Greg Anderson/' => $variables['author_name'],
            '/greg.1.anderson@greenknowe\.org/' => $variables['author_email'],
            '/Copyright (c) [0-9]*/' => "Copyright (c) ${variables['copyright_year']}",
        ];
        $replacements = array_filter($replacements);
        $this->replaceContentsOfAllTemplateFiles($replacements);

        // Composer customizations:
        //    1. Change project name
        //    2. Remove "CustomizeProject\\" from psr-4 autoloader
        //    3. Remove customize and post-install scripts
        $this->adjustComposerJson($variables);

        // Additional cleanup:
        //    1. Remove 'customize' directory
        $this->cleanupCustomization($variables);

        // Sanity checks post-customization
        //    1. Dump the autoload file
        //    2. Run the tests
        passthru('composer dumpautoload');
        passthru('composer test', $status);
        if ($status) {
            throw \Exception("Tests failed after customization - aborting.");
        }

        // Repository creation:
        //    1. Add a commit that explains all of the changes made to project.
        //    2. Create a GitHub repository via `hub create`
        //    3. Push code to GitHub

        // Testing:
        //    1. Enable testing on Travis via `travis enable`
        //    2. Enable testing on AppVeyor (tbd)
        //    3. Enable coveralls (tbd)
        //    4. Enable scrutinizer (tbd)

        // Packagist:
        //    1. Register with packagist?  (tbd cli not provided)

    }

    protected function adjustComposerJson($variables)
    {
        $composer_path = $variables['working_dir'] . '/composer.json';
        $composer_contents = file_get_contents($composer_path);
        $composer_data = json_decode($composer_contents, true);

        var_export($composer_data);

        // Fix the name
        $composer_data['name'] = "${variables['project_org']}/${variables['project_name']}";

        // Remove parts of autoloader that are no longer going to be used.
        unset($composer_data['autoload']['psr-4']['CustomizeProject\\']);

        // Remove unused scripts.
        unset($composer_data['scripts']['customize']);
        unset($composer_data['scripts']['post-install-cmd']);

        file_put_contents($composer_path, json_encode($composer_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    protected function cleanupCustomization($variables)
    {
    }

    protected function replaceContentsOfAllTemplateFiles($replacements)
    {
        $files = Finder::create()
            ->files()
            ->exclude('customize')
            ->exclude('vendor')
            ->in(dirname(__DIR__));
        foreach ($files as $file) {
            $this->replaceContentsOfFile($replacements, $file);
        }
    }

    protected function replaceContentsOfFile($replacements, $file)
    {
        if (empty($file->getRealPath())) {
            return;
        }
        print "Replace " . $file->getRelativePathname() . "\n";
        $contents = file_get_contents($file->getRealPath());
        $altered = preg_replace(array_keys($replacements), array_values($replacements), $contents);
        if ($altered != $contents) {
            file_put_contents($file->getRealPath(), $altered);
        }
    }

    protected function camelCase($str)
    {
        return str_replace('-', '', ucwords($str, '-'));
    }
}
