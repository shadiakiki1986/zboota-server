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
 * vendor/bin/phpunit tests/
 * vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/
* Modified .travis.yml
 * travis-lint
* encrypting sensitive data in travis
 * travis encrypt SOMEVAR=secretvalue
