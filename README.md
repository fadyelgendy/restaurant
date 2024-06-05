# Restaurant V0.1
Simple Restaurant App

## Setup
- Clone `git clone https://github.com/fadyelgendy/restaurant.git` into your machine.
- `cd ` into `restaurant` folder.
- Run `composer setup` and it will don the work for you. <b>NOTE</b>: if prompting to create the DB file, please select `Yes`.
- Run `php artisan serve` to start development server, or use any development server of your own.

## Background Jobs
- To run queue worker, `php artisan queue:work`, for emails dispathcing.

## Requirements
- As we're using sqlite as out DB driver, please make sure that `SQLite3` is installed in your machine, and `php-sqlite3` extension is installed and enabled.
- You have to set email server configs into `.env` in order to test the mail functionaliy. You can use any free provider like Mailtrap.

## Testing
- To Run Tests simply run `php artisan test`.

