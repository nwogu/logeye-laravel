<?php

namespace Nwogu\Logeye\Laravel;

use Monolog\Logger;
use Filebase\Database;
use Illuminate\Support\Carbon;
use Monolog\Formatter\LineFormatter;
use Illuminate\Log\Logger as IlluminateLogger;

class Logeye
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function __invoke(IlluminateLogger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor([$this, 'processLogRecord']);
        }
    }

    public function processLogRecord(array $record): array
    {
        $formatter = tap(new LineFormatter($this->format(), null, true, true), function ($formatter) {
            $formatter->ignoreEmptyContextAndExtra();
            $formatter->allowInlineLineBreaks();
        });

        $logeye_record = $record;

        $logeye_record['formatted'] = $formatter->format($record);

        $this->saveRecord($logeye_record);

        return $record;
    }

    protected function saveRecord($logeye_record)
    {
        $new_log = $this->database->get(uniqid());

        $new_log->status = "unsent";
        $new_log->message = $logeye_record['formatted'];
        $new_log->level = Logger::getLevelName($logeye_record['level']);
        $new_log->channel = $logeye_record['channel'];
        $new_log->date = Carbon::now()->toDateTimeString();

        $new_log->save();
    }

    protected function format()
    {
        return "%message% %context% %extra%\n";
    }
}