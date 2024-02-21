dc = docker compose --env-file=./application/.env.test

# Build all the necessary images to run the project...
build:
	$(dc) build --pull

# Run the project along with all of the required applications...
run:
	$(dc) up
