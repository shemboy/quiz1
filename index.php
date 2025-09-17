<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Quiz</title>
</head>
<body>
    <h1>Secure Quiz</h1>
    <div id="quiz-container">
        <p id="question-text">Loading...</p>
        <div id="choices"></div>
    </div>

    <script>
        let currentQuestionId = null;

        async function getNextQuestion() {
            const response = await fetch('quiz.php?action=getQuestion');
            const data = await response.json();

            if (data.finished) {
                alert('Quiz finished!');
                return;
            }

            currentQuestionId = data.question_id;
            document.getElementById('question-text').innerText = data.question_text;
            document.getElementById('choices').innerHTML = '';

            data.choices.forEach(choice => {
                const button = document.createElement('button');
                button.innerText = choice;
                button.onclick = () => submitAnswer(choice);
                document.getElementById('choices').appendChild(button);
            });
        }

        async function submitAnswer(answer) {
            const response = await fetch('quiz.php?action=submitAnswer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    question_id: currentQuestionId,
                    user_answer: answer
                })
            });
            const data = await response.json();

            if (data.correct) {
                alert('Correct!');
            } else {
                alert('Incorrect!');
            }

            getNextQuestion(); // Load the next question
        }

        // Start the quiz
        getNextQuestion();
    </script>
</body>
</html>