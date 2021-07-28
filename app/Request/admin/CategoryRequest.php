<?php

declare(strict_types=1);

namespace App\Request\admin;

use Hyperf\Validation\Request\FormRequest;

class CategoryRequest extends FormRequest
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
            'label' => 'required|string|max:15|min:2',
            'value' => 'regex:/^[\w-]*$/|max:25|min:2',
            'son' => 'required|integer',
            'icon' => 'required|string|max:15|min:2',
            'status' => 'required|boolean',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
            'label:required' => '请输入名称！',
            'value:required' => '请输入标识！',
            'son:required' => '请输入父菜单！',
            'icon:required' => '请输入icon！',
            'status:required' => '请输入状态！',
        ];
    }
}
