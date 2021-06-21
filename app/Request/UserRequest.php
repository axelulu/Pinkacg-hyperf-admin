<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class UserRequest extends FormRequest
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
            'avatar' => 'required|max:255',
//            'id' => 'required',
            'check' => 'required',
//            'created_at' => 'required',
//            'created_id' => 'required',
            'email' => 'required',
            'ip' => 'required',
            'name' => 'required',
            'desc' => 'required',
            'password' => 'required',
//            'remember_token' => 'required',
            'telephone' => 'required',
            'answertest' => 'required',
//            'updated_at' => 'required',
            'username' => 'required',
            'user_role' => 'required'
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
//            'id.required' => '请输入状态！',
            'check.required' => '请输入状态！',
            'email.required'  => '请输入邮箱！',
            'ip.required'  => '请输入ip！',
            'name.required'  => '请输入昵称！',
            'desc.required'  => '请输入描述！',
            'avatar.required'  => '请输入头像！',
            'password.required'  => '请输入密码！',
            'telephone.required'  => '请输入电话！',
            'answertest.required'  => '请输入答题得分！',
            'username.required'  => '请输入用户名！',
            'user_role.required'  => '请输入角色！',
        ];
    }
}
