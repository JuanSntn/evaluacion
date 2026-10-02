<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class JsonPlaceholderPostService
{
    public function all(): array
    {
        $response = $this->client()->get('/posts')->throw();

        return $response->json();
    }

    public function create(array $data): array
    {
        $response = $this->client()->post('/posts', $data);
        if ($response->status() !== 201) {
            $response->throw();
            throw new RuntimeException('JSONPlaceholder no respondió con código 201 al crear la publicación.');
        }

        return $response->json();
    }

    public function update(int $postId, array $data): array
    {
        $response = $this->client()->put("/posts/{$postId}", $data)->throw();

        return $response->json();
    }

    public function delete(int $postId): void
    {
        $this->client()->delete("/posts/{$postId}")->throw();
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('jsonplaceholder.base_url'), '/'))->acceptJson()->asJson()->timeout(10);
    }
}
