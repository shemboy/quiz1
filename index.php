<?php
// This is the secure, server-side code. It's not visible to the user's browser.

// The questions and answers are stored here, securely on the server.
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
        'answer' => 'True'
    ]
    // Add more questions here. They will be safe on the server.
];

// Start a session to keep track of answered questions.
session_start();
header('Content-Type: text/html');

// Handle API requests from the JavaScript code
if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    $action = $_GET['action'];
    $answered_count = $_SESSION['answered_count'] ?? 0;
    $total_questions = count($questions);

    switch ($action) {
        case 'start':
            $_SESSION['answered_count'] = 0;
            $_SESSION['shuffled_questions'] = $questions;
            shuffle($_SESSION['shuffled_questions']);
            echo json_encode(['status' => 'ok']);
            exit;

        case 'getQuestion':
            if ($answered_count >= $total_questions) {
                echo json_encode(['finished' => true, 'total_questions' => $total_questions]);
                session_destroy();
                exit;
            }

            $current = $_SESSION['shuffled_questions'][$answered_count];
            $question_data = [
                'id' => $current['id'],
                'type' => $current['type'],
                'q' => $current['q'],
                'choices' => $current['choices'] ?? [],
                'total_questions' => $total_questions
            ];
            echo json_encode($question_data);
            exit;

        case 'submitAnswer':
            $data = json_decode(file_get_contents('php://input'), true);
            $user_answer = $data['user_answer'];
            $question_id = $data['question_id'];
            
            $question = null;
            foreach ($questions as $q) {
                if ($q['id'] == $question_id) {
                    $question = $q;
                    break;
                }
            }

            if ($question) {
                $correct = false;
                if ($question['type'] === 'fill') {
                    $correct = (strtolower($question['answer']) === strtolower($user_answer));
                } elseif ($question['type'] === 'truefalse') {
                    $correct = (strtolower($question['answer']) === strtolower($user_answer));
                } else {
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
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Mixed-Type Timed Quiz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f4f8, #c1d5e0);
            color: #333;
            margin: 0;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .container {
            width: 100%;
            max-width: 600px;
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #welcomeScreen, #quiz, #timer, #scoreboard {
            width: 100%;
        }

        #quiz, #timer {
            display: none;
        }

        p {
            font-size: 1.1rem;
            line-height: 1.6;
        }

        button, input[type="text"] {
            width: 100%;
            padding: 0.9rem 1.2rem;
            margin: 0.6rem 0;
            font-size: 1rem;
            border: 2px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        input[type="text"] {
            border: 2px solid #ddd;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .primary-btn {
            background: #3498db;
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
        }

        .primary-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(52, 152, 219, 0.4);
        }
        
        .answer-btn {
            background: #ecf0f1;
            color: #2c3e50;
            font-weight: 400;
            text-align: left;
            border: 1px solid #bdc3c7;
            padding: 0.9rem 1.2rem;
        }
        
        .answer-btn:hover {
            background: #bdc3c7;
            transform: none;
            box-shadow: none;
        }

        .timer, .result, .scoreboard h2 {
            font-weight: 600;
            margin-top: 1.5rem;
            text-align: center;
        }

        .scoreboard h2 {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-top: 2rem;
            border-bottom: 2px solid #bdc3c7;
            padding-bottom: 0.5rem;
        }
        
        ul {
            list-style-type: none;
            padding: 0;
            width: 100%;
        }

        ul li {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 0.8rem 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }
            .container {
                padding: 1.5rem;
                margin-top: 0;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <h1>🧠 Quiz</h1>

    <div class="container" id="quiz-app">
        <div id="welcomeScreen">
            <p>Please enter your name to start the quiz:</p>
            <input type="text" id="nameInput" placeholder="Enter your Full Name" />
            <button id="startBtn" class="primary-btn">▶️ Start Quiz</button>
        </div>
        
        <div class="timer" id="timer">Time left: 60s</div>
        <div id="quiz">
            <p id="question"></p>
            <div id="choices"></div>
            <div id="result" class="result"></div>
        </div>
        
        <div class="scoreboard" id="scoreboard">
            <h2>📋 Scoreboard</h2>
            <ul id="scoreList"></ul>
        </div>
    </div>

    <script>
        let currentIndex = 0;
        let score = 0;
        let timeLeft = 60;
        let timerInterval;
        let studentName = "";
        let quizStartTime;
        let currentQuestion = null;

        const startBtn = document.getElementById('startBtn');
        const qEl = document.getElementById('question');
        const cEl = document.getElementById('choices');
        const rEl = document.getElementById('result');
        const tEl = document.getElementById('timer');
        const quizEl = document.getElementById('quiz');
        const scoreList = document.getElementById('scoreList');
        const nameInput = document.getElementById('nameInput');
        const welcomeScreen = document.getElementById('welcomeScreen');
        const scoreboard = document.getElementById('scoreboard');

        function loadScoreboard() {
            const scores = JSON.parse(localStorage.getItem('quizScores') || '[]');
            scoreList.innerHTML = scores.map(s => `<li>${s.name} on ${s.date} — ${s.score}/${s.total} - Time: ${s.time}</li>`).join('');
        }

        function saveScore(score, total, time) {
            const scores = JSON.parse(localStorage.getItem('quizScores') || '[]');
            const now = new Date().toLocaleString();
            scores.unshift({ date: now, name: studentName, score, total, time });
            localStorage.setItem('quizScores', JSON.stringify(scores.slice(0, 10)));
        }

        startBtn.addEventListener('click', () => {
            const name = nameInput.value.trim();
            if (name === "") {
                alert("Please enter your name to start.");
                return;
            }
            studentName = name;
            welcomeScreen.style.display = 'none';
            quizEl.style.display = 'block';
            tEl.style.display = 'block';
            currentIndex = 0;
            score = 0;
            quizStartTime = new Date();
            fetch('?action=start').then(() => getNextQuestion());
        });

        async function getNextQuestion() {
            const response = await fetch('?action=getQuestion');
            const data = await response.json();

            if (data.finished) {
                endQuiz();
                return;
            }

            currentQuestion = data;
            showQuestion();
        }

        async function submitAnswer(answer) {
            clearInterval(timerInterval);
            
            const response = await fetch('?action=submitAnswer', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ question_id: currentQuestion.id, user_answer: answer })
            });
            const data = await response.json();

            if (data.correct) {
                score++;
                rEl.textContent = "✅ Correct!";
            } else {
                rEl.textContent = `❌ Incorrect. Correct: ${data.correct_answer}`;
            }
            
            setTimeout(getNextQuestion, 1000);
        }
        
        function showQuestion() {
            timeLeft = 60;
            tEl.textContent = `Time left: ${timeLeft}s`;
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                timeLeft--;
                tEl.textContent = `Time left: ${timeLeft}s`;
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    rEl.textContent = "⏱️ Time's up!";
                    setTimeout(getNextQuestion, 1000);
                }
            }, 1000);

            qEl.textContent = currentQuestion.q;
            cEl.innerHTML = "";
            rEl.textContent = "";

            if (currentQuestion.type === "multiple") {
                currentQuestion.choices.forEach((choice) => {
                    const btn = document.createElement('button');
                    btn.className = "answer-btn";
                    btn.textContent = choice;
                    btn.onclick = () => submitAnswer(choice);
                    cEl.appendChild(btn);
                });
            } else if (currentQuestion.type === "truefalse") {
                ["True", "False"].forEach((val) => {
                    const btn = document.createElement('button');
                    btn.className = "answer-btn";
                    btn.textContent = val;
                    btn.onclick = () => submitAnswer(val);
                    cEl.appendChild(btn);
                });
            } else if (currentQuestion.type === "fill") {
                const input = document.createElement('input');
                input.type = "text";
                input.placeholder = "Type your answer...";
                cEl.appendChild(input);

                const submitBtn = document.createElement('button');
                submitBtn.className = "primary-btn";
                submitBtn.textContent = "Submit";
                submitBtn.onclick = () => submitAnswer(input.value.trim());
                cEl.appendChild(submitBtn);
            }
        }

        function endQuiz() {
            clearInterval(timerInterval);
            const quizEndTime = new Date();
            const totalTimeInMs = quizEndTime - quizStartTime;
            const totalSeconds = Math.floor(totalTimeInMs / 1000);
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            const formattedTime = `${minutes}m ${seconds}s`;

            qEl.textContent = "🎉 Quiz Complete!";
            cEl.innerHTML = "";
            rEl.innerHTML = `Your score: ${score} / ${currentQuestion.total_questions}<br>Total time: ${formattedTime}`;
            tEl.style.display = 'none';
            saveScore(score, currentQuestion.total_questions, formattedTime);
            loadScoreboard();
        }

        loadScoreboard();
    </script>
</body>
</html>

