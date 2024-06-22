<?php

namespace Modules\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Keyword\Entities\Keyword;

class ExportPostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'nullable|string',
            'source' => ['sometimes', 'array', Rule::in(
                Keyword::SOURCE_FACEBOOK,
                Keyword::SOURCE_INSTAGRAM,
                Keyword::SOURCE_TIKTOK,
                Keyword::SOURCE_YOUTUBE,
                Keyword::SOURCE_TWITTER,
            )],
            'start_date' => 'nullable|string',
            'end_date' => 'nullable|string',
            'sentiment' => ['sometimes', 'array', Rule::in(
                'positive',
                'neutral',
                'negative'
            )],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
