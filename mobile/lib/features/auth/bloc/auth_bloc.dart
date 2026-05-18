import 'package:dio/dio.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/services/auth_service.dart';
import 'auth_event.dart';
import 'auth_state.dart';

class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final AuthService _authService;

  AuthBloc({required AuthService authService})
      : _authService = authService,
        super(const AuthInitial()) {
    on<AuthCheckRequested>(_onCheckRequested);
    on<AuthLoginRequested>(_onLoginRequested);
    on<AuthLogoutRequested>(_onLogoutRequested);
  }

  Future<void> _onCheckRequested(
    AuthCheckRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(const AuthLoading());
    final user = await _authService.getStoredUser();
    final token = await _authService.getStoredToken();
    if (user != null && token != null) {
      final permissions = await _authService.fetchPermissions(user.role).catchError((_) => <String>[]);
      emit(AuthAuthenticated(user: user, token: token, permissions: permissions));
    } else {
      emit(const AuthUnauthenticated());
    }
  }

  Future<void> _onLoginRequested(
    AuthLoginRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(const AuthLoading());
    try {
      final result = await _authService.login(event.email, event.password);
      final permissions = await _authService
          .fetchPermissions(result.user.role)
          .catchError((_) => <String>[]);
      emit(AuthAuthenticated(
        user: result.user,
        token: result.token,
        permissions: permissions,
      ));
    } on DioException catch (e) {
      final message = _parseError(e);
      emit(AuthError(message));
    } catch (e) {
      emit(AuthError(e.toString()));
    }
  }

  Future<void> _onLogoutRequested(
    AuthLogoutRequested event,
    Emitter<AuthState> emit,
  ) async {
    await _authService.logout();
    emit(const AuthUnauthenticated());
  }

  String _parseError(DioException e) {
    if (e.response?.data is Map) {
      final data = e.response!.data as Map<String, dynamic>;
      if (data.containsKey('errors')) {
        final errors = data['errors'] as Map<String, dynamic>;
        return errors.values.first is List
            ? (errors.values.first as List).first.toString()
            : errors.values.first.toString();
      }
      return data['message']?.toString() ?? 'An error occurred';
    }
    if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout) {
      return 'Connection timeout. Is the server running?';
    }
    return 'Network error. Please try again.';
  }
}
