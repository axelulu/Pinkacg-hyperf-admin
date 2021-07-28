<?php

declare(strict_types=1);

namespace App\Request\admin;

use Hyperf\Validation\Request\FormRequest;

class AttachmentRequest extends FormRequest
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
            'id' => 'integer',
            'newFile' => 'array',
            'title' => 'string|max:50|min:4',
            'original_name' => 'string|max:50|min:4',
            'filename' => 'string|max:50|min:4',
            'path' => 'string|max:50|min:1',
            'type' => 'string|max:50|min:1',
            'cat' => 'required|string|max:50|min:0|exists:attachment_cats,slug',
            'size' => 'integer',
            'user_id' => 'required|integer',
            'post_id' => 'required|integer',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
            'id:required' => '请输入id！',
            'title:required' => '请输入名称！',
            'original_name:required' => '请输入文件名称！',
            'filename:required' => '请输入文件名称！',
            'path:required' => '请输入文件路径！',
            'type:required' => '请输入文件类型！',
            'cat:required' => '请输入分类！',
            'size:required' => '请输入文件大小！',
            'user_id:required' => '请输入用户id！',
            'post_id:required' => '请输入文章id！',
//            'updated_at:required' => '请输入更新时间！',
        ];
    }
}
