<?php

declare(strict_types=1);

namespace App\Request\admin;

use Hyperf\Validation\Request\FormRequest;

class RoleRequest extends FormRequest
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
            'id' => '',
            'name' => 'required',
            'description' => 'required',
            'status' => 'required',
            'rolePermission' => '',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
            'name.required' => '请输入名称！',
            'description.required'  => '请输入描述！',
            'status.required'  => '请输入状态！',
        ];
    }
}
