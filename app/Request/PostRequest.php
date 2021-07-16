<?php

declare(strict_types=1);

namespace App\Request;

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
            'id' => '',
            'author' => 'required',
            'title' => 'required',
            'content' => 'required',
            'content_file' => '',
            'excerpt' => 'required',
            'type' => 'required',
            'guid' => '',
            'comment_count' => 'required',
            'status' => 'required',
            'comment_status' => 'required',
            'menu' => 'required',
            'tag' => 'required',
            'download_status' => 'required',
            'download' => 'required',
//            'music' => 'required',
//            'video' => 'required',
            'views' => 'required',
            'header_img' => 'required',
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
            'author:required' => '请输入作者！',
            'title:required' => '请输入标题！',
            'content:required' => '请输入内容！',
            'excerpt:required' => '请输入摘要！',
            'type:required' => '请输入文章类型！',
//            'guid:required' => '请输入链接！',
            'comment_count:required' => '请输入评论数量！',
            'status:required' => '请输入文章状态！',
            'comment_status:required' => '请输入评论状态！',
            'menu:required' => '请输入菜单！',
            'tag:required' => '请输入标签！',
            'download_status:required' => '请输入下载状态！',
            'download:required' => '请输入下载链接！',
//            'music:required' => '请输入音乐链接！',
//            'video:required' => '请输入视频链接！',
            'views:required' => '请输入查看数！',
            'header_img:required' => '请输入头图！',
//            'updated_at:required' => '请输入更新时间！',
        ];
    }
}
