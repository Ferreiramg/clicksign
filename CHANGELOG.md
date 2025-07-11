# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-01-10

### BREAKING CHANGES
- **Migração completa para API v3 do Clicksign**
- Arquitetura baseada em envelopes ao invés de documentos individuais
- Todas as interfaces e métodos foram redesenhados
- URLs da API mudaram de `/api/v1` para `/api/v3`
- Content-Type mudou para `application/vnd.api+json`

### Added
- Novos DTOs para API v3:
  - `Envelope`: Container principal para documentos e signatários
  - `Requirement`: Requisitos de assinatura e autenticação
  - `Template`: Suporte para templates de documentos
- Classe `ClicksignWorkflow` para simplificar fluxos complexos
- Suporte completo ao fluxo básico da API v3:
  1. Criar envelope
  2. Adicionar documento
  3. Adicionar signatários
  4. Configurar requisitos
  5. Ativar processo
  6. Enviar notificações
- Operações em massa para requisitos (`bulkRequirements`)
- Suporte para templates com substituição de variáveis
- Modo sandbox configurável via `.env`
- Novos métodos na interface:
  - Operações de envelope: `createEnvelope()`, `getEnvelope()`, `updateEnvelope()`, `listEnvelopes()`
  - Operações de documento: `createDocument()`, `getDocument()`, `listDocuments()`
  - Operações de signatário: `createSigner()`, `getSigner()`, `listSigners()`, `updateSigner()`, `deleteSigner()`
  - Operações de requisito: `createRequirement()`, `listRequirements()`, `deleteRequirement()`
  - Notificações: `sendNotification()`
  - Templates: `createTemplate()`, `getTemplate()`, `listTemplates()`
  - Eventos: `getDocumentEvents()`, `getEnvelopeEvents()`

### Changed
- DTOs `Document` e `Signer` redesenhados para API v3
- Configuração padrão atualizada para API v3
- Service Provider agora suporta modo sandbox
- Facade atualizada com novos métodos
- Tratamento de erro melhorado para formato JSON API

### Removed
- Métodos da API v1 removidos:
  - `addSigner()`, `removeSigner()`, `getDownloadUrl()`, `cancelDocument()`, `resendNotification()`
- DTOs antigos que não se aplicam à API v3

### Migration Guide
Para migrar da v1 para v3:

1. **Atualize a configuração**:
   ```env
   CLICKSIGN_BASE_URL=https://app.clicksign.com/api/v3
   CLICKSIGN_SANDBOX=true # para testes
   ```

2. **Use o novo fluxo baseado em envelopes**:
   ```php
   // Antes (v1)
   $document = Clicksign::createDocument($data);
   Clicksign::addSigner($document['key'], $signerData);
   
   // Agora (v3)
   $envelope = Clicksign::createEnvelope($envelopeData);
   $document = Clicksign::createDocument($envelope['id'], $documentData);
   $signer = Clicksign::createSigner($envelope['id'], $signerData);
   ```

3. **Use o ClicksignWorkflow para simplificar**:
   ```php
   $workflow = new ClicksignWorkflow(app(ClicksignClientInterface::class));
   $result = $workflow->createSignatureWorkflow(...);
   ```

## [1.0.0] - 2024-12-01

### Added
- Estrutura inicial do pacote para API v1
- Cliente HTTP para API do Clicksign v1
- DTOs para Document, Signer e SignatureRequest
- Exceções específicas para diferentes cenários
- Cliente fake para testes
- Utilitários para verificação de assinatura
- Service Provider e Facade para Laravel
- Arquivo de configuração
- Testes unitários e de integração com Pest
- Documentação completa

### Changed
- Nada ainda

### Deprecated
- Nada ainda

### Removed
- Nada ainda

### Fixed
- Nada ainda

### Security
- Implementação de verificação de assinatura para webhooks
