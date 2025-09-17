<?php
// This is the server-side code. It is not visible to the user and handles all security.

header('Content-Type: application/json');

// This is where you store your questions and answers.
// The "id" is crucial for matching questions between the client and server.
$questions = [
    [
        'id' => 1,
        'type' => 'multiple',
        'q' => '1. What was the state of the firewall in Windows XP when it was released in 2001?',
        'choices' => [
            "A. Advanced firewall enabled by default ",
            "B. No firewall included ",
            "C. Basic firewall disabled by default",
            "D. Firewall with real-time protection"
        ],
        'answer' => 'C. Basic firewall disabled by default'
    ],
    [
        'id' => 2,
        'type' => 'fill',
        'q' => 'Fill in the blank: The keyword to declare a constant is ____.',
        'answer' => 'const'
    ],
    [
        'id' => 3,
        'type' => 'truefalse',
        'q' => '18. TPM 2.0 is a requirement for Windows 11 security features.',
        'answer' => 'True' // Note: Use a string for consistency
    ]
    // Add more questions here
];

// Shuffle the questions once and store the shuffled order in the session
// This ensures the order is the same for the entire quiz
session_start();
if (!isset($_SESSION['shuffled_questions'])) {
    $_SESSION['shuffled_questions'] = $questions;
    shuffle($_SESSION['shuffled_questions']);
}

$action = $_GET['action'] ?? '';
$answered_count = $_SESSION['answered_count'] ?? 0;
$total_questions = count($questions);

switch ($action) {
    case 'start':
        // Reset the quiz state in the session
        $_SESSION['answered_count'] = 0;
        $_SESSION['shuffled_questions'] = $questions;
        shuffle($_SESSION['shuffled_questions']);
        echo json_encode(['status' => 'ok']);
        break;

    case 'getQuestion':
        if ($answered_count >= $total_questions) {
            echo json_encode(['finished' => true, 'total_questions' => $total_questions]);
            session_destroy();
            return;
        }

        $current = $_SESSION['shuffled_questions'][$answered_count];
        
        // Prepare data to send to the client. This is the secure part.
        // We only send the question and choices, never the correct answer.
        $question_data = [
            'id' => $current['id'],
            'type' => $current['type'],
            'q' => $current['q'],
            'choices' => $current['choices'] ?? [],
            'total_questions' => $total_questions
        ];
        
        echo json_encode($question_data);
        break;

    case 'submitAnswer':
        $data = json_decode(file_get_contents('php://input'), true);
        $user_answer = $data['user_answer'];
        $question_id = $data['question_id'];
        
        // Find the correct question in our secure, server-side array
        $question = null;
        foreach ($questions as $q) {
            if ($q['id'] == $question_id) {
                $question = $q;
                break;
            }
        }
        
        // The answer validation happens here, securely on the server
        if ($question) {
            $correct = false;
            if ($question['type'] === 'fill') {
                $correct = (strtolower($question['answer']) === strtolower($user_answer));
            } elseif ($question['type'] === 'truefalse') {
                $correct = (strtolower($question['answer']) === strtolower($user_answer));
            } else { // multiple choice
                // For multiple choice, we send the choice text back to be compared
                $correct = ($question['answer'] === $user_answer);
            }
            
            $_SESSION['answered_count'] = $answered_count + 1;

            echo json_encode([
                'correct' => $correct,
                'correct_answer' => $question['answer']
            ]);
        } else {
            echo json_encode(['error' => 'Question not found.']);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
