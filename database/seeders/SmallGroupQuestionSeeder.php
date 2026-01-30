<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmallGroupQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'question_sw' => 'Mara ngapi umetembelea kwa kusudi la injili?',
                'question_en' => 'How many evangelism visits did you make?',
                'response_type' => 'number',
                'category' => 'evangelism',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question_sw' => 'Mara ngapi umetoa msaada kwa jamii?',
                'question_en' => 'How many times did you help the community?',
                'response_type' => 'number',
                'category' => 'community_service',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question_sw' => 'Chakula uliyotoa (Thamani kwa TSh)',
                'question_en' => 'Food given (Value in TSh)',
                'response_type' => 'amount',
                'category' => 'community_service',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question_sw' => 'Fedha uliyotoa (TSh)',
                'question_en' => 'Money given (TSh)',
                'response_type' => 'amount',
                'category' => 'community_service',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question_sw' => 'Ulisoma lesson kwa mpango?',
                'question_en' => 'Did you read the lesson according to plan?',
                'response_type' => 'yes_no',
                'category' => 'bible_study',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'question_sw' => 'Ulisoma lesson japo si kwa mpango?',
                'question_en' => 'Did you read the lesson not according to plan?',
                'response_type' => 'yes_no',
                'category' => 'bible_study',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'question_sw' => 'Ulisoma Biblia kwa mpango?',
                'question_en' => 'Did you read the Bible according to plan?',
                'response_type' => 'yes_no',
                'category' => 'bible_study',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'question_sw' => 'Ulisoma Biblia japo si kwa mpango?',
                'question_en' => 'Did you read the Bible not according to plan?',
                'response_type' => 'yes_no',
                'category' => 'bible_study',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($questions as $question) {
            DB::table('small_group_questions')->insert([
                ...$question,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
