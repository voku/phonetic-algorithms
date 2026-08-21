AGENT_LOOP_BIN := bin/agent-loop

-include tools/agent-loop/vendor/voku/agent-loop/make/agent-loop.mk

.PHONY: agent_loop_install ## install the isolated agent-loop tool project
agent_loop_install:
	composer install --working-dir=tools/agent-loop

.PHONY: test ## run the phpunit test-suite
test:
	php vendor/bin/phpunit -c phpunit.xml

.PHONY: phpstan ## run the static analysis
phpstan:
	php tools/agent-loop/vendor/bin/phpstan analyse -c phpstan.neon
