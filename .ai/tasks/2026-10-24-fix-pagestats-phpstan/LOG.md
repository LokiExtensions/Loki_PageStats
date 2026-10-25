PATH=$HOME/bin:$PATH act.sh -W .github/workflows/static-tests.yml -j phpstan -P ubuntu-latest=catthehacker/ubuntu:act-latest
act.sh -W .github/workflows/static-tests.yml -P ubuntu-latest=catthehacker/ubuntu:act-latest
hersir.py go; yireo-extension-builder check:all
hersir.py docker-compose exec php-fpm rm -rf dev/tests/integration/tmp/sandbox-*; hersir.py integration-tests
git commit; git push origin master
