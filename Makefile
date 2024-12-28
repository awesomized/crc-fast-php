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
	@echo "CRC-64/NVME should result in f8046e40c403f1d0:"
	@php cli/calculateCrc64Nvme.php 'hello, world!'
	@echo "CRC-32/ISO-HDLC should result in 58988d13:"
	@php cli/calculateCrc32IsoHdlc.php 'hello, world!'

.PHONY: composer
composer:
	# Psalm v5.26.1 doesn't like PHP-8.4
	composer install --ignore-platform-req=php+

.PHONY: build
build:
	@cd build && make

.PHONY: build-crc64nvme
build-crc64nvme:
	@cd build && make build-crc64nvme

.PHONY: build-crc32isohdlc
build-crc32isohdlc:
	@cd build && make build-crc32isohdlc

.PHONY: clean
clean:
	rm -rf vendor
	cd make && make clean
