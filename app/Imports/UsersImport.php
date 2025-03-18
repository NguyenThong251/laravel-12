<?php


namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Kiểm tra nếu email đã tồn tại
        if (User::where('email', $row['email'])->exists()) {
            return null; // Bỏ qua nếu email đã tồn tại
        }

        return new User([
            'fullname' => $row['fullname'] ?? null,
            'email' => $row['email'] ?? null,
            'password' => Hash::make($row['password'] ?? Str::random(8)), // Tạo mật khẩu ngẫu nhiên nếu không có
            'verification_token' => Str::random(40),
            'email_verified_at' => null,
        ]);
    }

    /**
     * Định nghĩa các quy tắc validation cho mỗi dòng
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'fullname' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Xử lý lỗi validation
     */
    public function onError(\Throwable $e)
    {
        Log::error('Import error: ' . $e->getMessage());
    }
}
