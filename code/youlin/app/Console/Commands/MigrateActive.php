<?php

namespace App\Console\Commands;

use App\Models\DestineActive;
use App\Models\DestineActiveType;
use App\Models\SystemSubject;
use App\Models\SystemSubjectType;
use Illuminate\Console\Command;

class MigrateActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '转移活动表';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->type();
        $this->active();

        return;
    }

    private function active()
    {
        $subjects = SystemSubject::query()->get();

        foreach ($subjects as $subject) {
            $model = new DestineActive([
                'id'      => $subject->id,
                'type_id' => $subject->type_id,
                'content' => $subject->content,
                'url'     => $subject->url,
                'title'   => $subject->title,
            ]);

            $model->save();
        }
    }

    private function type()
    {
        $systemTypes = SystemSubjectType::query()->get();

        foreach ($systemTypes as $systemType) {
            $type = new DestineActiveType([
                'id'             => $systemType->id,
                'title'          => $systemType->title,
                'desc'           => $systemType->desc,
                'background_url' => $systemType->image,
                'is_close'      => $systemType->is_closed,
            ]);

            $type->save();
        }
    }
}
