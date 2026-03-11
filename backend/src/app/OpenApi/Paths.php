<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

class Paths
{
    #[OA\Post(
        path: '/api/register',
        tags: ['Auth'],
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/RegisterRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User registered successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/RegisterSuccessResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
        ]
    )]
    public function register(): void
    {
    }

    #[OA\Post(
        path: '/api/login',
        tags: ['Auth'],
        summary: 'Log in a user and create a Sanctum token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User logged in successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/AuthSuccessResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
        ]
    )]
    public function login(): void
    {
    }

    #[OA\Post(
        path: '/api/logout',
        tags: ['Auth'],
        summary: 'Log out the authenticated user',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User logged out successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(): void
    {
    }

    #[OA\Get(
        path: '/api/questions',
        tags: ['Questions'],
        summary: 'List questions',
        parameters: [
            new OA\Parameter(name: 'keyword', in: 'query', required: false, description: 'Filter questions by keyword', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'location', in: 'query', required: false, description: 'Filter questions by location', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Pagination page number', schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated question list',
                content: new OA\JsonContent(ref: '#/components/schemas/QuestionPaginatedResponse')
            ),
        ]
    )]
    public function listQuestions(): void
    {
    }

    #[OA\Post(
        path: '/api/questions',
        tags: ['Questions'],
        summary: 'Create a new question',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/QuestionRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Question created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/QuestionMutationResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
        ]
    )]
    public function createQuestion(): void
    {
    }

    #[OA\Get(
        path: '/api/questions/{question}',
        tags: ['Questions'],
        summary: 'Show a question with its responses',
        parameters: [
            new OA\Parameter(name: 'question', in: 'path', required: true, description: 'Question id', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Question details',
                content: new OA\JsonContent(ref: '#/components/schemas/QuestionResource')
            ),
            new OA\Response(response: 404, description: 'Question not found'),
        ]
    )]
    public function showQuestion(): void
    {
    }

    #[OA\Put(
        path: '/api/questions/{question}',
        tags: ['Questions'],
        summary: 'Update a question',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'question', in: 'path', required: true, description: 'Question id', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/QuestionRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Question updated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/QuestionMutationResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(
                response: 403,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
        ]
    )]
    public function updateQuestion(): void
    {
    }

    #[OA\Delete(
        path: '/api/questions/{question}',
        tags: ['Questions'],
        summary: 'Delete a question',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'question', in: 'path', required: true, description: 'Question id', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Question deleted successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(
                response: 403,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
    public function deleteQuestion(): void
    {
    }

    #[OA\Post(
        path: '/api/questions/{question}/responses',
        tags: ['Responses'],
        summary: 'Create a response for a question',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'question', in: 'path', required: true, description: 'Question id', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ResponseRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Response created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/ResponseMutationResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
        ]
    )]
    public function createResponse(): void
    {
    }

    #[OA\Put(
        path: '/api/responses/{response}',
        tags: ['Responses'],
        summary: 'Update a response',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'response', in: 'path', required: true, description: 'Response id', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ResponseRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Response updated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/ResponseMutationResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(
                response: 403,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
        ]
    )]
    public function updateResponse(): void
    {
    }

    #[OA\Delete(
        path: '/api/responses/{response}',
        tags: ['Responses'],
        summary: 'Delete a response',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'response', in: 'path', required: true, description: 'Response id', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Response deleted successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(
                response: 403,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
            ),
        ]
    )]
    public function deleteResponse(): void
    {
    }

    #[OA\Get(
        path: '/api/favorites',
        tags: ['Favorites'],
        summary: "List the authenticated user's favorite questions",
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Pagination page number', schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated favorite questions',
                content: new OA\JsonContent(ref: '#/components/schemas/QuestionPaginatedResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function listFavorites(): void
    {
    }

    #[OA\Post(
        path: '/api/questions/{question}/favorite',
        tags: ['Favorites'],
        summary: 'Toggle favorite status for a question',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'question', in: 'path', required: true, description: 'Question id', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Favorite status toggled',
                content: new OA\JsonContent(ref: '#/components/schemas/FavoriteToggleResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function toggleFavorite(): void
    {
    }

    #[OA\Get(
        path: '/api/profile',
        tags: ['Profile'],
        summary: "Get the authenticated user's profile",
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authenticated profile data',
                content: new OA\JsonContent(ref: '#/components/schemas/ProfileResource')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function showProfile(): void
    {
    }

    #[OA\Get(
        path: '/api/profile/questions',
        tags: ['Profile'],
        summary: 'List questions created by the authenticated user',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Pagination page number', schema: new OA\Schema(type: 'integer', minimum: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated user question list',
                content: new OA\JsonContent(ref: '#/components/schemas/QuestionPaginatedResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function listProfileQuestions(): void
    {
    }
}
