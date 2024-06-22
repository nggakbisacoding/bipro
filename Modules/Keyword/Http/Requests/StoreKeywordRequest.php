<?php

namespace Modules\Keyword\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Keyword\Entities\Keyword;

class StoreKeywordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', 'string', Rule::in([
                Keyword::TYPE_ACCOUNT,
                Keyword::TYPE_KEYWORD,
            ])],
            'name' => 'sometimes|string',
            'status' => 'sometimes|boolean',
            'source' => ['required', 'string', Rule::in(
                Keyword::SOURCE_FACEBOOK,
                Keyword::SOURCE_INSTAGRAM,
                Keyword::SOURCE_TIKTOK,
                Keyword::SOURCE_YOUTUBE,
                Keyword::SOURCE_TWITTER,
            )],
            'date' => 'sometimes|array',
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
