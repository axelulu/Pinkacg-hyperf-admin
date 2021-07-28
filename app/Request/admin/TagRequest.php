<?php

declare(strict_types=1);

namespace App\Request\admin;

use Hyperf\Validation\Request\FormRequest;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'label' => 'required|string|min:2|max:20',
            'value' => 'required|string|min:2|max:20',
            'status' => 'required|boolean',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
            'label:required' => '请输入标识！',
            'value:required' => '请输入值！',
            'status:required' => '请输入状态！',
        ];
    }
}
