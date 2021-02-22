<?php

namespace CustomizeProject;

use Github\HttpClient\Message\ResponseMediator;
use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

use Http\Adapter\Guzzle6\Client as GuzzleClient;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class Customizer
{
    protected $serviceReplacements = [];

    /**
     * It is necessary to explicitly include certain Guzzle files when
     * running code via a Composer script. This workaround is fragile.
     */
    public static function loadGuzzleFunctions()
    {
        $vendor = __DIR__ . '/../vendor';
        require_once "$vendor/guzzlehttp/guzzle/src/functions_include.php";
        require_once "$vendor/guzzlehttp/psr7/src/functions_include.php";
        require_once "$vendor/guzzlehttp/promises/src/functions_include.php";
    }

    public static function customize()
    {
        static::loadGuzzleFunctions();
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

    /**
     * Create a new GitHub API client and authenticate.
     */
    public function createGitHubClient($token)
    {
        $this->gitHubAPI = new \Github\Client();
        $this->gitHubAPI->authenticate($token, null, \Github\Client::AUTH_HTTP_TOKEN);
    }

    /**
     * Get the currently-authenticated username
     */
    public function authenticatedUsername()
    {
        if (!isset($this->authenticated_username)) {
            $authenticated = $this->gitHubAPI->api('current_user')->show();
            $this->authenticated_username = $authenticated['login'];
        }
        return $this->authenticated_username;
    }

    public function run()
    {
        // See the README for instructions on creating access tokens
        $this->github_token = getenv('GITHUB_TOKEN');
        $this->travis_token = getenv('TRAVIS_TOKEN');
        $this->scrutinizer_token = getenv('SCRUTINIZER_TOKEN');
        $this->appveyor_token = getenv('APPVEYOR_TOKEN');

        // TODO: Notify and quit if github_token is not provided, or fails to authenticate.
        $this->createGitHubClient($this->github_token);

        $this->working_dir = dirname(__DIR__);
        $this->project_name = basename($this->working_dir);
        $composer_path = $this->working_dir . '/composer.json';

        $composer_data = $this->readComposerJson($composer_path);

        $this->author_name = exec('git config user.name');
        $this->author_email = exec('git config user.email');
        $this->copyright_year = date('Y');
        $this->creation_date = date('Y/M/d');
        $this->project_camelcase_name = $this->camelCase($this->project_name);
        $this->project_org = getenv('GITHUB_ORG') ?: $this->authenticatedUsername();
        $this->project_name_and_org = $this->project_org . '/' . $this->project_name;

        // If the existing repository was not preserved, then create
        // a new empty repository now. Otherwise, just fix up the
        // existing remote to point to the new location.
        if (!is_dir('.git')) {
            $this->passthru('git init');
            $this->passthru('git add .');
            $this->passthru('git rm -r --cached customize');
            $this->passthru('git commit -m "Initial commit of unmodified template project [ci skip]."');
        }
        else {
            // If we are re-using an existing repo, make sure that the
            // origin is set correctly. If there is no origin, then
            // 'hub' will set the origin.
            @passthru("git remote set-url origin git@github.com:{$this->project_name_and_org}.git");
            @passthru("git remote set-url origin --push git@github.com:{$this->project_name_and_org}.git");
            // Remove the 'composer' remote if it exists.
            @passthru("git remote remove composer 2>/dev/null");
        }

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
        $replacements = [
            '/{{CREATION_DATE}}/' => $this->creation_date,
            '/{{PROJECT}}/' => $this->project_name,
            '/{{PROJECT_CAMELCASE_NAME}}/' => $this->project_camelcase_name,
            '/{{ORG}}/' => $this->project_org,
            '/example-project/' => $this->project_name,
            '/ExampleProject/' => $this->project_camelcase_name,
            '/EXAMPLEPROJECT/' => strtoupper($this->project_camelcase_name),
            '/example-org/' => $this->project_org,
            "#{$composer_data['name']}#" => $this->project_org . '/' . $this->project_name,
            '#' . $this->camelCase($this->project_name) . '#' => $this->project_camelcase_name,
            "/{$composer_data['authors'][0]['name']}/" => $this->author_name,
            "/{$composer_data['authors'][0]['email']}/" => $this->author_email,
            '/Copyright (c) [0-9]*/' => "Copyright (c) " . $this->copyright_year,
            '/{{TEMPLATE_PROJECT}}/' => $this->project_name,
            '/{{TEMPLATE_ORG}}/' => $this->project_org,
        ];
        $template_dir = $this->working_dir . '/customize/templates';
        $this->replaceContentsOfAllTemplateFiles($replacements, $template_dir);

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

        // Create a GitHub repository via GitHub API
        $this->createGitHubRepository($this->working_dir, $this->project_name, getenv('GITHUB_ORG'));

        // Push initial commit with unmodified template
        $this->push();

        // Testing:
        //    1. Enable testing on Travis via `travis enable`
        //    2. Enable testing on AppVeyor
        //    3. Enable testing via Scrutinizer
        //    4. Enable coveralls (TODO API not available)
        $this->enableTesting($this->project_name_and_org);

        $this->enableViolinist($this->project_name_and_org);

        // Replace contents of template files again with service replacements
        $this->serviceReplacements($this->serviceReplacements);

        // Commit modifications to template project.
        // TODO: Make a more robust commit message.
        $this->passthru('git add .');
        $this->passthru('git commit -m "Modifications to template project from customization process."');

        // Push updated changes to fire off a build
        $this->push();

        // We need to explicitly tell Scrutinizer to start analyzing.
        $this->startScrutinizerInspection($this->project_name_and_org);

        // Composer:
        //    1. Register with packagist?  (TODO API not available)
        //    2. Register with violinist.io (TODO API not available)

        // Finished
        print "\nFinished! Visit your new project at https://github.com/{$this->project_name_and_org}\n";
    }

    protected function gitRemote($remote = 'origin')
    {
        return exec("git config --get remote.$remote.url");
    }

    protected function injectGitHubToken($remote = 'origin')
    {
        // If the remote was passed as an identifier, convert it to a URL
        if (preg_match('#[a-zA-Z_-]*#', $remote)) {
            $remote = $this->gitRemote($remote);
        }
        // If the remote was provided as 'git@github.com:org/project.git',
        // then convert it to 'https://github.com/org/project.git'
        $remote = str_replace('git@github.com:', 'https://github.com/', $remote);
        // If the remote goes not have a github token, then inject one.
        $remote = str_replace('https://github.com/', "https://{$this->github_token}:x-oauth-basic@github.com/", $remote);

        return $remote;
    }

    protected function push($remote = 'origin', $branch = 'master')
    {
        $remote = $this->injectGitHubToken($remote);
        $this->passthru("git push -u '$remote' $branch");
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
        unset($composer_data['scripts']['post-create-project-cmd']);

        file_put_contents($composer_path, json_encode($composer_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    protected function cleanupCustomization()
    {
        $fs = new Filesystem();
        $fs->remove($this->working_dir . '/customize');
    }

    protected function addServiceReplacement($keyRegex, $value)
    {
        $this->serviceReplacements[$keyRegex] = $value;
    }

    protected function enableTesting($project)
    {
        $this->enableTravis($project);
        $this->enableAppveyor($project);
        $this->enableScrutinizer($project);
    }

    protected function enableViolinist($project)
    {
        $client = new Client();
        $headers = [
            'X-Violinist-Provider-Token' => $this->github_token,
            'Content-type' => 'application/vnd.api+json',
        ];
        try {
            $result = $client->post('https://violinist.io/api/node/project', [
                'headers' => $headers,
                'body' => json_encode([
                    'data' => [
                        'type' => 'node--project',
                        'attributes' => [
                            'repo' => sprintf('https://github.com/%s', $project),
                        ]
                    ]
                ]),
            ]);
        } catch (\Throwable $e) {
            // @todo Maybe show some sort of error why it failed?
        }
    }

    protected function enableTravis($project)
    {
        try {
            // Log in to Travis with the github token
            passthru("travis login --no-interactive --github-token '{$this->github_token}'");

            // Problem: creating github via 'hub' syncs Travis, causes a failure here.
            // repository not known to Travis CI (or no access?)
            // triggering sync: 409: "{\"message\":\"Sync already in progress. Try again later.\"}"
            // Workaround is to check to see if we are syncing first.
            passthru('travis sync  --no-interactive --check');

            // Begin testing this repository
            passthru("travis enable --no-interactive", $status);

            // If 'travis enable' did not work, perhaps Travis needs more
            // time before the new GitHub repository shows up.
            // TODO: We should *eventually* give up.
            while ($status != 0) {
                print "Waiting for GitHub to advertise the new repository...\n";
                sleep(10);
                passthru('travis sync  --no-interactive');
                passthru("travis enable --no-interactive", $status);
            }

            $travis_url = "https://travis-ci.org/$project";
            $this->addServiceReplacement('#\[Enable Travis CI\]\([^)]*\)#', "[DONE]($travis_url)");
        }
        catch (\Exception $e) {
            $this->addServiceReplacement('#Enable Travis CI#', "Retry Travis CI");
        }
    }

    protected function enableAppveyor($project)
    {
        if (!$this->appveyor_token) {
            print "No APPVEYOR_TOKEN environment variable provided; skipping Appveyor setup.\n";
            return;
        }

        $appveyor_url = "https://ci.appveyor.com/project/$project";

        $uri = 'projects';
        $data = [
            "repositoryProvider" => "gitHub",
            "repositoryName" => "$project",
        ];

        try {
            $this->appveyorAPI($uri, $this->appveyor_token, $data);

            $appveyorStatusBadgeId = $this->appveyorStatusBadgeId($project);
            if ($appveyorStatusBadgeId) {
                $this->addServiceReplacement('#{{PUT_APPVEYOR_STATUS_BADGE_ID_HERE}}#', $appveyorStatusBadgeId);
            }
            $this->addServiceReplacement('#\[Enable Appveyor CI\]\([^)]*\)#', "[DONE]($appveyor_url)");
        }
        catch (\Exception $e) {
            $this->addServiceReplacement('#Enable Appveyor CI#', "Retry Appveyor CI");
        }
    }

    protected function appveyorStatusBadgeId($project)
    {
        if (!$this->appveyor_token) {
            return false;
        }
        $projectSlug = $this->project_name;
        $uri = "projects/$project/settings";
        $appveyorInfo = $this->appveyorAPI($uri, $this->appveyor_token);
        if (!isset($appveyorInfo['settings']['statusBadgeId'])) {
            return false;
        }
        return $appveyorInfo['settings']['statusBadgeId'];
    }

    function appveyorAPI($uri, $token, $data = [], $method = 'GET')
    {
        $url = "https://ci.appveyor.com/api/$uri";
        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'my-org/my-app',
            'Authorization' => "Bearer " . $token,
        ];
        $guzzleParams = [ 'headers' => $headers, ];
        if (!empty($data)) {
            $method = 'POST';
            $guzzleParams['json'] = $data;
        }
        $client = new \GuzzleHttp\Client();
        $res = $client->request($method, $url, $guzzleParams);
        $resultData = json_decode($res->getBody(), true);
        $httpCode = $res->getStatusCode();
        if ($httpCode >= 300) {
            throw new \Exception("Appveyor API call $uri failed with $httpCode");
        }

        return $resultData;
    }

    protected function enableScrutinizer($project)
    {
        if (!$this->scrutinizer_token) {
            print "No SCRUTINIZER_TOKEN environment variable provided; skipping Scrutinizer setup.\n";
            return;
        }

        try {
            $uri = 'repositories/g';
            $data = ['name' => $project];
            $this->scrutinizerAPI($uri, $this->scrutinizer_token, $data);

            // Point to the completed scrutinizer configuration
            $scrutinizer_url = "https://scrutinizer-ci.com/g/$project/";
            $this->addServiceReplacement('#\[Enable Scrutinizer CI\]\([^)]*\)#', "[DONE]($scrutinizer_url)");
        }
        catch (\Exception $e) {
            $this->addServiceReplacement('#Enable Scrutinizer CI#', "Retry Scrutinizer CI");
        }
    }

    protected function startScrutinizerInspection($project)
    {
        if (!$this->scrutinizer_token) {
            return;
        }
        $uri = "repositories/g/$project/inspections";
        $data = ['branch' => 'master'];
        $this->scrutinizerAPI($uri, $this->scrutinizer_token, $data);
    }

    function scrutinizerAPI($uri, $token, $data = [], $method = 'GET')
    {
        $url = "https://scrutinizer-ci.com/api/$uri?access_token=$token";
        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'my-org/my-app',
        ];
        $guzzleParams = [ 'headers' => $headers, ];
        if (!empty($data)) {
            $method = 'POST';
            $guzzleParams['json'] = $data;
        }
        $client = new \GuzzleHttp\Client();
        $res = $client->request($method, $url, $guzzleParams);
        $resultData = json_decode($res->getBody(), true);
        $httpCode = $res->getStatusCode();
        if ($httpCode >= 300) {
            throw new \Exception("Scrutinizer API call $uri failed with $httpCode");
        }

        return $resultData;
    }

    protected function createGitHubRepository($path, $target, $github_org = '')
    {
        // Delete the existing origin if it has been set
        @passthru("git -C '$path' remote remove origin 2>/dev/null");

        // Use GitHub API to create a new repository
        $description = '';
        $homepage = '';
        $public = true;
        $result = $this->gitHubAPI->api('repo')->create($target, $description, $homepage, $public, empty($github_org) ? null : $github_org);

        // Set the remote to point to the repository we just created
        $remote = $result['ssh_url'];
        $this->passthru("git -C '$path' remote add origin '{$remote}'");

        // Add a pointer to our repository
        $repository_url = $result['html_url'];
        $this->addServiceReplacement('#\[Create GitHub repository\]\([^)]*\)#', "[DONE]($repository_url)");
    }

    protected function replaceContentsOfAllTemplateFiles($replacements, $template_dir)
    {
        return $this->operateOnAllProjectFiles($replacements, [$this, 'replaceProjectFileOrTemplate'], $template_dir);
    }

    protected function serviceReplacements($replacements)
    {
        return $this->operateOnAllProjectFiles($replacements, [$this, 'replaceContentsOfFile']);
    }

    protected function operateOnAllProjectFiles($replacements, $fn, $parameter = false)
    {
        $replacements = array_filter($replacements);
        if (empty($replacements)) {
            return;
        }
        $files = Finder::create()
            ->files()
            ->ignoreDotFiles(false)
            ->exclude('customize')
            ->exclude('vendor')
            ->exclude('tools')
            ->in(dirname(__DIR__));
        foreach ($files as $file) {
            $fn($replacements, $file, $parameter);
        }
    }

    protected function replaceProjectFileOrTemplate($replacements, $file, $template_dir)
    {
        $source_file = $file->getRealPath();
        $template_file = $template_dir . '/' . $file->getRelativePathname();
        if (file_exists($template_file)) {
            $source_file = $template_file;
        }
        if (empty($source_file)) {
            return;
        }
        $contents = file_get_contents($source_file);
        $altered_contents = preg_replace(array_keys($replacements), array_values($replacements), $contents);
        $action_label = ($altered_contents != $contents) ? 'Edited ' : 'Copied ';
        print $action_label . $file->getRelativePathname() . "\n";
        file_put_contents($file->getRealPath(), $altered_contents);
    }

    protected function replaceContentsOfFile($replacements, $file)
    {
        $source_file = $file->getRealPath();
        if (empty($source_file)) {
            return;
        }
        $contents = file_get_contents($source_file);
        $altered_contents = preg_replace(array_keys($replacements), array_values($replacements), $contents);
        if ($altered_contents != $contents) {
            print 'Edited ' . $file->getRelativePathname() . "\n";
            file_put_contents($file->getRealPath(), $altered_contents);
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
