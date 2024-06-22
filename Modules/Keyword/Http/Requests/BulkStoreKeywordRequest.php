<?php

namespace Modules\Keyword\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Keyword\Entities\Keyword;

class BulkStoreKeywordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'keywords.*.type' => [
                'required',
                'string', Rule::in(['account', 'keyword']),
            ],
            'keywords.*.username' => [
                'required',
                'string', Rule::unique('keywords', 'name')->where(function ($query) {
                    return $query->where('type', '=', $this->input('keywords.*.type'));
                }),
            ],
            'keywords.*.source' => ['required', 'string', Rule::in(
                Keyword::SOURCE_FACEBOOK,
                Keyword::SOURCE_INSTAGRAM,
                Keyword::SOURCE_TIKTOK,
                Keyword::SOURCE_YOUTUBE,
                Keyword::SOURCE_TWITTER,
            )],
            'keywords.*.start_date' => 'required|date_format:d-m-Y',
            'keywords.*.end_date' => 'required|date_format:d-m-Y|after_or_equal:keywords.*.start_date',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'keywords.*.type.required' => 'The type field is required.',
            'keywords.*.username.required' => 'The username field is required.',
            'keywords.*.username.unique' => 'The username has already been taken.',
            'keywords.*.source.required' => 'The source field is required.',
            'keywords.*.source.in' => 'The source must be one of the following: facebook, instagram, tiktok, youtube, twitter.',
            'keywords.*.start_date.date_format' => 'The start date must be in the format d-m-y.',
            'keywords.*.end_date.date_format' => 'The end date must be in the format d-m-y.',
            'keywords.*.end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
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
