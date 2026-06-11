<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\URL;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // ForceHttp redirige a https fuera de env local; en testing emulamos https
        // para que las requests lleguen al controlador en vez de un 302.
        URL::forceScheme('https');
    }
}
