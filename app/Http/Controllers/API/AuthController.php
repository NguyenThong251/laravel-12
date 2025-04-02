<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Imports\HotelsImport;
use App\Imports\UsersImport;
use App\Jobs\SendVerificationEmail;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{


    public function showImportForm()
    {
        return view('import');
    }
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'fullname' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8|confirmed',
    //     ]);

    //     $verificationToken = Str::random(40);

    //     $user = User::create([
    //         'fullname' => $request->fullname,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'verification_token' => $verificationToken,
    //         'role' => 'user'
    //     ]);
    //     return $user;

    //     try {
    //         $this->sendVerificationEmail($user);
    //         // SendVerificationEmail::dispatch($user);
    //         Log::info('Email verification sent successfully to: ' . $user->email);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to send verification email to ' . $user->email . ': ' . $e->getMessage());
    //         // Trả về response lỗi nếu cần, hoặc tiếp tục với đăng ký thành công
    //         return response()->json(['message' => 'Đăng ký thất bại do không gửi được email'], 500);
    //     }

    //     return response()->json([
    //         'message' => 'Đăng ký thành công. Vui lòng kiểm tra email để kích hoạt tài khoản.',
    //         'user' => $user,
    //     ], 201);
    // }
    public function register(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $verificationToken = Str::random(40);

        try {
            // Tạo user mới
            $user = User::create([
                'fullname' => $request->fullname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'verification_token' => $verificationToken,
                'role' => 'user',
            ]);

            // Gửi email xác thực (sử dụng job hoặc trực tiếp)
            // $this->sendVerificationEmail($user);
            SendVerificationEmail::dispatch($user);
            // Hoặc sử dụng queue: SendVerificationEmail::dispatch($user);

            Log::info('Email verification sent successfully to: ' . $user->email);

            return response()->json([
                'message' => 'Đăng ký thành công. Vui lòng kiểm tra email để kích hoạt tài khoản.',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to register user or send verification email to ' . ($request->email ?? 'unknown') . ': ' . $e->getMessage());

            // Nếu đã tạo user nhưng gửi email thất bại, vẫn trả về thông báo
            return response()->json([
                'message' => 'Đăng ký thất bại do không gửi được email. Vui lòng liên hệ hỗ trợ.',
            ], 500);
        }
    }
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = User::where('verification_token', $request->token)->first();

        if (!$user) {
            return response()->json(['message' => 'Mã xác nhận không hợp lệ'], 400);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Tài khoản đã được kích hoạt'], 400);
        }

        $user->update([
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);

        return response()->json(['message' => 'Tài khoản đã được kích hoạt thành công']);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        try {
            // Kiểm tra thông tin đăng nhập và tạo token
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Thông tin đăng nhập không chính xác'], 401);
            }

            // Kiểm tra email đã được xác nhận chưa
            $user = JWTAuth::user();
            if (!$user->email_verified_at) {
                return response()->json(['message' => 'Vui lòng xác nhận email trước khi đăng nhập'], 403);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Không thể tạo token'], 500);
        }

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60, // Thời gian hết hạn (giây)
            'user' => $user,
        ]);
    }
    // private function sendVerificationEmail($user)
    // {
    //     $verificationUrl = url('/api/auth/verify?token=' . $user->verification_token);

    //     Log::info('Preparing to send verification email to: ' . $user->email);
    //     Log::info('Verification URL: ' . $verificationUrl);
    //     Log::info('Mail configuration: ' . json_encode(config('mail')));

    //     Mail::raw("Chào {$user->fullname},\n\nVui lòng nhấp vào liên kết sau để kích hoạt tài khoản:\n{$verificationUrl}\n\nTrân trọng,\n)}", function ($message) use ($user) {
    //         $message->to($user->email)
    //             ->subject('Xác nhận email đăng ký');
    //     });
    // }


    public function importExcel(Request $request)
    {
        Log::info('Import Excel request received: ' . json_encode($request->all()));
        Log::info('Files received: ' . json_encode($request->file()));

        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        try {
            $file = $request->file('file');
            if (!$file) {
                throw new \Exception('No file uploaded');
            }
            $import = new UsersImport();
            Excel::import($import, $file);

            $users = User::whereNull('email_verified_at')->get();

            return response()->json([
                'message' => 'Import Excel thành công. Email xác thực đã được gửi.',
                'imported_count' => $users->count(),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Import Excel failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lỗi khi import Excel: ' . $e->getMessage(),
            ], 500);
        }
    }

    // import hotel 
    public function importHotels(Request $request)
    {
        Log::info('Import Hotels request received: ' . json_encode($request->all()));
        Log::info('Files received: ' . json_encode($request->file()));

        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        try {
            $file = $request->file('file');
            if (!$file) {
                throw new \Exception('No file uploaded');
            }

            Excel::import(new HotelsImport(), $file);

            return response()->json([
                'message' => 'Import khách sạn thành công.',
                'imported_count' => Hotel::count(), // Đếm số khách sạn sau khi import
            ], 200);
        } catch (\Exception $e) {
            Log::error('Import Hotels failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lỗi khi import khách sạn: ' . $e->getMessage(),
            ], 500);
        }
    }
}
