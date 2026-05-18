import 'package:equatable/equatable.dart';
import '../../../core/models/user_model.dart';

abstract class AuthState extends Equatable {
  const AuthState();

  @override
  List<Object?> get props => [];
}

class AuthInitial extends AuthState {
  const AuthInitial();
}

class AuthLoading extends AuthState {
  const AuthLoading();
}

class AuthAuthenticated extends AuthState {
  final UserModel user;
  final String token;
  final List<String> permissions;

  const AuthAuthenticated({
    required this.user,
    required this.token,
    this.permissions = const [],
  });

  @override
  List<Object?> get props => [user.id, token, permissions];
}

class AuthUnauthenticated extends AuthState {
  const AuthUnauthenticated();
}

class AuthError extends AuthState {
  final String message;

  const AuthError(this.message);

  @override
  List<Object?> get props => [message];
}
