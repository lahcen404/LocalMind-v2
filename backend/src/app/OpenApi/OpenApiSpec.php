<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'LocalMind API',
    version: '1.0.0',
    description: 'OpenAPI documentation for the LocalMind backend.'
)]
#[OA\Server(
    url: '/',
    description: 'Docker-served LocalMind backend'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Use the bearer token returned by the register or login endpoint.'
)]
#[OA\Tag(
    name: 'Auth',
    description: 'Authentication endpoints'
)]
#[OA\Tag(
    name: 'Questions',
    description: 'Question discovery and management'
)]
#[OA\Tag(
    name: 'Responses',
    description: 'Responses posted on questions'
)]
#[OA\Tag(
    name: 'Favorites',
    description: 'Favorite question endpoints'
)]
#[OA\Tag(
    name: 'Profile',
    description: 'Authenticated user profile endpoints'
)]
class OpenApiSpec
{
}
