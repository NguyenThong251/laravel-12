{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h1>Import Excel File</h1>
    <div class="form-group">
        <label for="file">Chọn file Excel (.xlsx, .xls)</label>
        <input type="file" id="file" name="file" accept=".xlsx,.xls" required>
    </div>
    <button onclick="importFile()">Import</button>
    <div id="message" class="message" style="display: none;"></div>

    <script>
        async function importFile() {
            const fileInput = document.getElementById('file');
            const messageDiv = document.getElementById('message');
    
            // Kiểm tra nếu chưa chọn file
            if (!fileInput.files.length) {
                showMessage('Vui lòng chọn file Excel.', 'error');
                return;
            }
    
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            console.log('FormData:', formData); // Debug để kiểm tra
    
            try {
                const response = await fetch('/api/auth/import', {
                    method: 'POST',
                    body: formData,
                });
             
                const result = await response.json();
    
                if (response.ok) {
                    showMessage(result.message, 'success');
                } else {
                    showMessage(result.message || 'Đã có lỗi xảy ra.', 'error');
                }
            } catch (error) {
                showMessage('Lỗi kết nối: ' + error.message, 'error');
            }
        }
    
        function showMessage(message, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = message;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
        }
    </script>
</body>
</html> --}}


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Hotels Excel File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h1>Import Hotels Excel File</h1>
    <div class="form-group">
        <label for="file">Chọn file Excel (.xlsx, .xls)</label>
        <input type="file" id="file" name="file" accept=".xlsx,.xls" required>
    </div>
    <button onclick="importFile()">Import</button>
    <div id="message" class="message" style="display: none;"></div>

    <script>
        async function importFile() {
            const fileInput = document.getElementById('file');
            const messageDiv = document.getElementById('message');

            // Kiểm tra nếu chưa chọn file
            if (!fileInput.files.length) {
                showMessage('Vui lòng chọn file Excel.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            try {
                const response = await fetch('/api/auth/import-hotels', {
                    method: 'POST',
                    body: formData,
                });

                const result = await response.json();

                if (response.ok) {
                    showMessage(result.message + ' Số khách sạn: ' + result.imported_count, 'success');
                } else {
                    showMessage(result.message || 'Đã có lỗi xảy ra.', 'error');
                }
            } catch (error) {
                showMessage('Lỗi kết nối: ' + error.message, 'error');
            }
        }

        function showMessage(message, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = message;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
        }
    </script>
</body>
</html>