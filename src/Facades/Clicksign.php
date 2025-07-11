<?php

namespace Clicksign\Facades;

use Clicksign\Contracts\ClicksignClientInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array createEnvelope(array $data)
 * @method static array getEnvelope(string $envelopeId)
 * @method static array updateEnvelope(string $envelopeId, array $data)
 * @method static array listEnvelopes(array $filters = [])
 * @method static array createDocument(string $envelopeId, array $data)
 * @method static array getDocument(string $envelopeId, string $documentId)
 * @method static array listDocuments(string $envelopeId)
 * @method static array createSigner(string $envelopeId, array $data)
 * @method static array getSigner(string $envelopeId, string $signerId)
 * @method static array listSigners(string $envelopeId)
 * @method static array updateSigner(string $envelopeId, string $signerId, array $data)
 * @method static array deleteSigner(string $envelopeId, string $signerId)
 * @method static array createRequirement(string $envelopeId, array $data)
 * @method static array getRequirement(string $envelopeId, string $requirementId)
 * @method static array listRequirements(string $envelopeId)
 * @method static array deleteRequirement(string $envelopeId, string $requirementId)
 * @method static array bulkRequirements(string $envelopeId, array $operations)
 * @method static array sendNotification(string $envelopeId, array $data = [])
 * @method static array createTemplate(array $data)
 * @method static array getTemplate(string $templateId)
 * @method static array listTemplates(array $filters = [])
 * @method static array updateTemplate(string $templateId, array $data)
 * @method static array deleteTemplate(string $templateId)
 * @method static array getDocumentEvents(string $envelopeId, string $documentId)
 * @method static array getEnvelopeEvents(string $envelopeId)
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
