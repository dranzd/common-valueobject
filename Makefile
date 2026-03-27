# Common ValueObject - Makefile
#
# Provides convenient targets for development, testing, and quality checks.
# Docker must be running for all targets (use ./utils up).
#
# Usage: make <target>
#
# Structure:
#   test-*       PHPUnit tests (verify correctness)
#   quality      All quality checks (test + phpstan + cs-check)

.PHONY: help up down restart build rebuild status ps logs shell root-shell install update dump-autoload test test-unit test-integration test-coverage phpstan cs-check cs-fix quality

# ==================== Default ====================

help:
	@echo ""
	@echo "  \033[1m\033[32mCommon ValueObject\033[0m"
	@echo ""
	@echo "  \033[33mDocker:\033[0m"
	@echo "    \033[32mup\033[0m                            Start Docker containers"
	@echo "    \033[32mdown\033[0m                          Stop Docker containers"
	@echo "    \033[32mrestart\033[0m                       Restart Docker containers"
	@echo "    \033[32mbuild\033[0m                         Build Docker containers"
	@echo "    \033[32mrebuild\033[0m                       Rebuild Docker containers from scratch"
	@echo "    \033[32mstatus\033[0m                        Check if Docker containers are running"
	@echo "    \033[32mps\033[0m                            Show container status"
	@echo "    \033[32mlogs\033[0m                          Tail container logs"
	@echo "    \033[32mshell\033[0m                         Open shell in PHP container"
	@echo "    \033[32mroot-shell\033[0m                    Open root shell in PHP container"
	@echo ""
	@echo "  \033[33mComposer:\033[0m"
	@echo "    \033[32minstall\033[0m                       Run composer install"
	@echo "    \033[32mupdate\033[0m                        Run composer update"
	@echo "    \033[32mdump-autoload\033[0m                 Run composer dump-autoload"
	@echo ""
	@echo "  \033[33mTest (PHPUnit):\033[0m"
	@echo "    \033[32mtest\033[0m                          Run all tests"
	@echo "    \033[32mtest-unit\033[0m                     Run unit tests only"
	@echo "    \033[32mtest-integration\033[0m              Run integration tests only"
	@echo "    \033[32mtest-coverage\033[0m                 Run tests with coverage report"
	@echo ""
	@echo "  \033[33mQuality:\033[0m"
	@echo "    \033[32mphpstan\033[0m                       Run PHPStan static analysis"
	@echo "    \033[32mcs-check\033[0m                      Run code style check"
	@echo "    \033[32mcs-fix\033[0m                        Fix code style"
	@echo "    \033[32mquality\033[0m                       Run all quality checks"
	@echo ""

# ==================== Docker ====================

up: ## Start containers
	./utils up

down: ## Stop containers
	./utils down

restart: ## Restart containers
	./utils restart

build: ## Build containers
	./utils build

rebuild: ## Rebuild containers from scratch
	./utils rebuild

status: ## Check if Docker containers are running
	./utils ps

ps: ## Show container status
	./utils ps

logs: ## Tail container logs
	./utils logs

shell: ## Open shell in PHP container
	./utils shell

root-shell: ## Open root shell in PHP container
	./utils root-shell

# ==================== Composer ====================

install: ## Run composer install
	./utils install

update: ## Run composer update
	./utils update

dump-autoload: ## Run composer dump-autoload
	./utils dump-autoload

# ==================== Test (PHPUnit) ====================

test: ## Run PHPUnit tests (usage: make test f=MyTest)
	./utils exec composer test $(if $(f),-- --filter=$(f),)

test-unit: ## Run unit tests only
	./utils exec composer test:unit

test-integration: ## Run integration tests only
	./utils exec composer test:integration

test-coverage: ## Run tests with coverage report
	./utils exec composer test:coverage

# ==================== Quality ====================

phpstan: ## Run PHPStan static analysis
	./utils phpstan

cs-check: ## Check code style
	./utils cs-check

cs-fix: ## Fix code style
	./utils cs-fix

quality: ## Run all quality checks (test, phpstan, cs-check)
	./utils quality
