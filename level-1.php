<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $taskName = $_POST['task_name'];
    $taskDate = $_POST['task_date'];
    $taskTime = $_POST['task_time'];

    $task = array(
        'name' => $taskName,
        'date' => $taskDate,
        'time' => $taskTime
    );

    if (file_exists('tasks.json')) {
        $tasks = json_decode(file_get_contents('tasks.json'), true);
    } else {
        $tasks = array();
    }

    $tasks[] = $task;
    file_put_contents('tasks.json', json_encode($tasks));

    header('Location: level-1.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Scheduler</title>
</head>
<body>
    <h1>Task Scheduler</h1>
    <form method="post" action="">
        <label for="task_name">Task Name:</label>
        <input type="text" id="task_name" name="task_name" required><br><br>
        <label for="task_date">Task Date:</label>
        <input type="date" id="task_date" name="task_date" required><br><br>
        <label for="task_time">Task Time:</label>
        <input type="time" id="task_time" name="task_time" required><br><br>
        <input type="submit" value="Save Task">
    </form>
    <h2>Scheduled Tasks</h2>
    <?php
    if (file_exists('tasks.json')) {
        $tasks = json_decode(file_get_contents('tasks.json'), true);
        if (!empty($tasks)) {
            echo '<ul>';
            foreach ($tasks as $task) {
                echo '<li>' . $task['name'] . ' - ' . $task['date'] . ' ' . $task['time'] . '</li>';
            }
            echo '</ul>';
        } else {
            echo 'No tasks scheduled.';
        }
    }
    ?>
</body>
</html>
