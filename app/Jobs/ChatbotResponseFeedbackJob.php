<?php

namespace App\Jobs;

use App\Models\ChatbotDatas;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChatbotResponseFeedbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $question;
    private $answer;
    public function __construct($question, $answer)
    {
        $this->question = $question;
        $this->answer = $answer;
    }

    public function handle(): void
    {
        ChatbotDatas::insertOrIgnore([
            'question' => $this->question,
            'answer' => $this->answer,
            'admin' => false,
        ]);
    }
}
