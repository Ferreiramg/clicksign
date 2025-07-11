<?php

use Clicksign\DTO\Template;

it('can create template with basic data', function () {
    $template = new Template(
        name: 'Contract Template',
        description: 'Basic contract template'
    );

    expect($template->name)->toBe('Contract Template');
    expect($template->description)->toBe('Basic contract template');
});

it('can create template from array', function () {
    $data = [
        'type' => 'templates',
        'id' => 'template-123',
        'attributes' => [
            'name' => 'NDA Template',
            'description' => 'Non-disclosure agreement template',
            'created_at' => '2024-01-01T00:00:00Z',
            'updated_at' => '2024-01-01T12:00:00Z'
        ]
    ];

    $template = Template::fromArray($data);

    expect($template->id)->toBe('template-123');
    expect($template->name)->toBe('NDA Template');
    expect($template->description)->toBe('Non-disclosure agreement template');
    expect($template->createdAt)->toBe('2024-01-01T00:00:00Z');
    expect($template->updatedAt)->toBe('2024-01-01T12:00:00Z');
});

it('can convert template to array', function () {
    $template = new Template(
        name: 'Service Agreement',
        description: 'Standard service agreement template',
        content: 'Template content here...',
        metadata: ['category' => 'legal', 'version' => '1.0']
    );

    $array = $template->toArray();

    expect($array['type'])->toBe('templates');
    expect($array['attributes']['name'])->toBe('Service Agreement');
    expect($array['attributes']['description'])->toBe('Standard service agreement template');
    expect($array['attributes']['content'])->toBe('Template content here...');
    expect($array['attributes']['metadata'])->toBe(['category' => 'legal', 'version' => '1.0']);
});

it('can create template with metadata', function () {
    $template = new Template(
        name: 'Employee Contract',
        metadata: [
            'department' => 'HR',
            'category' => 'employment',
            'required_fields' => ['employee_name', 'start_date', 'salary']
        ]
    );

    expect($template->metadata)->toHaveKey('department');
    expect($template->metadata['department'])->toBe('HR');
    expect($template->metadata['required_fields'])->toContain('employee_name');
});

it('can handle template with null values', function () {
    $data = [
        'type' => 'templates',
        'id' => 'template-456',
        'attributes' => [
            'name' => 'Simple Template',
            'description' => null,
            'content' => null
        ]
    ];

    $template = Template::fromArray($data);

    expect($template->name)->toBe('Simple Template');
    expect($template->description)->toBeNull();
    expect($template->content)->toBeNull();
});

it('can create template with content', function () {
    $content = 'This is a contract between {{company_name}} and {{client_name}}...';
    
    $template = new Template(
        name: 'Dynamic Contract',
        content: $content
    );

    expect($template->content)->toBe($content);
    expect($template->content)->toContain('{{company_name}}');
});

it('can validate template has required fields', function () {
    $template = new Template(
        name: 'Test Template',
        metadata: ['required_fields' => ['name', 'email']]
    );

    expect($template->hasRequiredField('name'))->toBeTrue();
    expect($template->hasRequiredField('email'))->toBeTrue();
    expect($template->hasRequiredField('phone'))->toBeFalse();
});

it('can get template variables from content', function () {
    $template = new Template(
        name: 'Variable Template',
        content: 'Hello {{name}}, your order {{order_id}} is ready!'
    );

    $variables = $template->getVariables();
    
    expect($variables)->toContain('name');
    expect($variables)->toContain('order_id');
    expect($variables)->toHaveCount(2);
});

it('can check if template is active', function () {
    $activeTemplate = Template::fromArray([
        'type' => 'templates',
        'id' => 'template-1',
        'attributes' => [
            'name' => 'Active Template',
            'status' => 'active'
        ]
    ]);

    $inactiveTemplate = Template::fromArray([
        'type' => 'templates',
        'id' => 'template-2',
        'attributes' => [
            'name' => 'Inactive Template',
            'status' => 'inactive'
        ]
    ]);

    expect($activeTemplate->isActive())->toBeTrue();
    expect($inactiveTemplate->isActive())->toBeFalse();
});

it('can create template with version info', function () {
    $template = new Template(
        name: 'Versioned Template',
        version: '2.1.0',
        metadata: ['changelog' => 'Added new clauses']
    );

    expect($template->version)->toBe('2.1.0');
    expect($template->metadata['changelog'])->toBe('Added new clauses');
});
