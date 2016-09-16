<?php
/**
 * Created by PhpStorm.
 * User: fuzz
 * Date: 16.09.16
 * Time: 19:24
 */

namespace ladno\yii2cron;


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
        $resolver = new \Cron\Resolver\ArrayResolver();

        foreach ($this->crontab as $cronJob) {
            $job = new \Cron\Job\ShellJob();
            $job->setCommand($cronJob[1]);
            $job->setSchedule(new \Cron\Schedule\CrontabSchedule($cronJob[0]));
            $resolver->addJob($job);
        }

        $cron = new \Cron\Cron();
        $cron->setExecutor(new \Cron\Executor\Executor());
        $cron->setResolver($resolver);

        $cron->run();
    }

}