import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/models/user_model.dart';
import '../../../widgets/role_badge.dart';
import '../../attendance/screens/attendance_screen.dart';
import '../../diagnostic_test/screens/diagnostic_test_screen.dart';
import '../../knowledge_map/screens/knowledge_map_screen.dart';
import '../../results/screens/results_screen.dart';
import '../../schedule/screens/schedule_screen.dart';
import '../widgets/app_drawer.dart';

class StudentDashboard extends StatelessWidget {
  final UserModel user;

  const StudentDashboard({super.key, required this.user});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface2,
      drawer: const AppDrawer(),
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        title: const Text('Student Dashboard',
            style: TextStyle(color: AppColors.textPrimary, fontSize: 17, fontWeight: FontWeight.w700)),
        leading: Builder(
          builder: (ctx) => IconButton(
            icon: const Icon(Icons.menu, color: AppColors.textSecondary),
            onPressed: () => Scaffold.of(ctx).openDrawer(),
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Welcome banner
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(colors: [Color(0xFF064E3B), Color(0xFF059669)]),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Hi, ${user.name.split(' ').first}!',
                            style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w800)),
                        const SizedBox(height: 4),
                        const Text('Keep up the great work!',
                            style: TextStyle(color: Color(0xFFA7F3D0), fontSize: 13)),
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

            const Text('Quick Access', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: AppColors.textPrimary)),
            const SizedBox(height: 12),
            LayoutBuilder(
              builder: (context, constraints) {
                final cards = [
                  _QuickAccessCard(
                    icon: Icons.calendar_today_rounded,
                    label: 'My Schedule',
                    subtitle: 'Weekly timetable',
                    color: const Color(0xFF4F46E5),
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) => const ScheduleScreen())),
                  ),
                  _QuickAccessCard(
                    icon: Icons.check_circle_rounded,
                    label: 'Attendance',
                    subtitle: 'Your attendance record',
                    color: const Color(0xFF059669),
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) => AttendanceScreen(studentId: user.id))),
                  ),
                  _QuickAccessCard(
                    icon: Icons.bar_chart_rounded,
                    label: 'My Results',
                    subtitle: 'Grades & report card',
                    color: const Color(0xFF2563EB),
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) => ResultsScreen(studentId: user.id))),
                  ),
                  _QuickAccessCard(
                    icon: Icons.account_tree_rounded,
                    label: 'Knowledge Map',
                    subtitle: 'Mastery by topic',
                    color: const Color(0xFF7C3AED),
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) => KnowledgeMapScreen(studentId: user.id))),
                  ),
                  _QuickAccessCard(
                    icon: Icons.quiz_rounded,
                    label: 'Diagnostic Test',
                    subtitle: 'Assess your level',
                    color: const Color(0xFFD97706),
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) => DiagnosticTestScreen(studentId: user.id))),
                  ),
                ];

                final columns = constraints.maxWidth >= 900
                    ? 3
                    : constraints.maxWidth >= 520
                        ? 2
                        : 1;

                return GridView.builder(
                  gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: columns,
                    mainAxisSpacing: 12,
                    crossAxisSpacing: 12,
                    mainAxisExtent: 80,
                  ),
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: cards.length,
                  itemBuilder: (context, i) => cards[i],
                );
              },
            ),
          ],
        ),
      ),
    );
  }
}

class _QuickAccessCard extends StatelessWidget {
  final IconData icon;
  final String label;
  final String subtitle;
  final Color color;
  final VoidCallback onTap;

  const _QuickAccessCard({
    required this.icon,
    required this.label,
    required this.subtitle,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.surface,
      borderRadius: BorderRadius.circular(18),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(18),
        splashColor: color.withOpacity(0.08),
        highlightColor: color.withOpacity(0.04),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: AppColors.border),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.04),
                blurRadius: 10,
                offset: const Offset(0, 3),
              ),
            ],
          ),
          child: Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: color.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(13),
                ),
                alignment: Alignment.center,
                child: Icon(icon, color: color, size: 22),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(label,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                            color: AppColors.textPrimary,
                            fontSize: 14.5,
                            fontWeight: FontWeight.w700)),
                    const SizedBox(height: 2),
                    Text(subtitle,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                            color: AppColors.textMuted, fontSize: 11.5)),
                  ],
                ),
              ),
              Icon(Icons.chevron_right_rounded,
                  color: AppColors.textMuted.withOpacity(0.5), size: 20),
            ],
          ),
        ),
      ),
    );
  }
}
