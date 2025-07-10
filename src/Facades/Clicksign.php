<?php

namespace Clicksign\Facades;

use Clicksign\Contracts\ClicksignClientInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array createDocument(array $data)
 * @method static array getDocument(string $key)
 * @method static array listDocuments(array $filters = [])
 * @method static array addSigner(string $documentKey, array $signerData)
 * @method static array removeSigner(string $documentKey, string $signerKey)
 * @method static string getDownloadUrl(string $documentKey)
 * @method static array cancelDocument(string $documentKey)
 * @method static array resendNotification(string $documentKey, array $signerKeys = [])
 */
class Clicksign extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return ClicksignClientInterface::class;
    }
}
