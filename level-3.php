<?php
require_once 'vendor/autoload.php';
if (isset($_POST['add'])) {
    $taskName = $_POST['task_name'];
    $taskDate = $_POST['task_date'];
    $taskTime = $_POST['task_time'];
    $taskPriority = $_POST['task_priority'];

    $task = array(
        'name' => $taskName,
        'date' => $taskDate,
        'time' => $taskTime,
        'priority' => $taskPriority
    );

    if (file_exists('tasks.json')) {
        $tasks = json_decode(file_get_contents('tasks.json'), true);
    } else {
        $tasks = array();
    }

    $tasks[] = $task;
    file_put_contents('tasks.json', json_encode($tasks));

    sendEmailNotification($task);

    header('Location: level-3.php');
}

function sendEmailNotification($task) {
    $to = 'robianoor2002@gmail.com';  
    $subject = 'Upcoming Task Notification';
    $message = 'You have an upcoming task: ' . $task['name'] . ' on ' . $task['date'] . ' at ' . $task['time'] . '. Priority: ' . $task['priority'];

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= 'From: <no-reply@example.com>' . "\r\n";
    $headers .= 'Reply-To: no-reply@example.com' . "\r\n";

    $smtpHost = 'TODO';  
    $smtpUsername = 'TODO';  
    $smtpPassword = 'TODO';  
    $smtpPort = 587;

    $transport = (new Swift_SmtpTransport($smtpHost, $smtpPort))
        ->setUsername($smtpUsername)
        ->setPassword($smtpPassword);

    $mailer = new Swift_Mailer($transport);

    $emailMessage = (new Swift_Message($subject))
        ->setFrom(['no-reply@example.com' => 'Task Scheduler'])
        ->setTo([$to])
        ->setBody($message, 'text/html');

    $mailer->send($emailMessage);
}


if (isset($_POST['delete'])) {
    $taskIndex = $_POST['task_index'];

    if (file_exists('tasks.json')) {
        $tasks = json_decode(file_get_contents('tasks.json'), true);
        if (isset($tasks[$taskIndex])) {
            unset($tasks[$taskIndex]);
            $tasks = array_values($tasks); // Reindex array
            file_put_contents('tasks.json', json_encode($tasks));
        }
    }

    header('Location: level-3.php');
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Scheduler</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Task Scheduler</h1>
        <form method="post" action="" class="space-y-4">
            <div>
                <label for="task_name" class="block text-gray-700">Task Name:</label>
                <input type="text" id="task_name" name="task_name" required class="w-full border border-gray-300 p-2 rounded">
            </div>
            <div>
                <label for="task_date" class="block text-gray-700">Task Date:</label>
                <input type="date" id="task_date" name="task_date" required class="w-full border border-gray-300 p-2 rounded">
            </div>
            <div>
                <label for="task_time" class="block text-gray-700">Task Time:</label>
                <input type="time" id="task_time" name="task_time" required class="w-full border border-gray-300 p-2 rounded">
            </div>
            <div>
                <label for="task_priority" class="block text-gray-700">Task Priority:</label>
                <select id="task_priority" name="task_priority" required class="w-full border border-gray-300 p-2 rounded">
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select>
            </div>
            <div>
                <input type="submit" name="add" value="Save Task" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
            </div>
        </form>
        <h2 class="text-xl font-bold mt-6">Scheduled Tasks</h2>
        <?php
        if (file_exists('tasks.json')) {
            $tasks = json_decode(file_get_contents('tasks.json'), true);
            if (!empty($tasks)) {
                echo '<ul class="space-y-2">';
                foreach ($tasks as $index => $task) {
                    echo '<li class="flex justify-between items-center bg-gray-200 p-2 rounded">';
                    echo '<span>' . $task['name'] . ' - ' . $task['date'] . ' ' . $task['time'] . ' - ' . $task['priority'] . '</span>';
                    echo '<form method="post" action="" class="ml-4">';
                    echo '<input type="hidden" name="task_index" value="' . $index . '">';
                    echo '<input type="submit" name="delete" value="Delete" class="bg-red-500 text-white p-1 rounded hover:bg-red-600">';
                    echo '</form>';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="text-gray-500">No tasks scheduled.</p>';
            }
        }
        ?>
    </div>
</body>
</html>
