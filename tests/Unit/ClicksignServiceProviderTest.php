<?php

use Clicksign\ClicksignServiceProvider;
use Clicksign\Contracts\ClicksignClientInterface;
use Illuminate\Contracts\Support\DeferrableProvider;

describe('ClicksignServiceProvider', function () {
    it('implements DeferrableProvider', function () {
        // Create a mock app to pass to the constructor
        $app = new class
        {
            public function offsetGet($key)
            {
                return null;
            }

            public function offsetSet($key, $value) {}

            public function offsetExists($key)
            {
                return false;
            }

            public function offsetUnset($key) {}
        };

        $provider = new ClicksignServiceProvider($app);

        expect($provider)->toBeInstanceOf(DeferrableProvider::class);
    });

    it('provides correct services', function () {
        $app = new class
        {
            public function offsetGet($key)
            {
                return null;
            }

            public function offsetSet($key, $value) {}

            public function offsetExists($key)
            {
                return false;
            }

            public function offsetUnset($key) {}
        };

        $provider = new ClicksignServiceProvider($app);

        expect($provider->provides())->toBe([
            ClicksignClientInterface::class,
            'clicksign',
        ]);
    });

    it('extends ServiceProvider', function () {
        $reflection = new ReflectionClass(ClicksignServiceProvider::class);
        expect($reflection->isSubclassOf(\Illuminate\Support\ServiceProvider::class))->toBeTrue();
    });
});
