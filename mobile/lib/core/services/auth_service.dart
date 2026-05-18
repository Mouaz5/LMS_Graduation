import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../constants/app_constants.dart';
import '../models/user_model.dart';

class AuthService {
  final Dio _dio;
  final FlutterSecureStorage _storage;

  AuthService({Dio? dio, FlutterSecureStorage? storage})
      : _dio = dio ?? _buildDio(),
        _storage = storage ?? const FlutterSecureStorage();

  static Dio _buildDio() {
    final dio = Dio(BaseOptions(
      baseUrl: AppConstants.baseUrl,
      connectTimeout: const Duration(seconds: 15),
      receiveTimeout: const Duration(seconds: 15),
      headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
    ));
    return dio;
  }

  Future<({String token, UserModel user})> login(String email, String password) async {
    final response = await _dio.post('/auth/login', data: {
      'email': email,
      'password': password,
    });

    final token = response.data['token'] as String;
    final user = UserModel.fromJson(response.data['user'] as Map<String, dynamic>);

    await _storage.write(key: AppConstants.tokenKey, value: token);
    await _storage.write(key: AppConstants.userKey, value: user.toJsonString());

    return (token: token, user: user);
  }

  Future<void> logout() async {
    final token = await _storage.read(key: AppConstants.tokenKey);
    if (token != null) {
      try {
        await _dio.post(
          '/auth/logout',
          options: Options(headers: {'Authorization': 'Bearer $token'}),
        );
      } catch (_) {}
    }
    await _storage.deleteAll();
  }

  Future<UserModel?> getStoredUser() async {
    final raw = await _storage.read(key: AppConstants.userKey);
    if (raw == null) return null;
    return UserModel.fromJsonString(raw);
  }

  Future<String?> getStoredToken() => _storage.read(key: AppConstants.tokenKey);

  Future<bool> isLoggedIn() async {
    final token = await _storage.read(key: AppConstants.tokenKey);
    return token != null && token.isNotEmpty;
  }

  Future<List<String>> fetchPermissions(String role) async {
    final token = await _storage.read(key: AppConstants.tokenKey);
    final response = await _dio.get(
      '/roles',
      options: Options(headers: {'Authorization': 'Bearer $token'}),
    );
    final data = response.data as Map<String, dynamic>;
    return List<String>.from(data[role] as List? ?? []);
  }
}
