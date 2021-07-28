<?php

declare(strict_types=1);

namespace App\Request\admin;

use Hyperf\Validation\Request\FormRequest;

class PostRequest extends FormRequest
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
            'author' => 'required|integer|exists:users,id',
            'title' => 'required|string|min:5|max:100',
            'content' => 'required|string|min:50|max:10000',
            'content_file' => 'array',
            'excerpt' => 'required|string|min:10|max:200',
            'type' => 'required|string|min:2|max:20',
            'guid' => 'url',
            'comment_count' => 'integer',
            'status' => 'string',
            'comment_status' => 'required|boolean',
            'menu' => 'required|array',
            'tag' => 'required|array',
            'download_status' => 'required|boolean',
            'download' => 'array',
            'music' => 'array',
            'video' => 'array',
            'views' => 'integer',
            'header_img' => 'required',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
            'author:required' => '请输入作者！',
            'title:required' => '请输入标题！',
            'content:required' => '请输入内容！',
            'excerpt:required' => '请输入摘要！',
            'type:required' => '请输入文章类型！',
            'comment_count:required' => '请输入评论数量！',
            'status:required' => '请输入文章状态！',
            'comment_status:required' => '请输入评论状态！',
            'menu:required' => '请输入菜单！',
            'tag:required' => '请输入标签！',
            'download_status:required' => '请输入下载状态！',
            'download:required' => '请输入下载链接！',
            'views:required' => '请输入查看数！',
            'header_img:required' => '请输入头图！',
        ];
    }
}
