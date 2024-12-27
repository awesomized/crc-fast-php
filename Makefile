.PHONY: build
build:
	@if [ ! -d "./build" ]; then git clone https://github.com/awesomized/crc64fast-nvme.git build; fi
	@cd build && git checkout add-c-compatible-library
	@cd build && cargo build --release

.PHONY: validate
validate: phpcs php-cs-fixer-check static-analysis test cli

.PHONY: repair
repair: phpcbf php-cs-fixer-fix

.PHONY: static-analysis
static-analysis: phpstan psalm

.PHONY: test
test: phpunit

.PHONY: phpstan
phpstan:
	vendor/bin/phpstan

.PHONY: psalm
psalm:
	vendor/bin/psalm

.PHONY: phpcs
phpcs:
	vendor/bin/phpcs -s --standard=phpcs.xml .github cli src tests

.PHONY: php-cs-fixer-check
php-cs-fixer-check:
	vendor/bin/php-cs-fixer check --config=.php-cs-fixer.php -v

.PHONY: php-cs-fixer-fix
php-cs-fixer-fix:
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php -v

.PHONY: phpcbf
phpcbf:
	vendor/bin/phpcbf --standard=phpcs.xml .github cli src tests

.PHONY: phpunit
phpunit: build
	vendor/bin/phpunit -c phpunit.xml

.PHONY: cli
cli: build
	@echo "Should result in f8046e40c403f1d0:"
	@php cli/calculate.php 'hello, world!'

.PHONY: composer
composer:
	# Psalm v5.26.1 doesn't like PHP-8.4
	composer install --ignore-platform-req=php+

.PHONY: clean
clean:
	rm -rf build
