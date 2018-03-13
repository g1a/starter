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
        $composer_path = $this->working_dir . '/composer.json';

        $composer_data = $this->readComposerJson($composer_path);

        $this->author_name = exec('git config user.name');
        $this->author_email = exec('git config user.email');
        $this->copyright_year = date('Y');
        $this->creation_date = date('Y/M/d');
        $this->project_camelcase_name = $this->camelCase($this->project_name);
        $this->project_org = getenv('GITHUB_ORG');
        $this->project_name_and_org = $this->project_org . '/' . $this->project_name;

        $this->github_token = getenv('GITHUB_TOKEN');
        $this->travis_token = getenv('TRAVIS_TOKEN');

        // Copy contents of templates directory over the working directory
        $this->placeTemplates();

        // Composer customizations:
        //    1. Change project name
        //    2. Remove "CustomizeProject\\" from psr-4 autoloader
        //    3. Remove customize and post-install scripts
        $this->adjustComposerJson($composer_path, $composer_data);

        // General replacements:
        //    1. Project
        //       a. Project name (e.g. example-project)
        //       b. Project camelcase name (e.g. ExampleProject)
        //       c. Project organization (e.g. example-org)
        //    2. Credits
        //       a. Author name
        //       b. Author email address
        //       c. Copyright date
        // Note that these apply to all files in the project, including
        // composer.json (also customized above).
        $original_project_name_and_org = explode('/', $composer_data['name'], 2);
        $replacements = [
            '/{{CREATION_DATE}}/' => $this->creation_date,
            '/{{PROJECT}}/' => $this->project_name,
            '/{{PROJECT_CAMELCASE_NAME}}/' => $this->project_camelcase_name,
            '/{{ORG}}/' => $this->project_org,
            '/example-project/' => $this->project_name,
            '/ExampleProject/' => $this->project_camelcase_name,
            '/example-org/' => $this->project_org,
            "#{$composer_data['name']}#" => $this->project_org . '/' . $this->project_name,
            '#' . $this->camelCase($original_project_name_and_org[1]) . '#' => $this->project_camelcase_name,
            "/{$composer_data['authors'][0]['name']}/" => $this->author_name,
            "/{$composer_data['authors'][0]['email']}/" => $this->author_email,
            '/Copyright (c) [0-9]*/' => "Copyright (c) " . $this->copyright_year,
            '/{{TEMPLATE_PROJECT}}/' => $original_project_name_and_org[1],
            '/{{TEMPLATE_ORG}}/' => $original_project_name_and_org[0],
        ];
        $replacements = array_filter($replacements);
        $this->replaceContentsOfAllTemplateFiles($replacements);

        // Additional cleanup:
        //    1. Remove 'customize' directory
        $this->cleanupCustomization();

        // Update our dependencies after customizing
        $this->passthru('composer -n update');

        // Sanity checks post-customization
        //    1. Dump the autoload file
        //    2. Run the tests
        $this->passthru('composer -n dumpautoload');
        $this->passthru('composer -n test');

        // If the existing repository was not preserved, then create
        // a new empty repository now.
        if (!is_dir('.git')) {
            $this->passthru('git init');
        }
        else {
            // If we are re-using an existing repo, make sure that the
            // origin is set correctly. If there is no origin, then
            // 'hub' will set the origin.
            @passthru("git remote set-url origin git@github.com:{$this->project_name_and_org}.git");
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
        $this->passthru('git add .');
        $this->passthru('git commit -m "Initial commit."');

        // Push repository to fire off a build
        $this->passthru("git push -u origin master");

        // Composer:
        //    1. Register with packagist?  (tbd cli not provided)
        //    2. Register with dependencies.io (tbd)
    }

    protected function readComposerJson($composer_path)
    {
        $composer_contents = file_get_contents($composer_path);
        return json_decode($composer_contents, true);
    }

    protected function adjustComposerJson($composer_path, $composer_data)
    {
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

    protected function passthru($cmd)
    {
        passthru($cmd, $status);
        if ($status != 0) {
            throw new \Exception('Command failed with exit code ' . $status);
        }
    }
}
