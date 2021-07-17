<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Model\Attachment;
use App\Model\Setting;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use \League\Flysystem\Filesystem;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use Swoole\Coroutine\Channel;
use function Qiniu\entry;

abstract class AbstractController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @param array $data
     * @param string $message
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function success(array $data = [], string $message = '操作成功'): \Psr\Http\Message\ResponseInterface
    {
        $res = [
            'code' => 200,
            'message' => $message,
            'result' => $data ?: (object)[],
        ];
        return $this->response->json($res);
    }

    /**
     * @param array $data
     * @param string|null $message
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function fail(array $data = [], ?string $message = '操作失败'): \Psr\Http\Message\ResponseInterface
    {
        $res = [
            'code' => 401,
            'message' => $message,
            'result' => $data ?: (object)[],
        ];
        return $this->response->json($res);
    }

    /**
     * @param $password
     * @return string
     */
    public function passwordHash($password): string
    {
        return sha1(md5($password) . md5(env('APP_PASSWORD_SALT', 'pinkacg')));
    }

    /**
     * @param string $title
     * @param string $body
     * @param $email
     * @return mixed
     * @throws Exception
     */
    public function sendMail(string $title, string $body, $email)
    {
        $channel = new Channel();
        go(function () use ($title, $body, $email, $channel) {
            $mail = new PHPMailer; //PHPMailer对象
            $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
            $mail->IsSMTP(); // 设定使用SMTP服务
            $mail->SMTPDebug = 0; // 关闭SMTP调试功能
            $mail->SMTPAuth = true; // 启用 SMTP 验证功能
            $mail->SMTPSecure = env('MAIL_SMTP_ENCRYPTION', 'ssl'); // 使用安全协议
            $mail->Host = env('MAIL_SMTP_HOST', ''); // SMTP 服务器
            $mail->Port = env('MAIL_SMTP_PORT', 465); // SMTP服务器的端口号
            $mail->Username = env('MAIL_SMTP_USERNAME', ''); // SMTP服务器用户名
            $mail->Password = env('MAIL_SMTP_PASSWORD', ''); // SMTP服务器密码
            $mail->SetFrom(env('MAIL_FROM_ADDRESS', ''), env('MAIL_FROM_NAME', '')); // 邮箱，昵称
            $mail->Subject = $title;
            $mail->MsgHTML($body);
            $mail->AddAddress($email); // 收件人
            $result = $mail->Send();
            $channel->push($result);
        });
        return $channel->pop();
    }
}
