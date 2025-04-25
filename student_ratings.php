<?php
require_once 'includes/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: intro.html");
    exit;
}
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = $_POST['faculty_id'];
    $comments = $_POST['comments'] ?? '';

    $scores = [
        $_POST['metric_1'] ?? 0,
        $_POST['metric_2'] ?? 0,
        $_POST['metric_3'] ?? 0,
        $_POST['metric_4'] ?? 0,
        $_POST['metric_5'] ?? 0
    ];
    
    $scores = array_map('intval', $scores);
    $average_score = array_sum($scores) / count($scores);

    try {
        $stmt = $pdo->prepare("SELECT Student_score FROM evaluations WHERE faculty_id = ?");
        $stmt->execute([$faculty_id]);
        $existing = $stmt->fetchColumn();

        if ($existing !== false) {
            $new_avg = ($existing + $average_score) / 2;
            $update = $pdo->prepare("UPDATE evaluations SET Student_score = ?, comments = ? WHERE faculty_id = ?");
            $update->execute([$new_avg, $comments, $faculty_id]);
        } else {
            $insert = $pdo->prepare("INSERT INTO evaluations (faculty_id, Student_score, comments) VALUES (?, ?, ?)");
            $insert->execute([$faculty_id, $average_score, $comments]);
        }
        $message = "Evaluation submitted successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
try{
    $stmt = $pdo->prepare("SELECT f_id,Name FROM facultydata");
    $stmt->execute();
    $facultyList=$stmt->fetchAll();
}catch(PDOException $e){

}
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rate Faculty</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold text-center w-full">Rate Faculty</h2>
    <a href="?logout=true" class="absolute top-4 right-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Home</a>
</div>


    <?php if ($message): ?>
    <div class="mb-4 bg-green-100 text-green-700 border border-green-300 p-3 rounded">
        <?= htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <div>
            <label for="faculty_id" class="block text-sm font-medium text-gray-700">Faculty</label>
            <select name="faculty_id" id="faculty_id" required
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Select Faculty</option>
                <?php foreach ($facultyList as $faculty): ?>
                <option value="<?= $faculty['f_id'] ?>">
                    <?= htmlspecialchars($faculty['Name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php
        $metric_names = ['Punctuality', 'Subject Knowledge', 'Communication Skills', 'Student Engagement', 'Fairness in Evaluation'];
        foreach ($metric_names as $i => $name): ?>
        <div>
            <label for="metric_<?= $i+1 ?>" class="block text-sm font-medium text-gray-700"><?= $name ?></label>
            <select name="metric_<?= $i+1 ?>" id="metric_<?= $i+1 ?>" required
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">Select Score</option>
                <?php for ($j = 1; $j <= 5; $j++): ?>
                <option value="<?= $j ?>"><?= $j ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <?php endforeach; ?>

       
        <div>
            <label for="comments" class="block text-sm font-medium text-gray-700">Overall Comments</label>
            <textarea name="comments" id="comments" rows="3"
                      class="mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
        </div>

     
        <div class="flex justify-end">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Submit Evaluation
            </button>
        </div>
    </form>
</div>
</body>
</html>
