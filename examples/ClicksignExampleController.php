<?php

/**
 * Exemplo de Controller demonstrando o uso da API v3 do Clicksign
 * 
 * Este exemplo mostra como implementar o fluxo básico de assinatura
 * seguindo o script bash fornecido, mas adaptado para Laravel.
 */

use Clicksign\Support\ClicksignWorkflow;
use Clicksign\Facades\Clicksign;
use Clicksign\DTO\{Envelope, Document, Signer, Requirement};
use Illuminate\Http\JsonResponse;

class ClicksignExampleController
{
    protected ClicksignWorkflow $workflow;

    public function __construct()
    {
        $this->workflow = new ClicksignWorkflow(app(\Clicksign\Contracts\ClicksignClientInterface::class));
    }

    /**
     * Exemplo do fluxo básico completo seguindo o script bash fornecido
     */
    public function basicWorkflow(): JsonResponse
    {
        try {
            // 1. Criar Envelope
            $envelope = new Envelope(
                name: 'Envelope de Teste',
                locale: 'pt-BR',
                autoClose: true,
                remindInterval: 3,
                blockAfterRefusal: true
            );

            $envelopeResponse = Clicksign::createEnvelope($envelope->toArray());
            $envelopeId = $envelopeResponse['data']['id'];

            // 2. Criar Documento
            $contentBase64 = 'data:application/pdf;base64,JVBERi0xLj0K...'; // Seu PDF em base64
            $document = Document::fromFile('arquivo.pdf', $contentBase64);
            
            $documentResponse = Clicksign::createDocument($envelopeId, $document->toArray());
            $documentId = $documentResponse['data']['id'];

            // 3. Criar Signatário
            $signer = Signer::create(
                name: 'Signer Name',
                email: 'signer@example.com',
                hasDocumentation: true
            );

            $signerResponse = Clicksign::createSigner($envelopeId, $signer->toArray());
            $signerId = $signerResponse['data']['id'];

            // 4. Adicionar Requisito de Assinatura
            $signatureRequirement = Requirement::createSignatureRequirement(
                documentId: $documentId,
                signerId: $signerId,
                type: 'click'
            );

            $signatureReqResponse = Clicksign::createRequirement($envelopeId, $signatureRequirement->toArray());

            // 5. Adicionar Requisito de Autenticação
            $authRequirement = Requirement::createAuthRequirement(
                signerId: $signerId,
                type: 'email'
            );

            $authReqResponse = Clicksign::createRequirement($envelopeId, $authRequirement->toArray());

            // 6. Atualizar Envelope para "running"
            $runningEnvelope = new Envelope(
                id: $envelopeId,
                status: 'running'
            );

            $updateResponse = Clicksign::updateEnvelope($envelopeId, $runningEnvelope->toUpdateArray());

            // 7. Enviar Notificação
            $notificationResponse = Clicksign::sendNotification($envelopeId);

            return response()->json([
                'success' => true,
                'envelope_id' => $envelopeId,
                'document_id' => $documentId,
                'signer_id' => $signerId,
                'workflow_completed' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exemplo usando o workflow helper
     */
    public function workflowHelper(): JsonResponse
    {
        try {
            $signers = [
                [
                    'name' => 'João Silva',
                    'email' => 'joao@example.com',
                    'birthday' => '1990-01-01',
                    'has_documentation' => true
                ]
            ];

            // Simulando conteúdo de um PDF
            $pdfContent = base64_encode('PDF content here');

            $result = $this->workflow->createSignatureWorkflow(
                envelopeName: 'Contrato de Prestação de Serviços',
                filename: 'contrato.pdf',
                contentBase64: $pdfContent,
                signers: $signers,
                envelopeOptions: [
                    'locale' => 'pt-BR',
                    'auto_close' => true,
                    'remind_interval' => 3,
                    'deadline_at' => '2025-12-31T23:59:59.000-03:00'
                ]
            );

            $envelopeId = $result['envelope']['data']['id'];

            // Iniciar processo
            $this->workflow->startSignatureProcess($envelopeId);

            // Enviar notificação
            $this->workflow->sendNotification($envelopeId, 'Por favor, assine o documento.');

            return response()->json([
                'success' => true,
                'envelope_id' => $envelopeId,
                'message' => 'Processo de assinatura iniciado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exemplo com template
     */
    public function templateWorkflow(): JsonResponse
    {
        try {
            $signers = [
                [
                    'name' => 'Maria Santos',
                    'email' => 'maria@example.com',
                    'birthday' => '1985-05-15',
                    'has_documentation' => true
                ]
            ];

            $result = $this->workflow->createTemplateWorkflow(
                envelopeName: 'Contrato via Template',
                filename: 'contrato_preenchido.docx',
                templateId: 'your_template_id',
                templateData: [
                    'nome_cliente' => 'Maria Santos',
                    'valor_contrato' => 'R$ 5.000,00',
                    'data_inicio' => '01/01/2025',
                    'data_fim' => '31/12/2025'
                ],
                signers: $signers
            );

            $envelopeId = $result['envelope']['data']['id'];

            // Iniciar e notificar
            $this->workflow->startSignatureProcess($envelopeId);
            $this->workflow->sendNotification($envelopeId);

            return response()->json([
                'success' => true,
                'envelope_id' => $envelopeId,
                'message' => 'Contrato criado via template!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar status do envelope
     */
    public function checkStatus(string $envelopeId): JsonResponse
    {
        try {
            $status = $this->workflow->getEnvelopeStatus($envelopeId);

            return response()->json([
                'success' => true,
                'envelope' => $status['envelope']['data'],
                'signers' => $status['signers']['data'],
                'requirements' => $status['requirements']['data']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Operação em massa de requisitos
     */
    public function bulkRequirements(array $requestData): JsonResponse
    {
        try {
            $envelopeId = $requestData['envelope_id'];
            $oldRequirementId = $requestData['old_requirement_id'];
            $documentId = $requestData['document_id'];
            $signerId = $requestData['signer_id'];

            // Exemplo: Trocar autenticação de email para ICP Brasil
            $operations = [
                [
                    'op' => 'remove',
                    'ref' => [
                        'type' => 'requirements',
                        'id' => $oldRequirementId
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

            $result = $this->workflow->bulkUpdateRequirements($envelopeId, $operations);

            return response()->json([
                'success' => true,
                'result' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
