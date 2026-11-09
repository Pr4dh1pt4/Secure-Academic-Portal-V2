<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\StoreProjectRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi formulir proposal di panel admin (tambah dan ubah).
 *
 * Bedanya dengan formulir mahasiswa: admin/staf memilih mahasiswa pemilik saat menambah,
 * dan API key boleh dikosongkan saat mengubah (artinya key lama dipertahankan).
 */
class ProposalRequest extends FormRequest
{
    /**
     * Hak akses sudah dicek middleware permission: di controller; di sini dicek ulang server-side.
     */
    public function authorize(): bool
    {
        return $this->user()->can($this->isMethod('post') ? 'proposal.tambah' : 'proposal.ubah');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tambah = $this->isMethod('post');

        return [
            // Pemilik hanya dipilih saat menambah, dan harus akun mahasiswa.
            'user_id' => $tambah
                ? ['required', 'integer', Rule::exists(User::class, 'id')->where(fn ($q) => $q->where('email', 'like', '%@student.its.ac.id'))]
                : ['prohibited'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'tema_agent' => ['required', Rule::in(StoreProjectRequest::TEMA_AGENT)],
            'api_key_secure' => [$tambah ? 'required' : 'nullable', 'string', 'min:8', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min karakter.',
            'in' => ':attribute tidak valid.',
            'exists' => ':attribute tidak ditemukan.',
            'prohibited' => ':attribute tidak boleh diubah.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'Mahasiswa pemilik',
            'judul' => 'Judul proposal',
            'deskripsi' => 'Deskripsi',
            'tema_agent' => 'Tema agent',
            'api_key_secure' => 'API key',
        ];
    }
}
