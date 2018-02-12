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
set('shared_files', [
  'config/DBConfig.php',
  'config/EnvConfig.php',
]);
set('shared_dirs', [
  'vendor'
]);

//管理するリリース数
set('keep_releases', 5);  

// httpサーバのユーザ：nginx
set('http_user','nginx');
// Hosts
host('magazine-checker')
    ->set('deploy_path', '/var/www/{{application}}');


/*-------------------------------
 * 所有者変更タスク
 -------------------------------*/
desc('chown_folder');
task('chown_folder', function () {
    writeln("<comment>twig_cacheの所有者をnginxに変更</comment>");
    $result = run('pwd');
    writeln("初期パス: $result");
    //htdocs配下に移動し所有者変更
    $chown = 'chown -R nginx:nginx twig_cache';
    run('cd ' . get('release_path') . '/tmp/'. ' && '. $chown);
    writeln("変更完了");
    writeln("<comment>php-fpmを再起動</comment>");
    run('systemctl restart php72-php-fpm.service');
});

/*-------------------------------
 * データフォルダ配下を転送するタスク
 -------------------------------*/
desc('upload_file');
task('upload_file', function () {
    writeln("<comment>db/data配下のファイルをアップロードします。</comment>");
    $appFiles = [
        'db/data',
    ];
    $releasePath = get('release_path');

    foreach ($appFiles as $file) {
        upload("./{$file}", "{$releasePath}/db/");
    }
})->desc('Upload static file');

/*-------------------------------
 * マイグレーションタスク
 -------------------------------*/
desc('migration');
task('migration', function () {
    writeln("<comment>マイグレーションを実行します</comment>");
    $execphp = 'php72 migrate.php';
    $result = run('cd ' . get('release_path') . '/script/'. ' && '. $execphp);
    writeln("結果: $result");
})->desc('exec migration');

//標準タスク
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
    'upload_file',      //アップロード
    'migration',        //マイグレーション
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

//デプロイ後に「chown_folder」実行
after('success', 'chown_folder');