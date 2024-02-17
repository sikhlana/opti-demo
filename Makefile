build:
	docker compose --env-file=./application/.env.test build --pull

run:
	docker compose --env-file=./application/.env.test up
