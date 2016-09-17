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
use Cron\Report\CronReport;
use Cron\Report\JobReport;
use Cron\Resolver\ArrayResolver;
use Cron\Schedule\CrontabSchedule;
use yii\console\Controller;
use yii\log\Logger;

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
     * Log executed commands output
     * @var bool
     */
    public $log = false;
    public $logCategory = 'crontab';

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

        /**
         * @var CronReport $report
         */
        $report = $cron->run();

        if (false !== $this->log) {
            \Yii::getLogger()->log('Crontab executed', Logger::LEVEL_INFO, $this->logCategory);

            foreach ($report->getReports() as $jobReport) {
                /**
                 * @var JobReport $jobReport
                 */
                if (!empty($buffer = $jobReport->getOutput())) {

                    /**
                     * @var ShellJob $job
                     */
                    $job = $jobReport->getJob();
                    $message = "Crontab command [" . $job->getProcess()->getCommandLine() . "] returned output:" . PHP_EOL;
                    $message .= trim(join(PHP_EOL, $buffer));

                    \Yii::getLogger()->log($message, Logger::LEVEL_WARNING, $this->logCategory);
                }
            }
        }
    }

}