<?php

namespace CustomizeProject;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Customizer
{
    public static function customize()
    {
        $customizer = new self();

        try
        {
            $customizer->run();
        }
        catch (\Exception $e)
        {
            print $e->getMessage() . "\n";
            exit ($e->getCode());
        }
    }

    public function run()
    {
        $this->working_dir = dirname(__DIR__);
        $this->project_name = basename($this->working_dir);

        $this->author_name = 'git config user.name';
        $this->author_email = exec('git config user.email');
        $this->copyright_year = date('Y');
        $this->project_camelcase_name = $this->camelCase($this->project_name);
        $this->project_org = getenv('GITHUB_ORG');
        $this->project_name_and_org = $this->project_org . '/' . $this->project_name;

        $this->github_token = getenv('GITHUB_TOKEN');
        $this->travis_token = getenv('TRAVIS_TOKEN');

        // Copy contents of templates directory over the working directory
        $this->placeTemplates();

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
            '/example-project/' => $this->project_name,
            '/ExampleProject/' => $this->project_camelcase_name,
            '/example-org/' => $this->project_org,
            '/Greg Anderson/' => $this->author_name,
            '/greg.1.anderson@greenknowe\.org/' => $this->author_email,
            '/Copyright (c) [0-9]*/' => "Copyright (c) " . $this->copyright_year,
        ];
        $replacements = array_filter($replacements);
        $this->replaceContentsOfAllTemplateFiles($replacements);

        // Composer customizations:
        //    1. Change project name
        //    2. Remove "CustomizeProject\\" from psr-4 autoloader
        //    3. Remove customize and post-install scripts
        $this->adjustComposerJson();

        // Additional cleanup:
        //    1. Remove 'customize' directory
        $this->cleanupCustomization();

        // Update our dependencies after customizing
        passthru('composer -n update');

        // Sanity checks post-customization
        //    1. Dump the autoload file
        //    2. Run the tests
        passthru('composer -n dumpautoload');
        passthru('composer -n test', $status);
        if ($status) {
            throw new \Exception("Tests failed after customization - aborting.");
        }

        // If the existing repository was not preserved, then create
        // a new empty repository now.
        if (!is_dir('.git')) {
            passthru('git init');
        }

        // Repository creation:
        //    1. Add a commit that explains all of the changes made to project.
        //    2. Create a GitHub repository via `hub create`
        //    3. Push code to GitHub
        $this->createRepository();

        // Testing:
        //    1. Enable testing on Travis via `travis enable`
        //    2. Enable testing on AppVeyor (tbd)
        //    3. Enable coveralls (tbd)
        //    4. Enable scrutinizer (tbd)
        $this->enableTesting();

        // Make initial commit.
        // TODO: Make a more robust commit message including everthing that was done.
        passthru('git add .');
        passthru('git commit -m "Initial commit."');

        // Push repository to fire off a build
        passthru("git push -u origin master");

        // Packagist:
        //    1. Register with packagist?  (tbd cli not provided)

    }

    protected function adjustComposerJson()
    {
        $composer_path = $this->working_dir . '/composer.json';
        $composer_contents = file_get_contents($composer_path);
        $composer_data = json_decode($composer_contents, true);

        // Fix the name
        $composer_data['name'] = $this->project_name_and_org;

        // Remove parts of autoloader that are no longer going to be used.
        unset($composer_data['autoload']['psr-4']['CustomizeProject\\']);

        // Remove unused scripts.
        unset($composer_data['scripts']['customize']);
        unset($composer_data['scripts']['post-install-cmd']);

        file_put_contents($composer_path, json_encode($composer_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    protected function placeTemplates()
    {
        $fs = new Filesystem();
        $fs->mirror($this->working_dir . '/customize/templates', $this->working_dir);
    }

    protected function cleanupCustomization()
    {
        $fs = new Filesystem();
        // $fs->remove($this->working_dir . '/customize');
    }

    protected function createRepository()
    {
        // TODO: ensure that 'hub' is installed and print an error message if it isn't.
        passthru("hub create " . $this->project_name_and_org);
    }

    protected function enableTesting()
    {
        // If there is no travis token, log in with the github token
        if (empty($this->travis_token)) {
            passthru("travis login --no-interactive --github-token '{$this->github_token}'");
        }
        else {
            passthru("travis login --no-interactive --token '{$this->travis_token}'");
        }
        // Problem: creating github via 'hub' syncs Travis, causes a failure here.
        // repository not known to Travis CI (or no access?)
        // triggering sync: 409: "{\"message\":\"Sync already in progress. Try again later.\"}"
        passthru('travis sync  --no-interactive --check');
        passthru('travis sync  --no-interactive');

        // Begin testing this repository
        passthru("travis enable --no-interactive");
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
