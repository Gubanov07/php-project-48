.PHONY: install lint test

COMPOSER = composer

install:
	$(COMPOSER) install

lint:
	$(COMPOSER) exec --verbose phpcs -- --standard=PSR12 src bin tests

lint-fix:
	$(COMPOSER) exec --verbose phpcbf -- --standard=PSR12 src bin tests

test:
	$(COMPOSER) exec --verbose phpunit tests

test-coverage:
	$(COMPOSER) exec --verbose phpunit tests -- --coverage-clover=coverage.xml
	