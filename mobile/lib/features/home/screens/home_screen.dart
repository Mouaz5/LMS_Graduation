import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/models/user_model.dart';
import '../../../core/services/api_service.dart';
import '../../../widgets/info_card.dart';
import '../../../widgets/role_badge.dart';
import '../../dashboard/widgets/app_drawer.dart';
import '../../schedule/screens/schedule_screen.dart';
import '../bloc/home_bloc.dart';
import '../bloc/home_event.dart';
import '../bloc/home_state.dart';

class HomeScreen extends StatelessWidget {
  final UserModel user;

  const HomeScreen({super.key, required this.user});

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (_) => HomeBloc(apiService: context.read<ApiService>())..add(const HomeLoadRequested()),
      child: _HomeScreenContent(user: user),
    );
  }
}

class _HomeScreenContent extends StatelessWidget {
  final UserModel user;

  const _HomeScreenContent({required this.user});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface2,
      drawer: const AppDrawer(),
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        title: const Text('Home',
            style: TextStyle(color: AppColors.textPrimary, fontSize: 17, fontWeight: FontWeight.w700)),
        leading: Builder(
          builder: (ctx) => IconButton(
            icon: const Icon(Icons.menu, color: AppColors.textSecondary),
            onPressed: () => Scaffold.of(ctx).openDrawer(),
          ),
        ),
      ),
      body: BlocBuilder<HomeBloc, HomeState>(
        builder: (context, state) {
          if (state is HomeLoading) {
            return const Center(child: CircularProgressIndicator(color: AppColors.primary));
          }

          if (state is HomeError) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, size: 48, color: AppColors.textMuted),
                  const SizedBox(height: 12),
                  Text(state.message, style: const TextStyle(color: AppColors.textSecondary)),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () => context.read<HomeBloc>().add(const HomeLoadRequested()),
                    child: const Text('Retry'),
                  ),
                ],
              ),
            );
          }

          final classrooms = state is HomeLoaded ? state.classrooms : [];
          final assignments = state is HomeLoaded ? state.assignments : [];

          return SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Welcome banner
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFF1E3A5F), Color(0xFF2563EB)],
                    ),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('Hello, ${user.name.split(' ').first}!',
                                style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w800)),
                            const SizedBox(height: 4),
                            Text(
                              user.role == 'teacher'
                                  ? 'Ready for today\'s classes?'
                                  : 'Welcome back to SchoolLMS',
                              style: const TextStyle(color: Color(0xFFBFDBFE), fontSize: 13),
                            ),
                            const SizedBox(height: 12),
                            RoleBadge(role: user.role, large: true),
                          ],
                        ),
                      ),
                      const Icon(Icons.school_outlined, color: Color(0x40FFFFFF), size: 64),
                    ],
                  ),
                ),
                const SizedBox(height: 20),

                // My Classes section (for teachers)
                if (user.role == 'teacher') ...[
                  const Text('My Classes',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary)),
                  const SizedBox(height: 12),
                  if (classrooms.isEmpty)
                    Container(
                      padding: const EdgeInsets.all(24),
                      decoration: BoxDecoration(
                        color: AppColors.surface,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: AppColors.border),
                      ),
                      child: const Center(
                        child: Text('No classrooms assigned yet.',
                            style: TextStyle(color: AppColors.textMuted, fontSize: 14)),
                      ),
                    )
                  else
                    ...classrooms.map((classroom) => Padding(
                          padding: const EdgeInsets.only(bottom: 10),
                          child: InfoCard(
                            title: classroom.name,
                            subtitle: '${classroom.gradeName ?? ''} — ${classroom.studentCount} students',
                            icon: Icons.class_outlined,
                            iconColor: Colors.blue,
                            iconBg: Colors.blue.withOpacity(0.1),
                          ),
                        )),

                  const SizedBox(height: 12),
                  // My Schedule card
                  GestureDetector(
                    onTap: () => Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (_) => const ScheduleScreen()),
                    ),
                    child: Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFF4F46E5), Color(0xFF7C3AED)],
                        ),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: const Row(
                        children: [
                          Icon(Icons.calendar_today_outlined,
                              color: Colors.white, size: 22),
                          SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('My Schedule',
                                    style: TextStyle(
                                        color: Colors.white,
                                        fontSize: 15,
                                        fontWeight: FontWeight.w700)),
                                Text('View your weekly timetable',
                                    style: TextStyle(
                                        color: Color(0xFFBFDBFE),
                                        fontSize: 12)),
                              ],
                            ),
                          ),
                          Icon(Icons.chevron_right, color: Colors.white54),
                        ],
                      ),
                    ),
                  ),

                  const SizedBox(height: 20),
                  const Text('My Subject Assignments',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary)),
                  const SizedBox(height: 12),
                  if (assignments.isEmpty)
                    Container(
                      padding: const EdgeInsets.all(24),
                      decoration: BoxDecoration(
                        color: AppColors.surface,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: AppColors.border),
                      ),
                      child: const Center(
                        child: Text('No subject assignments yet.',
                            style: TextStyle(color: AppColors.textMuted, fontSize: 14)),
                      ),
                    )
                  else
                    ...assignments.map((assignment) => Padding(
                          padding: const EdgeInsets.only(bottom: 10),
                          child: InfoCard(
                            title: '${assignment.subjectName ?? 'Subject'} — ${assignment.classroomName ?? 'Class'}',
                            subtitle: '${assignment.gradeName ?? ''} (${assignment.subjectCode ?? ''})',
                            icon: Icons.menu_book_outlined,
                            iconColor: Colors.indigo,
                            iconBg: Colors.indigo.withOpacity(0.1),
                          ),
                        )),
                ],

                // For non-teacher roles, show a generic welcome
                if (user.role != 'teacher') ...[
                  Container(
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      color: AppColors.surface,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: AppColors.border),
                    ),
                    child: const Center(
                      child: Text('Dashboard content coming soon.',
                          style: TextStyle(color: AppColors.textMuted, fontSize: 14)),
                    ),
                  ),
                ],
              ],
            ),
          );
        },
      ),
    );
  }
}
