fetching changes:
    git fetch --all
    git reset origin/main --hard


database:
    php artisan migrate:fresh --seed


Docker:
    Start:
        docker compose up -d
    Stop:
        docker compose down
    Reset Database:
        docker compose exec app php artisan migrate:fresh --seed
    