<?php
/**
 * FileName: CrontabManager.php
 * ==============================================
 * Copy right 2016-2018
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 *    2021/7/1 2:23 下午
 */
declare (strict_types = 1);

namespace crontab;


class CrontabManager
{
    /**
     * @var Crontab[]
     */
    protected $crontabs = [];

    /**
     * @var Parser
     */
    protected $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function register(Crontab $crontab): bool
    {

        if (! $this->isValidCrontab($crontab)) {
            return false;
        }
        $this->crontabs[$crontab->getName()] = $crontab;
        return true;
    }

    public function parse(): array
    {
        $result = [];
        $crontabs = $this->getCrontabs();
        $last = time();
        foreach ($crontabs ?? [] as $key => $crontab) {
            if (! $crontab instanceof Crontab) {
                unset($this->crontabs[$key]);
                continue;
            }
            $time = $this->parser->parse($crontab->getRule(), $last);
            if ($time) {
                foreach ($time as $t) {
                    $result[] = clone $crontab->setExecuteTime($t);
                }
            }
        }
        return $result;
    }

    public function getCrontabs(): array
    {
        return $this->crontabs;
    }

    private function isValidCrontab(Crontab $crontab): bool
    {
        return $crontab->getName() && $crontab->getRule() && $crontab->getCallback() && $this->parser->isValid($crontab->getRule());
    }
}
