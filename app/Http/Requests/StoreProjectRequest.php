<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi formulir pengajuan proposal ide proyek Agentic AI.
 */
class StoreProjectRequest extends FormRequest
{
    /**
     * Pilihan provider/framework agen yang diterima.
     */
    public const TEMA_AGENT = ['Ollama', 'OpenAI', 'Claude', 'Gemini', 'LangChain', 'CrewAI'];

    /**
     * Hanya user yang login yang boleh mengajukan proposal (route juga dilindungi middleware auth).
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Aturan validasi. user_id sengaja tidak diterima dari request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'tema_agent' => ['required', Rule::in(self::TEMA_AGENT)],
            'api_key_secure' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min karakter.',
            'in' => ':attribute tidak valid.',
        ];
    }

    /**
     * Nama atribut yang ramah pengguna.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'judul' => 'Judul ide',
            'deskripsi' => 'Deskripsi',
            'tema_agent' => 'Tema agent',
            'api_key_secure' => 'API key',
        ];
    }
}
