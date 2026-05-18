import 'package:flutter/material.dart';

abstract class AppColors {
  static const primary = Color(0xFF4F46E5);
  static const primaryDark = Color(0xFF3730A3);
  static const primaryLight = Color(0xFF818CF8);
  static const accent = Color(0xFF10B981);

  static const sidebarBg = Color(0xFF1E1B4B);
  static const sidebarText = Color(0xFFC7D2FE);

  static const surface = Color(0xFFFFFFFF);
  static const surface2 = Color(0xFFF8FAFC);
  static const border = Color(0xFFE2E8F0);

  static const textPrimary = Color(0xFF0F172A);
  static const textSecondary = Color(0xFF64748B);
  static const textMuted = Color(0xFF94A3B8);

  static const error = Color(0xFFEF4444);
  static const success = Color(0xFF10B981);
  static const warning = Color(0xFFF59E0B);

  static const roleAdmin = Color(0xFF4338CA);
  static const roleTeacher = Color(0xFF1D4ED8);
  static const roleStudent = Color(0xFF059669);
  static const roleParent = Color(0xFF7C3AED);

  static Color roleColor(String role) => switch (role) {
        'admin' => roleAdmin,
        'teacher' => roleTeacher,
        'student' => roleStudent,
        'parent' => roleParent,
        _ => primary,
      };

  static Color roleBg(String role) => switch (role) {
        'admin' => const Color(0xFFEEF2FF),
        'teacher' => const Color(0xFFEFF6FF),
        'student' => const Color(0xFFECFDF5),
        'parent' => const Color(0xFFFAF5FF),
        _ => const Color(0xFFEEF2FF),
      };
}
