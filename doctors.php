<?php include 'header.php'; include 'db.php'; ?>

<div style="max-width: 1000px; margin: 40px auto; padding: 20px;">
    <h2 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 30px;">👨‍⚕️ Our Specialists</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
        <?php
        $result = $conn->query("SELECT d.*, h.name as hosp_name FROM Doctors d LEFT JOIN Hospitals h ON d.hospital_id = h.hospital_id");
        
        while($row = $result->fetch_assoc()) {
            echo "<div style='background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 4px solid #3498db; text-align:center;'>";
            echo "<img src='uploads/" . ($row['profile_pic'] ?? 'default_doc.png') . "' style='width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; border: 3px solid #eee;'>";
            echo "<h3 style='color: #2c3e50; margin-top: 0;'>{$row['name']}</h3>";
            echo "<p style='color: #3498db; font-weight: bold;'>{$row['specialization']}</p>";
            echo "<p style='color: #7f8c8d; font-size:13px;'>" . ($row['hosp_name'] ?? 'Not Assigned') . "</p>";
            echo "<a href='view_doctor.php?id={$row['doctor_id']}' class='btn' style='display: block; margin-top: 20px; font-size: 14px;'>View Full Profile</a>";
            echo "</div>";
        }
        ?>
    </div>
</div>