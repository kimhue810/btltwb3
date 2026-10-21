<?php
// Khởi tạo biến để giữ lại dữ liệu đã nhập
$name = "";
$attendance = "";
$midterm = "";
$final = "";
$errors = [];
$resultData = null;

// 1. Hàm kiểm tra điểm hợp lệ (nằm trong khoảng 0 - 10)
function isValidScore($score) {
    return is_numeric($score) && $score >= 0 && $score <= 10;
}

// 2. Hàm tính điểm tổng kết: 10% chuyên cần, 30% giữa kỳ, 60% cuối kỳ
function finalScore($att, $mid, $fin) {
    return ($att * 0.1) + ($mid * 0.3) + ($fin * 0.6);
}

// Hàm xếp loại học lực
function getClassification($score) {
    if ($score >= 8.0) return "Giỏi";
    if ($score >= 6.5) return "Khá";
    if ($score >= 5.0) return "Trung bình";
    return "Chưa đạt";
}

// 3. Xử lý khi người dùng nhấn nút gửi form (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $attendance = $_POST['attendance'] ?? '';
    $midterm = $_POST['midterm'] ?? '';
    $final = $_POST['final'] ?? '';

    // Kiểm tra dữ liệu đầu vào (Validation)
    if (empty($name)) {
        $errors[] = "Vui lòng nhập họ tên sinh viên.";
    }
    if (!isValidScore($attendance)) {
        $errors[] = "Điểm chuyên cần phải là số từ 0 đến 10.";
    }
    if (!isValidScore($midterm)) {
        $errors[] = "Điểm giữa kỳ phải là số từ 0 đến 10.";
    }
    if (!isValidScore($final)) {
        $errors[] = "Điểm cuối kỳ phải là số từ 0 đến 10.";
    }

    // Nếu không có lỗi thì tiến hành tính toán
    if (empty($errors)) {
        $total = finalScore((float)$attendance, (float)$midterm, (float)$final);
        $rank = getClassification($total);
        $resultData = [
            'name' => $name,
            'total' => round($total, 2),
            'rank' => $rank
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Máy tính điểm học phần</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f9fafb; }
        .container { max-width: 480px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #16a34a; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #16a34a; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        button:hover { background-color: #15803d; }
        .error-box { background-color: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 4px; margin-bottom: 15px; }
        .result-box { background-color: #dcfce7; color: #166534; padding: 15px; border-radius: 4px; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Máy tính điểm học phần</h2>

    <!-- Hiển thị lỗi nếu có -->
    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form nhập dữ liệu bằng phương thức POST, giữ lại dữ liệu bằng thuộc tính value -->
    <form method="POST" action="">
        <div class="form-group">
            <label>Họ và tên:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Nhập họ và tên...">
        </div>

        <div class="form-group">
            <label>Điểm chuyên cần (10%):</label>
            <input type="text" name="attendance" value="<?php echo htmlspecialchars($attendance); ?>" placeholder="Nhập điểm từ 0 - 10">
        </div>

        <div class="form-group">
            <label>Điểm giữa kỳ (30%):</label>
            <input type="text" name="midterm" value="<?php echo htmlspecialchars($midterm); ?>" placeholder="Nhập điểm từ 0 - 10">
        </div>

        <div class="form-group">
            <label>Điểm cuối kỳ (60%):</label>
            <input type="text" name="final" value="<?php echo htmlspecialchars($final); ?>" placeholder="Nhập điểm từ 0 - 10">
        </div>

        <button type="submit">Tính điểm</button>
    </form>

    <!-- Hiển thị kết quả -->
    <?php if ($resultData): ?>
        <div class="result-box">
            <p><strong>Sinh viên:</strong> <?php echo htmlspecialchars($resultData['name']); ?></p>
            <p><strong>Điểm tổng kết:</strong> <?php echo $resultData['total']; ?></p>
            <p><strong>Xếp loại:</strong> <?php echo $resultData['rank']; ?></p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>