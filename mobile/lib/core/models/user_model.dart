import 'dart:convert';

class UserModel {
  final int id;
  final String name;
  final String email;
  final String role;
  final String? phone;
  final bool isActive;

  const UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    this.phone,
    required this.isActive,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) => UserModel(
        id: json['id'] as int,
        name: json['name'] as String,
        email: json['email'] as String,
        role: json['role'] as String,
        phone: json['phone'] as String?,
        isActive: json['is_active'] as bool? ?? true,
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'email': email,
        'role': role,
        'phone': phone,
        'is_active': isActive,
      };

  String toJsonString() => jsonEncode(toJson());

  factory UserModel.fromJsonString(String s) =>
      UserModel.fromJson(jsonDecode(s) as Map<String, dynamic>);

  String get initials {
    final parts = name.trim().split(' ');
    if (parts.length >= 2) return '${parts[0][0]}${parts[1][0]}'.toUpperCase();
    return parts[0][0].toUpperCase();
  }
}
