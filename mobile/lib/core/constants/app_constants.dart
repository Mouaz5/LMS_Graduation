import 'package:flutter/foundation.dart';

abstract class AppConstants {
  static String get baseUrl {
    // The Android emulator can't reach the host via localhost; it maps
    // 10.0.2.2 to the host loopback instead. Every other target (desktop,
    // web, iOS simulator) can use localhost directly.
    if (!kIsWeb && defaultTargetPlatform == TargetPlatform.android) {
      return 'http://10.0.2.2:8000/api';
    }
    return 'http://localhost:8000/api';
  }

  static const String tokenKey = 'auth_token';
  static const String userKey = 'auth_user';
}
