<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory pesan obrolan antara user dan agent AI keamanan.
 *
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $giliran = fake()->randomElement(fake()->randomElement(ChatSessionFactory::SKENARIO)['giliran']);

        return [
            'chat_session_id' => ChatSession::factory(),
            'role' => 'user',
            'content' => $giliran['tanya'],
            // Perkiraan kasar: satu token kira-kira 4 karakter.
            'token_count' => fn (array $atribut) => (int) ceil(mb_strlen($atribut['content']) / 4),
        ];
    }

    /**
     * Pesan dari user.
     */
    public function fromUser(?string $content = null): static
    {
        return $this->state(fn () => array_filter([
            'role' => 'user',
            'content' => $content,
        ]));
    }

    /**
     * Pesan jawaban dari agent AI.
     */
    public function fromAssistant(?string $content = null): static
    {
        return $this->state(fn () => [
            'role' => 'assistant',
            'content' => $content
                ?? fake()->randomElement(fake()->randomElement(ChatSessionFactory::SKENARIO)['giliran'])['jawab'],
        ]);
    }
}
