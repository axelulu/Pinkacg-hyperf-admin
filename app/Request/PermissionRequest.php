<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class PermissionRequest extends FormRequest
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
            'name' => 'required',
            'title' => 'required',
            'icon' => 'required',
            'path' => 'required',
            'url' => 'required',
            'status' => 'required',
            'method' => 'required',
//            'p_id' => 'required',
            'is_menu' => 'required',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
//            'id.required' => '请输入id！',
            'name.required' => '请输入名称！',
            'title.required' => '请输入标题！',
            'icon.required'  => '请输入icon！',
            'path.required'  => '请输入路径！',
            'url.required'  => '请输入url！',
            'status.required'  => '请输入状态！',
            'method.required'  => '请输入方法！',
//            'p_id.required'  => '请输入父节点！',
            'is_menu.required'  => '请输入菜单状态！',
//            'updated_at.required'  => '请输入更新时间！',
        ];
    }
}
