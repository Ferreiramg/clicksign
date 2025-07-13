<?php

namespace Clicksign\DTO;

class SignatureRequest
{
    public function __construct(
        public readonly string $documentPath,
        public readonly string $filename,
        public readonly array $signers = [],
        public readonly bool $skipEmail = false,
        public readonly bool $ordered = false,
        public readonly ?string $message = null
    ) {}

    public static function create(string $documentPath, string $filename): self
    {
        return new self($documentPath, $filename);
    }

    public function addSigner(
        string $email,
        string $name,
        ?string $documentationNumber = null,
        ?string $birthday = null,
        ?string $phoneNumber = null,
        string $deliveryMethod = 'email',
        string $authenticationMethod = 'email'
    ): self {
        $signers = $this->signers;
        $signers[] = [
            'email' => $email,
            'name' => $name,
            'documentation' => $documentationNumber,
            'birthday' => $birthday,
            'phone_number' => $phoneNumber,
            'delivery' => $deliveryMethod,
            'authentication' => $authenticationMethod,
        ];

        return new self(
            $this->documentPath,
            $this->filename,
            $signers,
            $this->skipEmail,
            $this->ordered,
            $this->message
        );
    }

    public function withMessage(string $message): self
    {
        return new self(
            $this->documentPath,
            $this->filename,
            $this->signers,
            $this->skipEmail,
            $this->ordered,
            $message
        );
    }

    public function skipEmail(bool $skip = true): self
    {
        return new self(
            $this->documentPath,
            $this->filename,
            $this->signers,
            $skip,
            $this->ordered,
            $this->message
        );
    }

    public function ordered(bool $ordered = true): self
    {
        return new self(
            $this->documentPath,
            $this->filename,
            $this->signers,
            $this->skipEmail,
            $ordered,
            $this->message
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'document_path' => $this->documentPath,
            'filename' => $this->filename,
            'signers' => $this->signers,
            'skip_email' => $this->skipEmail,
            'ordered' => $this->ordered,
            'message' => $this->message,
        ], fn ($value) => $value !== null && $value !== false && $value !== []);
    }
}
