<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'Magazine_Checker');

// Project repository
set('repository', 'git@github.com:yamanouehare/Magazine-Checker.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
//set('shared_files', []);
set('shared_files', [
  'config/analyticstracking.php',
  'config/DBConfig.php'
]);
//set('shared_dirs', []);
set('shared_dirs', [
  'vendor'
]);

// Writable dirs by web server 
set('writable_dirs', []);


// Hosts

//host('project.com')
//    ->set('deploy_path', '~/{{application}}');    
host('magazine-checker')
    ->set('deploy_path', '/var/www/{{application}}');    
    

// Tasks

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
