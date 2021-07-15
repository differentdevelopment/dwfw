# Different Web Framework

<p align="center"><img src="https://github.com/laravel/art/raw/master/logo-lockup/4%20PNG/3%20RGB/1%20Full%20Color/laravel-logolockup-rgb-red.png" width="400"></p>
<p align="center"><img src="https://camo.githubusercontent.com/dd59936bdc371e014ff70060166a9815386e189a/68747470733a2f2f6261636b7061636b666f726c61726176656c2e636f6d2f70726573656e746174696f6e2f696d672f6261636b7061636b2f6c6f676f732f6261636b7061636b5f6c6f676f5f636f6c6f722e706e67" width="400"></p>

## About DWFW (Different Web Framework)

DFWF is based on [Laravel](https://laravel.com/), with [Backpack](https://backpackforlaravel.com/).

## Contributing

Thank you for considering contributing to the DWFW! The contribution guide can be found in the [documentation](https://github.com/alitak/dwfw/).

## Security Vulnerabilities

If you discover a security vulnerability within DWFW, please send an e-mail to the Different Development support via [support@different.hu](mailto:support@different.hu). All security vulnerabilities will be promptly addressed.

## License

The DWFW is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

### Install

- laravel new my-project ([Laravel Installation](https://laravel.com/docs/8.x/installation))
- cd my-project
- composer config repositories.dwfw vcs git@github.com:differentdevelopment/dwfw.git
- composer require different/dwfw:dev-master
- set .env for database, mail
- php artisan backpack:install
- php artisan dwfw:install
- (optional) composer require backpack/generators --dev
- (optional) composer require laracasts/generators --dev
- (optional) composer require barryvdh/laravel-debugbar --dev

### Update

#### update to version 0.10.9

- composer update
- php artisan vendor:publish --provider="Different\Dwfw\DwfwServiceProvider" --tag=core.langs
