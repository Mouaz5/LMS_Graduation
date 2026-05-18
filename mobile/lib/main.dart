import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'core/constants/app_colors.dart';
import 'core/services/api_service.dart';
import 'core/services/auth_service.dart';
import 'features/auth/bloc/auth_bloc.dart';
import 'features/auth/bloc/auth_event.dart';
import 'features/auth/bloc/auth_state.dart';
import 'features/auth/screens/login_screen.dart';
import 'features/dashboard/screens/dashboard_router.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const SchoolLMSApp());
}

class SchoolLMSApp extends StatelessWidget {
  const SchoolLMSApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiRepositoryProvider(
      providers: [
        RepositoryProvider(create: (_) => ApiService()),
        RepositoryProvider(create: (_) => AuthService()),
      ],
      child: BlocProvider(
        create: (context) => AuthBloc(
          authService: context.read<AuthService>(),
        )..add(const AuthCheckRequested()),
        child: BlocBuilder<AuthBloc, AuthState>(
          builder: (context, state) {
            // Detect locale for RTL
            final locale = WidgetsBinding.instance.platformDispatcher.locale;
            final isArabic = locale.languageCode == 'ar';

            return MaterialApp(
              title: 'SchoolLMS',
              debugShowCheckedModeBanner: false,
              locale: locale,
              // RTL support
              builder: (context, child) {
                return Directionality(
                  textDirection: isArabic ? TextDirection.rtl : TextDirection.ltr,
                  child: child!,
                );
              },
              theme: _buildTheme(),
              home: _buildHome(state),
            );
          },
        ),
      ),
    );
  }

  Widget _buildHome(AuthState state) {
    if (state is AuthAuthenticated) return const DashboardRouter();
    if (state is AuthInitial || state is AuthLoading) return const _SplashScreen();
    return const LoginScreen();
  }

  ThemeData _buildTheme() {
    return ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: AppColors.primary,
        primary: AppColors.primary,
        surface: AppColors.surface,
        secondary: AppColors.accent,
      ),
      fontFamily: 'Roboto',
      scaffoldBackgroundColor: AppColors.surface2,
      appBarTheme: const AppBarTheme(
        backgroundColor: AppColors.surface,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        titleTextStyle: TextStyle(
          color: AppColors.textPrimary,
          fontSize: 17,
          fontWeight: FontWeight.w700,
        ),
        iconTheme: IconThemeData(color: AppColors.textSecondary),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: Colors.white,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          elevation: 0,
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: AppColors.surface2,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.border, width: 1.5),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppColors.primary, width: 1.5),
        ),
      ),
    );
  }
}

class _SplashScreen extends StatelessWidget {
  const _SplashScreen();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.sidebarBg,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [AppColors.primaryLight, AppColors.primary],
                ),
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: AppColors.primary.withOpacity(0.5),
                    blurRadius: 24,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: const Icon(Icons.school_rounded, color: Colors.white, size: 40),
            ),
            const SizedBox(height: 20),
            const Text(
              'SchoolLMS',
              style: TextStyle(
                color: Colors.white,
                fontSize: 28,
                fontWeight: FontWeight.w800,
                letterSpacing: -0.5,
              ),
            ),
            const SizedBox(height: 6),
            const Text(
              'MANAGEMENT SYSTEM',
              style: TextStyle(
                color: AppColors.sidebarText,
                fontSize: 10,
                letterSpacing: 2.5,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(height: 40),
            const CircularProgressIndicator(
              color: AppColors.primaryLight,
              strokeWidth: 2,
            ),
          ],
        ),
      ),
    );
  }
}
