<?php

namespace Database\Factories;

use App\Models\ChatbotDatas;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ChatbotDatasFactory extends Factory
{
    protected $model = ChatbotDatas::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'question' => $this->faker->word(),
            'answer' => $this->faker->word(),
        ];
    }
}
