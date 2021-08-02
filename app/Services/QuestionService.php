<?php


namespace App\Services;


use App\Exception\RequestException;
use App\Filters\QuestionFilter;
use App\Model\Question;
use App\Model\Setting;
use App\Model\User;
use App\Resource\admin\QuestionResource as AdminQuestionResource;
use App\Resource\home\QuestionResource as HomeQuestionResource;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class QuestionService extends Service
{
    /**
     * @Inject
     * @var QuestionFilter
     */
    protected $questionFilter;

    /**
     * @param $request
     * @param $JWT
     * @return ResponseInterface
     */
    public function question_query($request, $JWT): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $answer = $request->input('answer', 0);
        $pageSize = $request->query('pageSize') ?? 12;

        //获取数据
        try {
            $question = Question::query()
                ->where($this->questionFilter->apply())
                ->orderBy($orderBy, 'asc')
                ->paginate((int)$pageSize, ['*'], 'pageNo');
            $questions = $question->toArray();

            //当前用户得数
            $userId = $JWT->getParserData()['id'];
            $grade = (User::query()->select('answertest')->where('id', $userId)->first())['answertest'];

            // 验证
            $data = AdminQuestionResource::collection($question)->toArray();

            $exceptColumns = \Qiniu\json_decode($request->getAttribute('except_columns'));
            if (is_array($data)) {
                foreach ($data as $kk => $vv) {
                    if (is_array($exceptColumns)) {
                        foreach ($exceptColumns as $k => $v) {
                            unset($data[$kk][$v]);
                        }
                    }
                }
            }
            //前台访问
            if ($answer && $grade === -1) {
                $len = count($data);
                $result = [];
                $num = self::uniqueRand(0, $len - 1, 10);
                //生成随机题库
                for ($i = 0; $i < 10; $i++) {
                    $result[$i]['id'] = $data[$num[$i]]['id'];
                    $result[$i] = $data[$num[$i]];
                }
                $data = $result;
            }
            if ($answer && ($grade >= 0 && $grade < 60)) {
                return $this->fail([
                    'data' => 'noPass',
                ]);
            }
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        return $this->success([
            'pageSize' => $questions['per_page'],
            'pageNo' => $questions['current_page'],
            'totalCount' => $questions['total'],
            'totalPage' => $questions['to'],
            'data' => $data,
        ]);
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function question_create($request): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //创建内容
        try {
            $flag = Question::query()->create($data);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $id
     * @return ResponseInterface
     */
    public function question_update($request, $id): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //更新内容
        try {
            $flag = Question::query()->where('id', $id)->update($data);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $id
     * @return ResponseInterface
     */
    public function question_delete($id): ResponseInterface
    {
        //删除内容
        try {
            $flag = Question::query()->where('id', $id)->delete();
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $JWT
     * @return ResponseInterface
     */
    public function question_submit($request, $JWT): ResponseInterface
    {
        $userId = $JWT->getParserData()['id'];
        $data = $request->all();
        $len = count($data);
        $gradeItem = 0;

        //处理数据
        foreach ($data as $k => $v) {
            $answer = Question::query()->select('answer')->where('id', $v['id'])->first()->toArray()['answer'];
            if ($answer === $v['result']) {
                $gradeItem++;
            }
        }
        $grade = (100 / $len) * $gradeItem;

        //更新用户分数
        User::query()->where('id', $userId)->update([
            'answertest' => $grade
        ]);
        if ($grade >= 60) {
            //获取角色id
            $user_role = Setting::query()->select('value')->where('name', 'site_meta')->first()->toArray();
            $user_role = \Qiniu\json_decode($user_role['value'])->question_role;
            //赋予角色
            if (!self::setUserRole($userId, $user_role)) {
                return $this->fail([], '赋予角色失败');
            }
            return $this->success(['grade' => $grade]);
        }

        //返回结果
        return $this->fail([
            'grade' => $grade,
            'data' => 'noPass'
        ]);
    }
}