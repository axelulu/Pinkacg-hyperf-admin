<?php

declare(strict_types=1);

namespace App\Request;

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
//            'id' => 'required',
            'label' => 'required',
            'value' => 'required',
            'son' => 'required',
            'icon' => 'required',
            'status' => 'required',
//            'updated_at' => 'required',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
//            'id:required' => '请输入id！',
            'label:required' => '请输入名称！',
            'value:required' => '请输入标识！',
            'son:required' => '请输入父菜单！',
            'icon:required' => '请输入icon！',
            'status:required' => '请输入状态！',
//            'updated_at:required' => '请输入更新时间！',
        ];
    }
}
