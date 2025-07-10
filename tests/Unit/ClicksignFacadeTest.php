<?php

use Clicksign\Contracts\ClicksignClientInterface;
use Clicksign\Facades\Clicksign;

describe('Clicksign Facade', function () {
    it('returns correct facade accessor', function () {
        $reflection = new ReflectionClass(Clicksign::class);
        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        expect($method->invoke(null))->toBe(ClicksignClientInterface::class);
    });

    it('extends Laravel Facade', function () {
        $reflection = new ReflectionClass(Clicksign::class);
        expect($reflection->isSubclassOf(\Illuminate\Support\Facades\Facade::class))->toBeTrue();
    });
});
