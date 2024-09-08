<?php

namespace App\Livewire;

use App\Jobs\ChatbotResponseFeedbackJob;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Phpml\ModelManager;

class Chatbot extends Component
{
    public ?array $conversation = [];
    public array $liked = [];
    #[Validate('required|string')]
    public $message;

    public function render()
    {
        return view('livewire.chatbot')
            ->layoutData(['attributes' => 'x-data="{ chatContainer: null }"']);
    }

    public function save()
    {
        $this->validate();
        $arr_data = [
            'sender' => auth()->id(),
            'question' => $this->message,
        ];
        $this->conversation[] = $arr_data;

        $this->dispatch('scroll-to-bottom');
        $this->botman();
    }

    private function botman()
    {
        $model = new ModelManager();
        if (auth()->user()->is_admin || auth()->user()->role === 'pengajar') {
            $trainedModel = $model->restoreFromFile(storage_path('app/adminBotModel'));
        } else {
            $trainedModel = $model->restoreFromFile(storage_path('app/dosenBotModel'));
        }
        $answer = $trainedModel->predict([$this->message]);
        // Check if the last two bot answers are the same as the current answer
        $previousAnswers = array_slice(array_filter($this->conversation, function ($item) {
            return isset($item['sender']) && $item['sender'] === 1;
        }), -2);


        $arr_data = [
            'sender' => 1,
            'answer' => $answer[0],
        ];
        $currentAnswer = $answer[0];
        $this->conversation[] = $arr_data;
        if (count($previousAnswers) == 2 && $previousAnswers[0]['answer'] === $currentAnswer && $previousAnswers[1]['answer'] === $currentAnswer) {
            $currentAnswer = "Maaf, saya tidak mengerti pertanyaan Anda.";
            $this->conversation[] = [
                'sender' => 1,
                'answer' => $currentAnswer,
            ];
        }
        $this->dispatch('scroll-to-bottom');
    }

    public function like(int $key)
    {
        $answer = $this->conversation[$key]['answer'];
        $question = $this->conversation[$key - 1]['question'];
        ChatbotResponseFeedbackJob::dispatch($question, $answer);
        $this->liked[$key] = true;
    }
}
