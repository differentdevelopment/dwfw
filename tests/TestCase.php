<?php

namespace Different\Dwfw\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static $database_built = false;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh');
    }

}
