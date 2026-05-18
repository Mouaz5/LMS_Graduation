import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../features/auth/bloc/auth_bloc.dart';
import '../../../features/auth/bloc/auth_state.dart';
import '../../../features/home/screens/home_screen.dart';
import 'admin_dashboard.dart';
import 'student_dashboard.dart';
import 'parent_dashboard.dart';

class DashboardRouter extends StatelessWidget {
  const DashboardRouter({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<AuthBloc, AuthState>(
      builder: (context, state) {
        if (state is! AuthAuthenticated) {
          return const Scaffold(
            body: Center(
              child: CircularProgressIndicator(color: AppColors.primary),
            ),
          );
        }

        final user = state.user;

        return switch (user.role) {
          'admin' => AdminDashboard(user: user),
          'teacher' => HomeScreen(user: user),
          'student' => StudentDashboard(user: user),
          'parent' => ParentDashboard(user: user),
          _ => AdminDashboard(user: user),
        };
      },
    );
  }
}
