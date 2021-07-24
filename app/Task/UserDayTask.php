<?php

namespace App\Task;

use App\Model\User;
use Hyperf\Crontab\Annotation\Crontab;

/**
 * @Crontab(name="UserDay", rule="00 24 * * *", callback="resetUserAnswerTest", memo="这是一个用户每日定时任务")
 */
class UserDayTask
{
    /**
     * 每日零点自动重置用户答题
     */
    public function resetUserAnswerTest()
    {
        User::query()->where('answertest', '<', 60)->update([
            'answertest' => -1
        ]);
    }
}
