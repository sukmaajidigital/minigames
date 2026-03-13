<?php

namespace App\Http\Requests\Games;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGameSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'game_slug' => ['required', 'string', 'max:120', 'exists:games,slug'],
            'score' => ['required', 'integer', 'min:0', 'max:100000'],
            'duration_seconds' => ['nullable', 'integer', 'min:1', 'max:7200'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'game_slug.exists' => 'Game tidak ditemukan.',
            'score.max' => 'Skor melebihi batas yang diperbolehkan.',
        ];
    }
}
