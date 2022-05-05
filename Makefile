install:
	composer install

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin

test:
	composer exec --verbose phpunit tests

test-coverage-text:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-text

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

stylish:
	bin/gendiff tests/fixtures/json/file1.json tests/fixtures/json/file2.json

plain:
	bin/gendiff --format plain tests/fixtures/json/file1.json tests/fixtures/json/file2.json

json:
	bin/gendiff --format json tests/fixtures/json/file1.json tests/fixtures/json/file2.json