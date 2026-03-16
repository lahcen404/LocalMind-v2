<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AuthUser',
    type: 'object',
    required: ['id', 'name', 'email', 'role'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Lahcen'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'lahcen@example.com'),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'member'], example: 'member'),
    ]
)]
#[OA\Schema(
    schema: 'AuthorSummary',
    type: 'object',
    required: ['id', 'name', 'initial'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Lahcen'),
        new OA\Property(property: 'initial', type: 'string', example: 'L'),
    ]
)]
#[OA\Schema(
    schema: 'QuestionMetrics',
    type: 'object',
    required: ['responses_count', 'favorites_count'],
    properties: [
        new OA\Property(property: 'responses_count', type: 'integer', example: 4),
        new OA\Property(property: 'favorites_count', type: 'integer', example: 9),
    ]
)]
#[OA\Schema(
    schema: 'ResponseResource',
    type: 'object',
    required: ['id', 'content', 'question_id', 'created_at', 'author', 'is_owner'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 12),
        new OA\Property(property: 'content', type: 'string', example: 'You can find a pharmacy near the train station.'),
        new OA\Property(property: 'question_id', type: 'integer', example: 5),
        new OA\Property(property: 'created_at', type: 'string', example: '2 minutes ago'),
        new OA\Property(property: 'author', ref: '#/components/schemas/AuthorSummary'),
        new OA\Property(property: 'is_owner', type: 'boolean', example: true),
    ]
)]
#[OA\Schema(
    schema: 'QuestionResource',
    type: 'object',
    required: ['id', 'title', 'content', 'location', 'created_at', 'timestamp', 'author', 'metrics', 'responses'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 5),
        new OA\Property(property: 'title', type: 'string', example: 'Where can I find a late-night pharmacy?'),
        new OA\Property(property: 'content', type: 'string', example: 'I need a pharmacy open after 10pm in Casablanca.'),
        new OA\Property(property: 'location', type: 'string', example: 'Casablanca'),
        new OA\Property(property: 'created_at', type: 'string', example: '3 hours ago'),
        new OA\Property(property: 'timestamp', type: 'string', example: '10 Mar 2026 21:30'),
        new OA\Property(property: 'author', ref: '#/components/schemas/AuthorSummary'),
        new OA\Property(property: 'metrics', ref: '#/components/schemas/QuestionMetrics'),
        new OA\Property(
            property: 'responses',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/ResponseResource')
        ),
    ]
)]
#[OA\Schema(
    schema: 'ProfileStats',
    type: 'object',
    required: ['questions_count', 'responses_count', 'favorites_count'],
    properties: [
        new OA\Property(property: 'questions_count', type: 'integer', example: 7),
        new OA\Property(property: 'responses_count', type: 'integer', example: 16),
        new OA\Property(property: 'favorites_count', type: 'integer', example: 11),
    ]
)]
#[OA\Schema(
    schema: 'ProfileResource',
    type: 'object',
    required: ['id', 'name', 'email', 'role', 'initial', 'joined_at', 'stats', 'recent_questions'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Lahcen'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'lahcen@example.com'),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'member'], example: 'member'),
        new OA\Property(property: 'initial', type: 'string', example: 'L'),
        new OA\Property(property: 'joined_at', type: 'string', example: 'Mar 2026'),
        new OA\Property(property: 'stats', ref: '#/components/schemas/ProfileStats'),
        new OA\Property(
            property: 'recent_questions',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/QuestionResource')
        ),
    ]
)]
#[OA\Schema(
    schema: 'AuthSuccessResponse',
    type: 'object',
    required: ['token', 'user'],
    properties: [
        new OA\Property(property: 'token', type: 'string', example: '1|sanctum-token-example'),
        new OA\Property(property: 'user', ref: '#/components/schemas/AuthUser'),
    ]
)]
#[OA\Schema(
    schema: 'RegisterSuccessResponse',
    type: 'object',
    required: ['message', 'token', 'user'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'User registered successfully!!'),
        new OA\Property(property: 'token', type: 'string', example: '1|sanctum-token-example'),
        new OA\Property(property: 'user', ref: '#/components/schemas/AuthUser'),
    ]
)]
#[OA\Schema(
    schema: 'QuestionMutationResponse',
    type: 'object',
    required: ['message', 'data'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Question created successfully!'),
        new OA\Property(property: 'data', ref: '#/components/schemas/QuestionResource'),
    ]
)]
#[OA\Schema(
    schema: 'ResponseMutationResponse',
    type: 'object',
    required: ['message', 'data'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Intelligence contribution recorded.'),
        new OA\Property(property: 'data', ref: '#/components/schemas/ResponseResource'),
    ]
)]
#[OA\Schema(
    schema: 'FavoriteToggleResponse',
    type: 'object',
    required: ['message', 'is_favorited', 'favorites_count'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Question added to favorites !'),
        new OA\Property(property: 'is_favorited', type: 'boolean', example: true),
        new OA\Property(property: 'favorites_count', type: 'integer', example: 10),
    ]
)]
#[OA\Schema(
    schema: 'MessageResponse',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Operation completed successfully.'),
    ]
)]
#[OA\Schema(
    schema: 'ValidationErrorResponse',
    type: 'object',
    required: ['message', 'errors'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string')
            )
        ),
    ]
)]
#[OA\Schema(
    schema: 'UnauthorizedResponse',
    type: 'object',
    required: ['message'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
    ]
)]
#[OA\Schema(
    schema: 'RegisterRequest',
    type: 'object',
    required: ['name', 'email', 'password', 'device_name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', minLength: 3, maxLength: 255, example: 'Lahcen'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'lahcen@example.com'),
        new OA\Property(property: 'password', type: 'string', minLength: 8, example: 'secret123'),
        new OA\Property(property: 'device_name', type: 'string', example: 'chrome'),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'member'], nullable: true, example: 'member'),
    ]
)]
#[OA\Schema(
    schema: 'LoginRequest',
    type: 'object',
    required: ['email', 'password', 'device_name'],
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'lahcen@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'secret123'),
        new OA\Property(property: 'device_name', type: 'string', example: 'chrome'),
    ]
)]
#[OA\Schema(
    schema: 'QuestionRequest',
    type: 'object',
    required: ['title', 'content', 'location'],
    properties: [
        new OA\Property(property: 'title', type: 'string', minLength: 5, example: 'Where can I repair my bike?'),
        new OA\Property(property: 'content', type: 'string', minLength: 10, example: 'Looking for a trusted bike repair shop near Maarif.'),
        new OA\Property(property: 'location', type: 'string', example: 'Casablanca'),
    ]
)]
#[OA\Schema(
    schema: 'ResponseRequest',
    type: 'object',
    required: ['content'],
    properties: [
        new OA\Property(property: 'content', type: 'string', minLength: 5, maxLength: 2000, example: 'There is one next to the central market.'),
    ]
)]
#[OA\Schema(
    schema: 'PaginationLinks',
    type: 'object',
    properties: [
        new OA\Property(property: 'first', type: 'string', nullable: true, example: 'http://localhost:8080/api/questions?page=1'),
        new OA\Property(property: 'last', type: 'string', nullable: true, example: 'http://localhost:8080/api/questions?page=2'),
        new OA\Property(property: 'prev', type: 'string', nullable: true),
        new OA\Property(property: 'next', type: 'string', nullable: true, example: 'http://localhost:8080/api/questions?page=2'),
    ]
)]
#[OA\Schema(
    schema: 'PaginationMetaLink',
    type: 'object',
    required: ['url', 'label', 'active'],
    properties: [
        new OA\Property(property: 'url', type: 'string', nullable: true, example: 'http://localhost:8080/api/questions?page=1'),
        new OA\Property(property: 'label', type: 'string', example: '1'),
        new OA\Property(property: 'active', type: 'boolean', example: true),
    ]
)]
#[OA\Schema(
    schema: 'PaginationMeta',
    type: 'object',
    required: ['current_page', 'from', 'last_page', 'links', 'path', 'per_page', 'to', 'total'],
    properties: [
        new OA\Property(property: 'current_page', type: 'integer', example: 1),
        new OA\Property(property: 'from', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'last_page', type: 'integer', example: 3),
        new OA\Property(
            property: 'links',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/PaginationMetaLink')
        ),
        new OA\Property(property: 'path', type: 'string', example: 'http://localhost:8080/api/questions'),
        new OA\Property(property: 'per_page', type: 'integer', example: 15),
        new OA\Property(property: 'to', type: 'integer', nullable: true, example: 15),
        new OA\Property(property: 'total', type: 'integer', example: 31),
    ]
)]
#[OA\Schema(
    schema: 'QuestionPaginatedResponse',
    type: 'object',
    required: ['data', 'links', 'meta'],
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/QuestionResource')
        ),
        new OA\Property(property: 'links', ref: '#/components/schemas/PaginationLinks'),
        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
    ]
)]
class Schemas
{
}
