<?php
/**
 * Created by PhpStorm.
 * User: fuzz
 * Date: 16.09.16
 * Time: 19:24
 */

namespace ladno\yii2cron;


use Cron\Cron;
use Cron\Executor\Executor;
use Cron\Resolver\ArrayResolver;
use Cron\Schedule\CrontabSchedule;
use yii\console\Controller;

/**
 * Allows to define cronjobs in application config and execute the
 * @package ladno\yii2cron
 */
class CronController extends Controller
{
    /**
     * Crontab formatted arrays like following:
     * ['* * * * *', 'ls -la ']
     *
     * @var array
     */
    public $crontab = [];

    /**
     * Reads crontab config and executes jobs. Should be started in system crontab every minute
     */
    public function actionIndex()
    {
        $resolver = new ArrayResolver();

        foreach ($this->crontab as $cronJob) {
            $job = new ShellJob();
            $job->setCommand($cronJob[1]);
            $job->setSchedule(new CrontabSchedule($cronJob[0]));
            $resolver->addJob($job);
        }

        $cron = new Cron();
        $cron->setExecutor(new Executor());
        $cron->setResolver($resolver);

        $cron->run();
    }

}