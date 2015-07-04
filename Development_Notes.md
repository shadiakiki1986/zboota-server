These are misc notes for myself

* Commit and skip travis-ci build
 * add "[ci skip]" to commit message
 * http://docs.travis-ci.com/user/how-to-skip-a-build/
* Push local git tags
 * git push origin --tags
* Modified composer.json
 * composer dump-autoload
 * composer update
* Run unit tests
 * phpunit --verbose tests/
* Modified .travis.yml
 * travis-lint
* encrypting sensitive data in travis
 * travis encrypt 'SOMEVAR=secretvalue' (no need for quotes)
 * http://docs.travis-ci.com/user/encryption-keys/#Note-on-escaping-certain-symbols
* refactoring
 * sed -i "s/require_once '\/etc\/zboota-server-config.php';/require_once dirname(__FILE__).'\/..\/config.php';/g" test^C*.php
* git revert an uncomitted file
 * git checkout path/to/file
* Delete git branch
 * Local: git branch -d lastsuccess
 * Remote: git push origin --delete lastsuccess
* using gh-pages branch
 * 1st checkout: git checkout --track -b gh-pages origin/gh-pages
 * later: git checkout gh-pages    or    git checkout master
 * can commit and push changes normally
