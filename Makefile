.PHONY: build
build: build-crc64nvme build-crc32isohdlc

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

.PHONY: build-directory
build-directory:
	@if [ ! -d "./build" ]; then mkdir build; fi

.PHONY: build-crc64nvme
build-crc64nvme: build-directory
	@cd build && (if [ ! -d "./crc64fast-nvme" ]; then git clone https://github.com/awesomized/crc64fast-nvme.git; fi || true)
	@cd build/crc64fast-nvme && git fetch && git checkout 1.1.0
	@cd build/crc64fast-nvme && cargo build --release

.PHONY: build-crc32isohdlc
build-crc32isohdlc: build-directory
	@cd build && (if [ ! -d "./crc32fast-lib-rust" ]; then git clone https://github.com/awesomized/crc32fast-lib-rust.git; fi || true)
	@cd build/crc32fast-lib-rust && git fetch && git checkout 1.0.0
	@cd build/crc32fast-lib-rust && cargo build --release

.PHONY: clean
clean:
	rm -rf build
