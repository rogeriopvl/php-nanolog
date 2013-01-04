test:
	./vendor/bin/phpunit --debug test/*.php

style:
	phpcs src/

.PHONY: test
