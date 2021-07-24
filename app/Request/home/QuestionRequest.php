<?php

declare(strict_types=1);

namespace App\Request\admin;

use Hyperf\Validation\Request\FormRequest;

class QuestionRequest extends FormRequest
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
            'question' => 'required',
            'A' => 'required',
            'B' => 'required',
            'C' => 'required',
            'D' => 'required',
//            'updated_at' => 'required'
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
//            'id:required' => '请输入id！',
            'question:required' => '请输入问题！',
            'A:required' => '请输入A选项！',
            'B:required' => '请输入B选项！',
            'C:required' => '请输入C选项！',
            'D:required' => '请输入D选项！',
//            'updated_at:required' => '请输入更新时间！'
        ];
    }
}
