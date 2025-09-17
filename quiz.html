<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Mixed-Type Timed Quiz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f4;
      color: #333;
      margin: 0;
      padding: 1rem;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    h1 {
      text-align: center;
      color: #2c3e50;
    }
    #welcomeScreen, #quiz, #timer, #scoreboard {
      width: 100%;
      max-width: 600px;
      margin-top: 1rem;
    }
    #quiz, #timer {
      display: none;
    }
    button {
      display: block;
      width: 100%;
      padding: 0.75rem;
      margin: 0.5rem 0;
      font-size: 1rem;
      background: #3498db;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background: #2980b9;
    }
    input[type="text"] {
      width: 100%;
      padding: 0.6rem;
      font-size: 1rem;
      margin: 0.5rem 0;
      box-sizing: border-box;
    }
    .timer, .result, .scoreboard h2 {
      font-weight: bold;
      margin-top: 1rem;
    }
    ul {
      padding-left: 1rem;
    }
    @media (max-width: 480px) {
      button, input[type="text"] {
        font-size: 0.95rem;
        padding: 0.6rem;
      }
      h1 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <h1>🧠 Quiz</h1>

  <div id="welcomeScreen">
    <p>Please enter your name to start the quiz:</p>
    <input type="text" id="nameInput" placeholder="Enter your name" />
    <button id="startBtn">▶️ Start Quiz</button>
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
<script>
    let currentIndex = 0;
    let score = 0;
    let timeLeft = 60;
    let timerInterval;
    let studentName = "";
    let quizStartTime;
    let currentQuestion = null; // Store the current question data

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
        // Send a request to the server to reset the quiz state
        fetch('quiz.php?action=start').then(() => getNextQuestion());
    });

    async function getNextQuestion() {
        // Fetch the next question from the PHP file
        const response = await fetch('quiz.php?action=getQuestion');
        const data = await response.json();

        if (data.finished) {
            endQuiz();
            return;
        }

        currentQuestion = data;
        currentIndex++;
        showQuestion();
    }

    async function submitAnswer(answer) {
        clearInterval(timerInterval);
        
        // Send the user's answer to the PHP file for validation
        const response = await fetch('quiz.php?action=submitAnswer', {
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
                btn.textContent = choice;
                btn.onclick = () => submitAnswer(choice);
                cEl.appendChild(btn);
            });
        } else if (currentQuestion.type === "truefalse") {
            ["True", "False"].forEach((val) => {
                const btn = document.createElement('button');
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
