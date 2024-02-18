# Build all the necessary images to run the project...
build:
	docker compose --env-file=./application/.env.test build --pull

# Run the project along with all of the required applications...
run:
	docker compose --env-file=./application/.env.test up
