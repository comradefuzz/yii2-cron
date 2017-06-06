<?php
/**
 * Created by PhpStorm.
 * User: fuzz
 * Date: 17.09.16
 * Time: 15:43
 */

namespace comradefuzz\yii2cron;


use Cron\Report\JobReport;
use Symfony\Component\Process\Process;

class ShellJob extends \Cron\Job\ShellJob
{
    public function run(JobReport $report)
    {
        $this->report = $report;
        $report->setStartTime(microtime(true));
        $process = $this->getProcess();
        $process->start(function ($type, $buffer) use ($report) {
            if (Process::ERR === $type) {
                $report->addError($buffer);
            } else {
                $report->addOutput($buffer);
            }
        });
        /**
         * Workaround for child processes killed at the end of the parent
         * @url https://github.com/Cron/Cron/issues/42
         */
        $process->wait();
    }
}
