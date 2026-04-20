<?php 
include 'header.php'; 
include 'db.php'; 
if (!isset($_SESSION['patient_id'])) { header("Location: login.php"); exit(); }

$pid = $_SESSION['patient_id'];
$selected_spec = $_GET['spec'] ?? '';

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $did = $_POST['doctor_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);
    
    $stmt = $conn->prepare("INSERT INTO Reviews (patient_id, doctor_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $pid, $did, $rating, $comment);
    
    if($stmt->execute()){
         $success = "✅ Thank you! Your review has been submitted.";
    }
    $stmt->close();
}
?>

<div class="container">
    <div class="card" style="border-top-color: #f1c40f;">
        <h2>⭐ Review a Doctor</h2>
        <p style="color: #7f8c8d; margin-bottom: 20px;">Find doctors by selecting a specialization to leave your feedback.</p>

        <form method="GET" style="background: #fbfcfc; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #eee;">
            <div class="form-group" style="margin-bottom:0; display: flex; gap: 10px; align-items: flex-end;">
                <div style="flex: 1;">
                    <label>Filter by Specialization</label>
                    <select name="spec" onchange="this.form.submit()">
                        <option value="">-- All Specializations --</option>
                        <?php
                        $specs = $conn->query("SELECT DISTINCT specialization FROM Doctors WHERE specialization IS NOT NULL");
                        while($s = $specs->fetch_assoc()) {
                            $sel = ($selected_spec == $s['specialization']) ? 'selected' : '';
                            echo "<option value='".htmlspecialchars($s['specialization'])."' $sel>".htmlspecialchars($s['specialization'])."</option>";
                        }
                        ?>
                    </select>
                </div>
                <a href="reviews.php" class="btn" style="background: #95a5a6;">Clear</a>
            </div>
        </form>

        <?php if(isset($success)) echo "<p style='color:green; font-weight:bold; margin-bottom:15px;'>$success</p>"; ?>

        <form method="POST">
            <div class="form-group">
                <label>Select Registered Doctor</label>
                <select name="doctor_id" required>
                    <option value="">-- Choose Doctor --</option>
                    <?php
                    $sql = "SELECT doctor_id, name, specialization FROM Doctors";
                    if($selected_spec) {
                        $sql .= " WHERE specialization = '" . $conn->real_escape_string($selected_spec) . "'";
                    }
                    $docs = $conn->query($sql);
                    while($d = $docs->fetch_assoc()) {
                        echo "<option value='{$d['doctor_id']}'>" . htmlspecialchars($d['name']) . " (" . htmlspecialchars($d['specialization']) . ")</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Rating</label>
                    <select name="rating" required>
                        <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                        <option value="4">⭐⭐⭐⭐ (Very Good)</option>
                        <option value="3">⭐⭐⭐ (Good)</option>
                        <option value="2">⭐⭐ (Fair)</option>
                        <option value="1">⭐ (Poor)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Your Feedback</label>
                <textarea name="comment" rows="3" placeholder="How was your experience?" required></textarea>
            </div>

            <button type="submit" name="submit_review" class="btn" style="background: #f1c40f; color: #2c3e50; width: 100%;">Submit Review</button>
        </form>
    </div>

    <h3 style="margin-top: 40px; margin-bottom: 20px;">Recent Patient Experiences</h3>
    <div class="cards-grid">
        <?php
        $revs = $conn->query("SELECT r.*, p.name as p_name, d.name as d_name FROM Reviews r 
                              JOIN Patients p ON r.patient_id = p.patient_id 
                              JOIN Doctors d ON r.doctor_id = d.doctor_id 
                              ORDER BY r.review_id DESC LIMIT 6");
        
        while($r = $revs->fetch_assoc()) {
            echo "<div class='card' style='border-top: 4px solid #f1c40f;'>";
            echo "<div style='color: #f39c12; margin-bottom: 10px;'>" . str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']) . "</div>";
            echo "<p style='font-style: italic;'>\"" . htmlspecialchars($r['comment']) . "\"</p>";
            echo "<p style='font-size: 13px; margin-top: 15px; color: #7f8c8d; border-top: 1px solid #eee; padding-top: 10px;'>";
            echo "<strong>" . htmlspecialchars($r['p_name']) . "</strong> reviewed <strong>" . htmlspecialchars($r['d_name']) . "</strong>";
            echo "</p></div>";
        }
        ?>
    </div>
</div>