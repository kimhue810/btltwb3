<?php
// 1. Tạo mảng $students gồm 3 sinh viên với name, midterm, final
$students = [
    ["name" => "Nguyễn Văn An", "midterm" => 7.5, "final" => 8.0],
    ["name" => "Trần Thị Bình", "midterm" => 4.0, "final" => 5.0], // Thử nghiệm ký tự đặc biệt cho htmlspecialchars
    ["name" => "Lê Hoàng Cường", "midterm" => 9.0, "final" => 9.5]
];

// 2. Viết hàm calculateAverage() tính điểm trung bình
function calculateAverage($midterm, $final) {
    return ($midterm + $final) / 2;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng điểm 3 sinh viên</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 650px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #16a34a; color: white; }
        td:first-child { text-align: left; }
        .pass { color: green; font-weight: bold; }
        .fail { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Bảng điểm sinh viên</h2>

    <table>
        <thead>
            <tr>
                <th>Họ và tên</th>
                <th>Điểm giữa kỳ</th>
                <th>Điểm cuối kỳ</th>
                <th>Điểm trung bình</th>
                <th>Kết quả</th>
            </tr>
        </thead>
        <tbody>
            <!-- 3. Dùng foreach để in bảng HTML -->
            <?php foreach ($students as $student): ?>
                <?php 
                    $avg = calculateAverage($student['midterm'], $student['final']); 
                    // 4. Thêm cột kết quả: Đạt nếu điểm trung bình >= 5
                    $result = ($avg >= 5) ? "Đạt" : "Chưa đạt";
                    $class = ($avg >= 5) ? "pass" : "fail";
                ?>
                <tr>
                    <!-- 5. Mã hóa tên sinh viên bằng htmlspecialchars() trước khi in -->
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                    <td><?php echo $student['midterm']; ?></td>
                    <td><?php echo $student['final']; ?></td>
                    <td><?php echo number_format($avg, 2); ?></td>
                    <td class="<?php echo $class; ?>"><?php echo $result; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>