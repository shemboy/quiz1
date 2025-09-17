<?php
// This is the server-side code. It handles the quiz logic and is not visible to the user.

header('Content-Type: application/json');

// This is where you store your questions and answers.
// This data is secure because it stays on the server.
$questions = [
    [
        'id' => 1,
        'question_text' => '1. What was the state of the firewall in Windows XP when it was released in 2001?',
        'choices' => ['A. Advanced firewall enabled by default', 'B. No firewall included', 'C. Basic firewall disabled by default', 'D. Firewall with real-time protection'],
        'correct_answer' => 'C. Basic firewall disabled by default'
    ],
    [
        'id' => 2,
        'question_text' => '2. Why did users of Windows XP (2001) need third-party antivirus software?',
        'choices' => ['A. Windows XP lacked built-in antivirus protection', 'B. Windows XP was incompatible with antivirus software', 'C. Windows XP had built-in antivirus'],
        'correct_answer' => 'A. Windows XP lacked built-in antivirus protection'
    ],
    [
        'id' => 3,
        'question_text' => '3. Which security feature was introduced in Windows XP Service Pack 2 (2004)?',
        'choices' => ['A. Security Center', 'B. BitLocker', 'C. User Account Control (UAC)'],
        'correct_answer' => 'A. Security Center'
    ]
    // Add more questions here
];

// Start a session to keep track of answered questions
session_start();

$action = $_GET['action'] ?? '';

// Handle the request from the front-end
switch ($action) {
    case 'getQuestion':
        // Get the list of answered question IDs from the session
        $answered_ids = $_SESSION['answered_ids'] ?? [];

        // Find a question that hasn't been answered yet
        $unanswered_questions = array_filter($questions, function($q) use ($answered_ids) {
            return !in_array($q['id'], $answered_ids);
        });

        if (empty($unanswered_questions)) {
            echo json_encode(['finished' => true]);
            session_destroy(); // End the quiz session
        } else {
            // Get a random unanswered question
            $random_question = $unanswered_questions[array_rand($unanswered_questions)];
            
            // Add this question's ID to the answered list
            $_SESSION['answered_ids'][] = $random_question['id'];

            // Send only the question text and choices back to the browser
            echo json_encode([
                'question_id' => $random_question['id'],
                'question_text' => $random_question['question_text'],
                'choices' => $random_question['choices']
            ]);
        }
        break;

    case 'submitAnswer':
        $data = json_decode(file_get_contents('php://input'), true);
        $question_id = $data['question_id'];
        $user_answer = $data['user_answer'];

        // Find the correct question in our secure array
        $question = null;
        foreach ($questions as $q) {
            if ($q['id'] == $question_id) {
                $question = $q;
                break;
            }
        }
        
        // Check the user's answer against the correct one
        $is_correct = ($question['correct_answer'] === $user_answer);

        echo json_encode(['correct' => $is_correct]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>