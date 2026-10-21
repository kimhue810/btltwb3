<?php
// Khởi tạo biến để giữ lại dữ liệu đã nhập
$name = "";
$email = "";
$subject = "Hỗ trợ kỹ thuật";
$message = "";
$errors = [];
$success = false;
$uploadedAvatar = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = $_POST['subject'] ?? 'Hỗ trợ kỹ thuật';
    $message = trim($_POST['message'] ?? '');

    // 2. Kiểm tra họ tên và nội dung không rỗng (kèm điều kiện 10 - 500 ký tự theo ảnh mẫu)
    if (empty($name)) {
        $errors['name'] = "Vui lòng nhập họ tên.";
    }

    // 3. Kiểm tra email đúng định dạng
    if (empty($email)) {
        $errors['email'] = "Vui lòng nhập email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không đúng định dạng.";
    }

    $msgLength = mb_strlen($message, 'UTF-8');
    if (empty($message) || $msgLength < 10 || $msgLength > 500) {
        $errors['message'] = "Nội dung phải từ 10 đến 500 ký tự.";
    }

    // 1. Kiểm tra trường Upload ảnh đại diện (3 kiểm tra bắt buộc)
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
        // Kiểm tra 1: Đã chọn file hay chưa
        $errors['avatar'] = "Vui lòng chọn ảnh đại diện.";
    } elseif ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        $errors['avatar'] = "Có lỗi xảy ra khi tải file lên.";
    } else {
        $fileTmp = $_FILES['avatar']['tmp_name'];
        $fileName = $_FILES['avatar']['name'];
        $fileSize = $_FILES['avatar']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Kiểm tra 2: Định dạng file (chỉ cho phép jpg, jpeg, png, gif)
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExt, $allowedExts)) {
            $errors['avatar'] = "Chỉ chấp nhận file ảnh định dạng JPG, JPEG, PNG hoặc GIF.";
        }
        // Kiểm tra 3: Kích thước file (tối đa 2MB = 2 * 1024 * 1024 bytes)
        elseif ($fileSize > 2 * 1024 * 1024) {
            $errors['avatar'] = "Kích thước ảnh không được vượt quá 2MB.";
        }
    }

    // 5. Nếu hợp lệ (không có lỗi), lưu ảnh và hiển thị thông báo thành công
    if (empty($errors)) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Tự động tạo thư mục uploads nếu chưa có
        }
        $newFileName = time() . "_" . basename($_FILES['avatar']['name']);
        $targetPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
            $uploadedAvatar = $targetPath;
        }
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Form liên hệ</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f3f4f6; display: flex; justify-content: center; padding: 40px 15px; margin: 0; }
        .card { background: #ffffff; width: 100%; max-width: 480px; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); }
        h2 { text-align: center; color: #1e40af; margin: 0 0 8px 0; font-size: 26px; }
        .subtitle { text-align: center; color: #6b7280; font-size: 14px; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-weight: bold; color: #374151; margin-bottom: 6px; font-size: 14px; }
        input[type="text"], select, textarea, input[type="file"] {
            width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px;
            font-size: 14px; box-sizing: border-box; font-family: inherit;
        }
        input[type="text"]:focus, select:focus, textarea:focus {
            outline: none; border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }
        textarea { height: 110px; resize: vertical; }
        /* Style viền đỏ và nền hồng nhạt khi có lỗi giống hệt hình mẫu */
        .input-error { border-color: #ef4444 !important; background-color: #fef2f2; }
        .error-text { color: #dc2626; font-size: 12.5px; margin-top: 5px; }
        .btn-submit {
            width: 100%; background-color: #1d64b8; color: white; border: none; padding: 12px;
            font-size: 15px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-top: 8px;
        }
        .btn-submit:hover { background-color: #1e40af; }
        .success-box {
            background-color: #dcfce7; border: 1px solid #86efac; color: #166534;
            padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;
        }
        .avatar-preview { margin-top: 10px; max-width: 100px; border-radius: 8px; border: 1px solid #ccc; }
    </style>
</head>
<body>

<div class="card">
    <h2>Liên hệ</h2>
    <p class="subtitle">Vui lòng nhập đầy đủ thông tin bên dưới.</p>

    <!-- 5. Hiển thị thông báo thành công nếu hợp lệ -->
    <?php if ($success): ?>
        <div class="success-box">
            <strong>Gửi liên hệ thành công!</strong><br>
            Cảm ơn <b><?php echo htmlspecialchars($name); ?></b>, chúng tôi đã nhận được thông tin của bạn.
            <?php if ($uploadedAvatar): ?>
                <br><img src="<?php echo htmlspecialchars($uploadedAvatar); ?>" alt="Avatar" class="avatar-preview">
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Lưu ý: Bắt buộc có enctype="multipart/form-data" để upload được file -->
    <form method="POST" action="" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" name="name" 
                   class="<?php echo isset($errors['name']) ? 'input-error' : ''; ?>"
                   value="<?php echo htmlspecialchars($name); ?>" 
                   placeholder="Nhập họ và tên...">
            <?php if (isset($errors['name'])): ?>
                <div class="error-text"><?php echo $errors['name']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" 
                   class="<?php echo isset($errors['email']) ? 'input-error' : ''; ?>"
                   value="<?php echo htmlspecialchars($email); ?>" 
                   placeholder="example@edu.vn">
            <?php if (isset($errors['email'])): ?>
                <div class="error-text"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Chủ đề</label>
            <select name="subject">
                <option value="Hỗ trợ kỹ thuật" <?php echo ($subject === "Hỗ trợ kỹ thuật") ? "selected" : ""; ?>>Hỗ trợ kỹ thuật</option>
                <option value="Tư vấn khóa học" <?php echo ($subject === "Tư vấn khóa học") ? "selected" : ""; ?>>Tư vấn khóa học</option>
                <option value="Góp ý - Khiếu nại" <?php echo ($subject === "Góp ý - Khiếu nại") ? "selected" : ""; ?>>Góp ý - Khiếu nại</option>
                <option value="Khác" <?php echo ($subject === "Khác") ? "selected" : ""; ?>>Khác</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ảnh đại diện (JPG, PNG, GIF - Tối đa 2MB)</label>
            <input type="file" name="avatar" class="<?php echo isset($errors['avatar']) ? 'input-error' : ''; ?>">
            <?php if (isset($errors['avatar'])): ?>
                <div class="error-text"><?php echo $errors['avatar']; ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Nội dung</label>
            <textarea name="message" 
                      class="<?php echo isset($errors['message']) ? 'input-error' : ''; ?>"
                      placeholder="Nhập nội dung liên hệ..."><?php echo htmlspecialchars($message); ?></textarea>
            <?php if (isset($errors['message'])): ?>
                <div class="error-text"><?php echo $errors['message']; ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-submit">Gửi liên hệ</button>
    </form>
</div>

</body>
</html>