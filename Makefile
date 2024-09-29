validate:
	composer validate;
install:
	composer install;
autoload:
	composer dump-autoload;
diff:
	./bin/gendiff;
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin;
test:
	composer exec --verbose phpunit tests;
update:
	composer update;
test-coverage:
	composer exec --verbose XDEBUG_MODE=coverage phpunit tests -- --coverage-clover build/logs/clover.xml;