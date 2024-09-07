<?php

namespace App\Console\Commands;

use App\Models\ChatbotDatas;
use Illuminate\Console\Command;
use Phpml\Classification\NaiveBayes;
use Phpml\Pipeline;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\ModelManager;

class ChatbotCommand extends Command
{
    protected $signature = 'chatbot';
    protected $description = 'Command description';

    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const ADMININVALID = 2;
    public const DOSENINVALID = 3;

    public function handle(): int
    {
        $this->info('Training chatbot model...');
        try {
            $adminStatus = $this->trainModel(true, 'adminBotModel');
            $dosenStatus = $this->trainModel(false, 'dosenBotModel');

            if ($adminStatus === self::SUCCESS && $dosenStatus === self::SUCCESS) {
                $this->info('Chatbot model trained successfully!');
                return self::SUCCESS;
            }

            return $adminStatus !== self::SUCCESS ? $adminStatus : $dosenStatus;
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function trainModel(bool $admin, string $modelName): int
    {
        if ($admin) {
            $data = ChatbotDatas::all();
        } else {
            $data = ChatbotDatas::where('admin', false)->get();
        }
        $questions = array_map('strtolower', $data->pluck('question')->toArray());
        $answers = array_map('strtolower', $data->pluck('answer')->toArray());

        if (!empty($questions) && !empty($answers)) {
            $this->trainAndSaveModel($questions, $answers, $modelName);
            return self::SUCCESS;
        }

        $this->warn('No ' . ($admin ? 'admin' : 'dosen') . ' data available for training.');
        return $admin ? self::ADMININVALID : self::DOSENINVALID;
    }

    private function trainAndSaveModel(array $questions, array $answers, string $modelName): void
    {
        $pipeline = new Pipeline([
            new TokenCountVectorizer(new WordTokenizer()),
            new TfIdfTransformer()
        ], new NaiveBayes());

        $pipeline->train($questions, $answers);
        (new ModelManager())->saveToFile($pipeline, storage_path('app/' . $modelName));
    }
}
