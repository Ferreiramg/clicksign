# Clicksign Laravel SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ferreiramg/clicksign.svg?style=flat-square)](https://packagist.org/packages/ferreiramg/clicksign)
[![Tests](https://img.shields.io/github/actions/workflow/status/Ferreiramg/clicksign/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/Ferreiramg/clicksign/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ferreiramg/clicksign.svg?style=flat-square)](https://packagist.org/packages/ferreiramg/clicksign)

SDK para integração com a API do Clicksign em aplicações Laravel.

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
CLICKSIGN_BASE_URL=https://app.clicksign.com/api/v1
CLICKSIGN_WEBHOOK_SECRET=your_webhook_secret_here
```

## Uso Básico

### Facade

```php
use Clicksign\Facades\Clicksign;

// Criar um documento
$document = Clicksign::createDocument([
    'filename' => 'contrato.pdf',
    'content' => base64_encode(file_get_contents('path/to/document.pdf'))
]);

// Adicionar um signatário
$signer = Clicksign::addSigner($document['key'], [
    'email' => 'joao@exemplo.com',
    'name' => 'João Silva',
    'documentation' => '12345678901'
]);

// Obter documento
$document = Clicksign::getDocument($documentKey);

// Listar documentos
$documents = Clicksign::listDocuments();
```

### Injeção de Dependência

```php
use Clicksign\Contracts\ClicksignClientInterface;

class DocumentController extends Controller
{
    public function __construct(
        private ClicksignClientInterface $clicksign
    ) {}

    public function store(Request $request)
    {
        $document = $this->clicksign->createDocument([
            'filename' => $request->file('document')->getClientOriginalName(),
            'content' => base64_encode($request->file('document')->getContent())
        ]);

        return response()->json($document);
    }
}
```

### Usando DTOs

```php
use Clicksign\DTO\SignatureRequest;

// Construir uma solicitação de assinatura
$request = SignatureRequest::create('/path/to/document.pdf', 'contrato.pdf')
    ->addSigner('joao@exemplo.com', 'João Silva', '12345678901')
    ->addSigner('maria@exemplo.com', 'Maria Santos')
    ->withMessage('Por favor, assinem este contrato')
    ->ordered() // Assinatura sequencial
    ->skipEmail(); // Não enviar email automático

$document = Clicksign::createDocument($request->toArray());
```

## Testes

### Usando o Fake Client

Para testes, você pode usar o `ClicksignFake`:

```php
use Clicksign\Http\ClicksignFake;
use Clicksign\Contracts\ClicksignClientInterface;

// No seu teste
$this->app->bind(ClicksignClientInterface::class, ClicksignFake::class);

// Ou usando o Facade
Clicksign::fake();
```

### Executar os testes

```bash
composer test
```

### Com cobertura

```bash
composer test-coverage
```

## Formatação de Código

```bash
composer format
```

## Verificar formatação

```bash
composer format-check
```

## Recursos

- ✅ Criação e gerenciamento de documentos
- ✅ Adição e remoção de signatários
- ✅ Download de documentos
- ✅ Cancelamento de documentos
- ✅ Reenvio de notificações
- ✅ DTOs tipados
- ✅ Client fake para testes
- ✅ Verificação de assinaturas de webhook
- ✅ Facade Laravel
- ✅ Service Provider
- ✅ Configuração publicável

## Tratamento de Erros

O pacote inclui exceções específicas para diferentes cenários:

```php
use Clicksign\Exceptions\{
    ClicksignException,
    AuthenticationException,
    DocumentNotFoundException,
    ValidationException
};

try {
    $document = Clicksign::getDocument('documento-inexistente');
} catch (DocumentNotFoundException $e) {
    // Documento não encontrado
} catch (AuthenticationException $e) {
    // Erro de autenticação
} catch (ValidationException $e) {
    // Erro de validação
    $errors = $e->getErrors();
} catch (ClicksignException $e) {
    // Outros erros da API
}
```

## Verificação de Webhooks

```php
use Clicksign\Support\SignatureHash;

$data = $request->getContent();
$signature = $request->header('X-Clicksign-Signature');
$secret = config('clicksign.webhook_secret');

if (SignatureHash::verify($data, $signature, $secret)) {
    // Webhook válido
    $payload = json_decode($data, true);
    // Processar webhook...
} else {
    // Webhook inválido
    abort(400, 'Invalid webhook signature');
}
```

## Changelog

Por favor, veja [CHANGELOG](CHANGELOG.md) para mais informações sobre as mudanças recentes.

## Contribuindo

Por favor, veja [CONTRIBUTING](CONTRIBUTING.md) para detalhes.

## Segurança

Se você descobrir alguma vulnerabilidade de segurança, por favor envie um e-mail para luis@lpdeveloper.com.br.

## Créditos

- [Luís Ferreira](https://github.com/lpdev)
- [Todos os Contribuidores](../../contributors)

## Licença

A Licença MIT (MIT). Por favor, veja [License File](LICENSE.md) para mais informações.
