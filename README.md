# Clicksign Laravel SDK v3

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ferreiramg/clicksign.svg?style=flat-square)](https://packagist.org/packages/ferreiramg/clicksign)
[![Tests](https://img.shields.io/github/actions/workflow/status/Ferreiramg/clicksign/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/Ferreiramg/clicksign/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ferreiramg/clicksign.svg?style=flat-square)](https://packagist.org/packages/ferreiramg/clicksign)

SDK para integração com a **API v3** do Clicksign em aplicações Laravel. Este pacote foi completamente atualizado para trabalhar com a nova arquitetura baseada em envelopes da API v3.

## Requisitos

- PHP 8.2 ou superior
- Laravel 12.x

## Instalação

Você pode instalar o pacote via composer:

```bash
composer require ferreiramg/clicksign
```

## Configuração

Publique o arquivo de configuração:

```bash
php artisan vendor:publish --tag="clicksign-config"
```

Configure suas variáveis de ambiente no arquivo `.env`:

```env
CLICKSIGN_ACCESS_TOKEN=your_access_token_here
CLICKSIGN_BASE_URL=https://app.clicksign.com/api/v3
CLICKSIGN_SANDBOX=false
CLICKSIGN_SANDBOX_URL=https://sandbox.clicksign.com/api/v3
CLICKSIGN_WEBHOOK_SECRET=your_webhook_secret_here
```

## Fluxo Básico de Assinatura

A API v3 do Clicksign segue um fluxo baseado em **envelopes**:

1. **Envelope**: Container que agrupa documentos, signatários e requisitos
2. **Documento**: O arquivo que será assinado
3. **Signatário**: Pessoa que irá assinar o documento
4. **Requisitos**: Regras de assinatura e autenticação
5. **Ativação**: Colocar o envelope em execução
6. **Notificação**: Enviar notificações aos signatários

## Uso com Workflow Helper

### Fluxo Completo Simplificado

```php
use Clicksign\Support\ClicksignWorkflow;
use Clicksign\Facades\Clicksign;

// Instanciar o workflow helper
$workflow = new ClicksignWorkflow(app(ClicksignClientInterface::class));

// Dados do signatário
$signers = [
    [
        'name' => 'João Silva',
        'email' => 'joao@example.com',
        'birthday' => '1990-01-01',
        'has_documentation' => true
    ]
];

// Criar workflow completo
$result = $workflow->createSignatureWorkflow(
    envelopeName: 'Contrato de Prestação de Serviços',
    filename: 'contrato.pdf',
    contentBase64: base64_encode(file_get_contents('path/to/contrato.pdf')),
    signers: $signers,
    envelopeOptions: [
        'locale' => 'pt-BR',
        'auto_close' => true,
        'remind_interval' => 3,
        'deadline_at' => '2025-12-31T23:59:59.000-03:00'
    ]
);

$envelopeId = $result['envelope']['data']['id'];

// Ativar o processo de assinatura
$workflow->startSignatureProcess($envelopeId);

// Enviar notificação
$workflow->sendNotification($envelopeId, 'Por favor, assine o documento.');
```

### Fluxo com Template

```php
// Criar workflow usando template
$result = $workflow->createTemplateWorkflow(
    envelopeName: 'Contrato Personalizado',
    filename: 'contrato_preenchido.docx',
    templateId: 'template_123',
    templateData: [
        'nome_cliente' => 'João Silva',
        'valor_contrato' => 'R$ 5.000,00',
        'data_vencimento' => '31/12/2025'
    ],
    signers: $signers
);
```

## Uso Direto da API

### Criando um Envelope

```php
use Clicksign\Facades\Clicksign;
use Clicksign\DTO\Envelope;

$envelope = new Envelope(
    name: 'Meu Envelope',
    locale: 'pt-BR',
    autoClose: true,
    remindInterval: 3,
    blockAfterRefusal: true,
    deadlineAt: '2025-12-31T23:59:59.000-03:00'
);

$response = Clicksign::createEnvelope($envelope->toArray());
$envelopeId = $response['data']['id'];
```

### Adicionando um Documento

```php
use Clicksign\DTO\Document;

// Documento a partir de arquivo
$document = Document::fromFile(
    filename: 'contrato.pdf',
    contentBase64: base64_encode(file_get_contents('path/to/arquivo.pdf'))
);

$response = Clicksign::createDocument($envelopeId, $document->toArray());
$documentId = $response['data']['id'];

// Documento a partir de template
$document = Document::fromTemplate(
    filename: 'contrato_preenchido.docx',
    templateId: 'template_123',
    templateData: [
        'nome' => 'João Silva',
        'valor' => 'R$ 1.000,00'
    ]
);
```

### Adicionando Signatários

```php
use Clicksign\DTO\Signer;

$signer = Signer::create(
    name: 'João Silva',
    email: 'joao@example.com',
    birthday: '1990-01-01',
    hasDocumentation: true
);

$response = Clicksign::createSigner($envelopeId, $signer->toArray());
$signerId = $response['data']['id'];
```

### Adicionando Requisitos

```php
use Clicksign\DTO\Requirement;

// Requisito de assinatura
$signatureReq = Requirement::createSignatureRequirement(
    documentId: $documentId,
    signerId: $signerId,
    role: 'sign'
);

Clicksign::createRequirement($envelopeId, $signatureReq->toArray());

// Requisito de autenticação
$authReq = Requirement::createAuthRequirement(
    documentId: $documentId,
    signerId: $signerId,
    auth: 'email'
);

Clicksign::createRequirement($envelopeId, $authReq->toArray());
```

### Ativando o Envelope

```php
use Clicksign\DTO\Envelope;

$envelope = new Envelope(
    id: $envelopeId,
    status: 'running'
);

Clicksign::updateEnvelope($envelopeId, $envelope->toUpdateArray());
```

### Enviando Notificações

```php
Clicksign::sendNotification($envelopeId, [
    'type' => 'notifications',
    'attributes' => [
        'message' => 'Por favor, assine o documento urgentemente.'
    ]
]);
```

## Operações em Massa

### Atualizações em Lote de Requisitos

```php
$operations = [
    [
        'op' => 'remove',
        'ref' => [
            'type' => 'requirements',
            'id' => 'requirement_123'
        ]
    ],
    [
        'op' => 'add',
        'data' => [
            'type' => 'requirements',
            'attributes' => [
                'action' => 'provide_evidence',
                'auth' => 'icp_brasil'
            ],
            'relationships' => [
                'document' => [
                    'data' => ['type' => 'documents', 'id' => $documentId]
                ],
                'signer' => [
                    'data' => ['type' => 'signers', 'id' => $signerId]
                ]
            ]
        ]
    ]
];

Clicksign::bulkRequirements($envelopeId, ['atomic:operations' => $operations]);
```

## Templates

### Criando um Template

```php
use Clicksign\DTO\Template;

$template = new Template(
    name: 'Contrato Padrão',
    contentBase64: base64_encode(file_get_contents('template.docx')),
    color: '#577b8d'
);

$response = Clicksign::createTemplate($template->toArray());
```

## Status e Monitoramento

### Verificando Status do Envelope

```php
$status = $workflow->getEnvelopeStatus($envelopeId);

echo "Status do envelope: " . $status['envelope']['data']['attributes']['status'];
echo "Signatários: " . count($status['signers']['data']);
echo "Requisitos: " . count($status['requirements']['data']);
```

### Eventos

```php
// Eventos de um documento específico
$documentEvents = Clicksign::getDocumentEvents($envelopeId, $documentId);

// Eventos de todos os documentos do envelope
$envelopeEvents = Clicksign::getEnvelopeEvents($envelopeId);
```

## Modo Sandbox

Para testes, configure o modo sandbox:

```env
CLICKSIGN_SANDBOX=true
```

Ou use diretamente:

```php
$client = new ClicksignClient(
    accessToken: 'your_token',
    baseUrl: 'https://sandbox.clicksign.com/api/v3'
);
```

## Tratamento de Erros

```php
use Clicksign\Exceptions\{
    AuthenticationException,
    DocumentNotFoundException,
    ValidationException,
    ClicksignException
};

try {
    $response = Clicksign::createEnvelope($envelope->toArray());
} catch (AuthenticationException $e) {
    // Token inválido
} catch (ValidationException $e) {
    // Dados inválidos
    $errors = $e->getErrors();
} catch (DocumentNotFoundException $e) {
    // Documento não encontrado
} catch (ClicksignException $e) {
    // Outros erros da API
}
```

## Contribuindo

Por favor, veja [CONTRIBUTING](CONTRIBUTING.md) para detalhes.

## Segurança

Se você descobrir alguma vulnerabilidade de segurança, por favor envie um e-mail para luis@lpdeveloper.com.br ao invés de usar o issue tracker.

## Créditos

- [Ferreiramg](https://github.com/Ferreiramg)
- [Todos os Contribuidores](../../contributors)

## Licença

A licença MIT (MIT). Por favor veja [License File](LICENSE.md) para mais informações.
